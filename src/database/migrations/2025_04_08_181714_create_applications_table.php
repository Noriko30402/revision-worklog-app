<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staffs')->onDelete('cascade');
            $table->string('date');
            $table->time('clock_in')->nullable()->default(null);
            $table->time('clock_out')->nullable();
            $table->time('rest_in')->nullable()->default(null);
            $table->time('rest_out')->nullable();
            $table->foreignId('work_id')->constrained('works')->onDelete('cascade');
            $table->string('comment');
            $table->boolean('approved')->default(0);
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
        Schema::dropIfExists('applications');
    }
}
