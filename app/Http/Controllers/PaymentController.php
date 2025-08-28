<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Interfaces\PaymentServiceInterface;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createSetup()
    {
        $url = $this->paymentService->createSetupPayment(Auth::user()
);

        return response()->json(['checkout_url' => $url]);
    }

    public function webhook(Request $request)
    {
        $this->paymentService->handleWebhook($request->all());

        return response()->json(['status' => 'ok']);
    }

    public function testCharge()
    {
        $this->paymentService->chargeWinner(Auth::user(), 99.99);

        return response()->json(['status' => 'charged']);
    }
}
