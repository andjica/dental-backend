<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('tax_number')->unique();
            $table->string('registration_number')->unique();
            $table->string('email')->unique();
            $table->string('website')->nullable();
            $table->foreignId('country_id')->constrained()->onDelete('restrict');
            $table->foreignId('city_id')->constrained()->onDelete('restrict');
            $table->timestamps();
        });
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
}
