<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staffs')->onDelete('cascade');
            $table->string('date');
            $table->time('clock_in')->nullable()->default(null);
            $table->time('clock_out')->nullable();
            $table->time('total_work_time')->nullable();
            $table->time('total_rest_time')->nullable();
            $table->integer('status')->nullable();
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('works');
    }
}
