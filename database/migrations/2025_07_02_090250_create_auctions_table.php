<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ko je postavio aukciju
            $table->string('name');                // Auction name
            $table->text('description')->nullable(); // Auction description
            $table->decimal('base_price', 10, 2);  // Cena u evrima
            $table->timestamp('auction_date');     // Datum i vreme početka aukcije
            $table->timestamps();    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
