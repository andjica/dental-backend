<?php
namespace App\Http\Services;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Mollie\Api\Exceptions\ApiException;
use App\Http\Interfaces\PaymentServiceInterface;

class PaymentService implements PaymentServiceInterface
{
    public function createOrGetCustomer(User $user) : string
    {
        if ($user->mollie_customer_id) {
            return $user->mollie_customer_id;
        }

       $customer = Mollie::api()->customers->create([
            'name'  => $user->name,
            'email' => $user->email,
        ]);


        $user->update(['mollie_customer_id' => $customer->id]);

        return $customer->id;
    }

    public function createSetup(Request $request)
    {
        $user = Auth::user();
        $customerId = $this->createOrGetCustomer($user);

        $context  = $request->input('context', 'webshop'); // npr: auction | webshop
        $entityId = $request->input('entity_id');          // opcionalno: auctionId | orderId
        $redirectUrl = rtrim(env('FRONTEND_URL'), '/')
        . "/#/aukcija/{$entityId}/success";

        $payment = Mollie::api()->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value'    => '0.00', // setup/kartica, bez naplate
            ],
            'customerId'   => $customerId,
            'sequenceType' => 'first',  // OBAVEZNO za mandat
            'description'  => "Setup card for {$context}",
            'redirectUrl'  => $redirectUrl,
            'webhookUrl'   => env('MOLLIE_WEBHOOK_URL'),
            'metadata'     => [
                'user_id'   => $user->id,
                'context'   => $context,
                'entity_id' => $entityId,
                'type'      => 'save-card',
            ],
        ]);

        return response()->json(['checkout_url' => $payment->getCheckoutUrl()]);
    }

    /** Pomoćna: izvuči poslednja 4 broja iz bilo kog stringa (**** **** 4242 -> 4242) */
    protected function last4(?string $maybeMasked): ?string
    {
        if (!$maybeMasked) return null;
        $digits = preg_replace('/\D+/', '', $maybeMasked);
        return $digits && strlen($digits) >= 4 ? substr($digits, -4) : null;
    }


    /** Webhook handler: upiši/azuriraj mandate (kartice) u payment_methods */
    public function handleWebhook(string $paymentId)
    {
        try {
            $payment = Mollie::api()->payments->get($paymentId);

            if (!($payment->isPaid() ?? false)) {
                Log::info('Webhook: payment not paid', ['id' => $paymentId, 'status' => $payment->status ?? null]);
                return;
            }

            $customerId = $payment->customerId ?? null;
            if (!$customerId) {
                Log::warning('Webhook: payment has no customerId', ['id' => $paymentId]);
                return;
            }

            // 1) PRONAĐI user-a (po mollie_customer_id ili METADATA fallback)
            $user = User::where('mollie_customer_id', $customerId)->first();

            if (!$user && !empty($payment->metadata?->user_id)) {
                $user = User::find($payment->metadata->user_id);
                if ($user && empty($user->mollie_customer_id)) {
                    // upiši customerId da sledeći put radi direktno
                    $user->update(['mollie_customer_id' => $customerId]);
                }
            }

            if (!$user) {
                Log::warning('Webhook: user not found', ['customerId' => $customerId, 'metadata_user' => $payment->metadata?->user_id ?? null]);
                return;
            }

            // (sigurnosno) ako i dalje nema upisano na user-u – upiši
            if (empty($user->mollie_customer_id)) {
                $user->update(['mollie_customer_id' => $customerId]);
            }

            $customer = Mollie::api()->customers->get($customerId);
            $mandates = $customer->mandates();
            $active   = collect($mandates)->firstWhere('status', 'valid');

            if (!$active) {
                Log::info('Webhook: no valid mandate', ['customerId' => $customerId]);
                return;
            }

            // (opciono) meta iz payment->details
            $details = $payment->details ?? null;
            $brand   = $details->cardLabel   ?? null;
            $last4   = isset($details->cardNumber) ? substr(preg_replace('/\D+/', '', $details->cardNumber), -4) : null;
            $holder  = $details->cardHolder  ?? null;
            $method  = $payment->method      ?? null;

            DB::transaction(function () use ($user, $active, $customerId, $brand, $last4, $holder, $method) {
                // 2) KORISTI customerId iz Mollie-ja (NE $user->mollie_customer_id)
                $pm = PaymentMethod::updateOrCreate(
                    ['user_id' => $user->id, 'mollie_mandate_id' => $active->id],
                    [
                        'mollie_customer_id' => $customerId,   // <— FIX
                        'brand'              => $brand,
                        'last4'              => $last4,
                        'holder'             => $holder,
                        'method'             => $method,
                        'mandate_status'     => $active->status ?? 'valid',
                        'is_active'          => ($active->status ?? 'valid') === 'valid',
                        'is_default'         => ($active->status ?? 'valid') === 'valid',
                    ]
                );

                // spusti ostale sa default-a
                $user->paymentMethods()
                    ->where('id', '!=', $pm->id)
                    ->update(['is_default' => false]);
            });

            Log::info('Webhook: synced', [
                'user_id' => $user->id, 'customerId' => $customerId, 'mandateId' => $active->id
            ]);
        } catch (ApiException $e) {
            Log::error('Mollie API error', ['paymentId' => $paymentId, 'msg' => $e->getMessage()]);
        } catch (Throwable $e) {
            Log::error('Webhook error', ['paymentId' => $paymentId, 'msg' => $e->getMessage()]);
        }
    }



    /** Naplati pobedniku aukcije preko default mandate-a */
    public function chargeWinner(User $user, float $amount)
    {
        $paymentMethod = $user->paymentMethods()->where('is_default', true)->first();

        if (!$paymentMethod) {
            throw new \Exception("No default payment method found.");
        }

        return Mollie::api()->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($amount, 2, '.', ''),
            ],
            'customerId'   => $paymentMethod->mollie_customer_id,
            'mandateId'    => $paymentMethod->mollie_mandate_id,
            'sequenceType' => 'recurring',
            'description'  => 'Charge for auction win',
            'webhookUrl'   => env('MOLLIE_WEBHOOK_URL'),
            'metadata'     => [
                'user_id' => $user->id,
                'type'    => 'charge',
            ],
        ]);
    }
}
