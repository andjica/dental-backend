<?php
namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\Auth;
use App\Http\Interfaces\PaymentServiceInterface;

class PaymentService implements PaymentServiceInterface
{
    public function createOrGetCustomer($user)
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
        $customerId = $this->createOrGetCustomer($user); // ovde direktno pozivaš istu klasu

        $context  = $request->input('context', 'webshop'); // auction ili webshop
        $entityId = $request->input('entity_id'); // auctionId ili orderId

        $redirectUrl = env('FRONTEND_URL') . "/payment-success?context={$context}";
        if ($entityId) {
            $redirectUrl .= "&entity_id={$entityId}";
        }

        $payment = Mollie::api()->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value'    => '0.00',
            ],
            'customerId'   => $customerId,
            'sequenceType' => 'first',
            'description'  => "Setup card for {$context}",
            'redirectUrl'  => $redirectUrl,
            'webhookUrl'   => env('MOLLIE_WEBHOOK_URL'),
        ]);

        return response()->json(['checkout_url' => $payment->getCheckoutUrl()]);
    }



    public function handleWebhook(array $data)
    {
        $payment = Mollie::api()->payments->get($data['id']);
        $customerId = $payment->customerId;

        // Ako nije plaćeno ili nema customerId, prekidamo
        if (!$customerId || !$payment->isPaid()) {
            return;
        }

        // Uzimamo mandate (kartice) vezane za tog customer-a
        $mandates = Mollie::api()->customers->get($customerId)->mandates();
        $activeMandate = collect($mandates)->firstWhere('status', 'valid');

        if (!$activeMandate) {
            return;
        }

        // Nađemo user-a kome pripada customerId
        $user = User::where('mollie_customer_id', $customerId)->first();

        if (!$user) {
            return;
        }

        // Proveri da li već postoji kartica u bazi
        $existing = PaymentMethod::where('user_id', $user->id)
            ->where('mollie_mandate_id', $activeMandate->id)
            ->first();

        if ($existing) {
            // Ako postoji, markiraj je kao default i gotovo
            $existing->update(['is_default' => true]);
        } else {
            // Ako ne postoji, upiši novu
            PaymentMethod::create([
                'user_id'            => $user->id,
                'mollie_customer_id' => $customerId,
                'mollie_mandate_id'  => $activeMandate->id,
                'is_default'         => true,
            ]);
        }

        // Ako user ima više kartica → samo jedna može biti default
        PaymentMethod::where('user_id', $user->id)
            ->where('id', '!=', optional($existing)->id)
            ->update(['is_default' => false]);
    }


    public function chargeWinner($user, float $amount)
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

        ]);
    }
}
