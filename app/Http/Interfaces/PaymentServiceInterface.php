<?php
namespace App\Http\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface PaymentServiceInterface
{
    public function createOrGetCustomer(User $user);
    public function createSetup(Request $request);
    public function handleWebhook(string $paymentId);
    public function chargeWinner(User $user, float $amount);
}
