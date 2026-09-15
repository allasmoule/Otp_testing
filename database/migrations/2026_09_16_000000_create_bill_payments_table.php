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
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('payer_name');
            $table->string('bill_type'); // Education Bill, Electricity Bill, etc.
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // bkash, nagad
            $table->string('account_number'); // bKash or Nagad mobile number
            $table->string('trx_id')->unique();
            $table->string('status')->default('SUCCESS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
    }
};
