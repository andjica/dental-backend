<?php
namespace App\Http\Interfaces;

interface PaymentServiceInterface
{
    public function createOrGetCustomer($user);
    public function createSetupPayment($user);
    public function handleWebhook(array $data);
    public function chargeWinner($user, float $amount);
}
