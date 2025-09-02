<?php
namespace App\Http\Interfaces;

use Illuminate\Http\Request;

interface PaymentServiceInterface
{
    public function createOrGetCustomer($user);
    public function createSetup(Request $request);
    public function handleWebhook(array $data);
    public function chargeWinner($user, float $amount);
}
