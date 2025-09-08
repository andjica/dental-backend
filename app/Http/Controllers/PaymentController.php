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
        // Mollie šalje form-urlencoded: id=tr_xxx
        $paymentId = $request->input('id');
        Log::info('Mollie webhook hit', ['body' => $request->all(), 'raw' => $request->getContent()]);

        if (!$paymentId) {
            Log::warning('Webhook: missing id');
            return response('OK', 200); // 200 da Mollie ne spama retry
        }
        $this->paymentService->handleWebhook($paymentId);
    
        return response('OK', 200);
    }

    public function testCharge()
    {
        $this->paymentService->chargeWinner(Auth::user(), 99.99);

        return response()->json(['status' => 'charged']);
    }

   

}
