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
        Schema::create('test_series_questions', function (Blueprint $table) {
            $table->id();

            $table->string('question_index');
            $table->unsignedBigInteger('test_series_id');
            $table->unsignedBigInteger('question_id');
            $table->float('positive_mark')->default(0);
            $table->float('negative_mark')->default(0);

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
        Schema::dropIfExists('test_series_questions');
    }
};
