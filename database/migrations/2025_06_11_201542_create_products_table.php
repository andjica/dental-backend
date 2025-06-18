<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

           
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

           
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('sub_category_id')->nullable()->constrained()->onDelete('set null');

            $table->enum('product_type', ['NEW', 'USED'])->default('NEW');
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2); // cena bez PDV-a
            $table->string('sku')->unique(); //mi pravimo unikatno
            $table->string('barcode')->nullable();

     
           
            $table->integer('quantity')->default(0); // koliko unosimo proizvoda
            $table->boolean('in_stock')->default(true); // da li smo stopirali na sajtu ili smo ostavili prodaju

           
            $table->decimal('length', 8, 2); // cm
            $table->decimal('width', 8, 2);  // cm
            $table->decimal('height', 8, 2); // cm
            $table->decimal('weight', 8, 2); // kg

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}
