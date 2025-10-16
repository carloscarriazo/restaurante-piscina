<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'transfer'])->default('cash');
            $table->string('customer_name')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['paid', 'cancelled'])->default('paid');
            $table->timestamps();

            $table->index(['created_at', 'status']);
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
