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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('status')->default(\App\Enums\OrderStatusEnum::NEW);
            $table->string('name');
            $table->integer('age');
            $table->string('mobile_number');
            $table->string('nationality');
            $table->string('city');
            $table->string('company_name');
            $table->decimal('salary', 10, 2);
            $table->string('bank');
            $table->boolean('liabilities');
            $table->text('liabilities_description')->nullable();
            $table->decimal('installment', 10, 2);
            $table->string('car_brand');
            $table->string('car_name');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
