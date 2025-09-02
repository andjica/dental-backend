<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\PaymentService;
use App\Http\Interfaces\AuctionInterface;
use App\Http\Requests\AuctionStoreRequest;
use App\Http\Requests\AuctionUpdateRequest;

class AuctionController extends Controller
{

    protected $auctionService;

    public function __construct(AuctionInterface $auctionService)
    {
        $this->auctionService = $auctionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auctions = $this->auctionService->getAll();
        $auctions->load(['user', 'images']);

        return response()->json([
            'success' => true,
            'data' => $auctions
        ], 200);
    }

    public function getByUserId($userId)
    {
        $auctions = $this->auctionService->getAllByUserId($userId);
        $auctions->load(['user', 'images']);

        if (is_null($auctions)) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $auctions
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AuctionStoreRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = Auth::user()->id;

        $auction = $this->auctionService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Auction created successfully.',
            'data' => $auction
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $auction = $this->auctionService->view($id);
        
        if (!$auction) {
            return response()->json([
                'success' => false,
                'message' => 'Auction not found.',
            ], 404);
        }
        $auction->load(['user', 'images', 'bids.user']);
        return response()->json([
            'success' => true,
            'message' => 'Auction fetched successfully.',
            'data' => $auction,
        ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuctionUpdateRequest $request, $id)
    {
        $validated = $request->validated();

        $auction = $this->auctionService->update($validated, $id);

        if (!$auction) {
            return response()->json([
                'success' => false,
                'message' => 'Auction not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Auction updated successfully.',
            'data' => $auction
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $deleted = $this->auctionService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Auction not found or already deleted.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Auction deleted successfully.',
        ], 200);
    }

     public function placeBid(Request $request, $auctionId)
    {
        $user = Auth::user();

        // 1. provera kartice
        if (!$user->defaultPaymentMethod) {
            // umesto user-a, prosleđujemo Request sa context i entity_id
            $setupRequest = new Request([
                'context' => 'auction',
                'entity_id' => $auctionId,
            ]);

            $checkoutResponse = app(PaymentService::class)->createSetup($setupRequest);

            $checkoutUrl = json_decode($checkoutResponse->getContent(), true)['checkout_url'];

            return response()->json([
                'success' => false,
                'requires_payment_setup' => true,
                'redirect_url' => $checkoutUrl,
                'message' => 'Please setup a payment method before bidding.'
            ], 402);
        }

        // 2. validacija iznosa
        $amount = $request->input('amount');
        if (!$amount || $amount <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid bid amount'], 422);
        }

        // 3. upis bida
        $bid = Bid::create([
            'auction_id' => $auctionId,
            'user_id'    => $user->id,
            'amount'     => $amount,
            'placed_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bid placed successfully.',
            'data'    => $bid,
        ]);
    }
}
