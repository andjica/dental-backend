<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\AuctionInterface;
use Illuminate\Support\Facades\Auth;
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
        //
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

}
