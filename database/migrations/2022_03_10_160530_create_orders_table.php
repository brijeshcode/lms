<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->default('1');
            $table->date('date');
            $table->integer('quantity');
            $table->double('discount', 10,2)->default(0);
            $table->double('sub_total', 10,2)->default(0);
            $table->double('total', 10,2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'returned', 'cancelled'])->default('pending');
            $table->boolean('is_paid')->default(false);


            $table->text('note')->nullable()->comment('additional information for this entry');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('user_id')->default('1');
            $table->ipAddress('user_ip')->default('127.0.0.1');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
