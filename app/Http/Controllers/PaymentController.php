<?php
namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\PaymentService;
use App\Http\Interfaces\PaymentServiceInterface;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

   public function createSetup(Request $request)
    {
        return $this->paymentService->createSetup($request);
    }

    public function check()
    {
        $user = Auth::user();
        $hasCard = $user->paymentMethods->where('is_default', true)->exists();

        return response()->json([
            'hasCard' => $hasCard
        ]);
    }


    public function webhook(Request $request)
    {
        Log::info('Webhook hit', $request->all());
        $this->paymentService->handleWebhook($request->all());

        return response()->json(['status' => 'ok']);
    }

    public function testCharge()
    {
        $this->paymentService->chargeWinner(Auth::user(), 99.99);

        return response()->json(['status' => 'charged']);
    }

   

}
