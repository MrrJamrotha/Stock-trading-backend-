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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('stock_id')->constrained('stocks', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('trade_type', ['buy', 'sell']);
            $table->integer('quantity')->default(0);
            $table->decimal('price', 15, 2)->default(0.0);
            $table->decimal('total')->storedAs('quantity * price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
