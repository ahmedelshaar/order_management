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
            $table->boolean('is_saudi')->default(true);
            $table->string('city');
            $table->string('company_name');
            $table->decimal('salary', 10, 2);
            $table->string('bank');
            $table->boolean('liabilities')->default(false);
            $table->decimal('liabilities_amount')->nullable();
            $table->string('car_brand');
            $table->string('car_name');
            $table->string('car_model');
            $table->boolean('traffic_violations')->default(false);
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
