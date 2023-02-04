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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->decimal('amount',9,3);
            $table->integer('terms');
            $table->date('start_date')->nullable(true);
            $table->decimal('remaining_amount',9,3)->nullable(true);
            $table->decimal('term_amount',9,3)->nullable(true);
            $table->enum('status', ['Pending', 'Approved', 'Paid', 'Reject', 'Cancel'])->default('Pending');
            $table->timestamps();
        });

        Schema::table('loans', function($table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
