<?php
namespace App\Http\Services;

use App\Http\Interfaces\PaymentServiceInterface;
use App\Models\PaymentMethod;
use Mollie\Laravel\Facades\Mollie;

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

    public function createSetupPayment($user)
    {
        $customerId = $this->createOrGetCustomer($user);

        $payment = Mollie::api()->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => '0.00'
            ],
            'customerId'   => $customerId,
            'sequenceType' => 'first',
            'description'  => 'Setup card for future payments',
            'redirectUrl'  => env('FRONTEND_URL') . '/payment-success',
            'webhookUrl'   => route('mollie.webhook'),
        ]);

        return $payment->getCheckoutUrl();
    }

    public function handleWebhook(array $data)
    {
        $payment = Mollie::api()->payments->get($data['id']);
        $customerId = $payment->customerId;

        if (!$customerId || !$payment->isPaid()) {
            return;
        }

        $mandates = Mollie::api()->customers->get($customerId)->mandates();
        $activeMandate = collect($mandates)->firstWhere('status', 'valid');

        if (!$activeMandate) {
            return;
        }

        $user = \App\Models\User::where('mollie_customer_id', $customerId)->first();

        if (!$user) {
            return;
        }

        PaymentMethod::create([
            'user_id'            => $user->id,
            'mollie_customer_id' => $customerId,
            'mollie_mandate_id'  => $activeMandate->id,
            'is_default'         => true,
        ]);
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
            'webhookUrl'   => route('mollie.webhook'),
        ]);
    }
}
