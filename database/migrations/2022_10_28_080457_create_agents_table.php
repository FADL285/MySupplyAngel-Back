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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('desc', 255)->nullable();
            $table->string('company_name')->nullable();
            $table->string('product_name')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->enum('agent_type', ['agent', 'distrebutor']);
            $table->enum('type', ['required_agent_or_distrebutor', 'potential_agent_or_potential_distrebutor']);
            $table->string('status')->nullable(); // pending - admin_accept - admin_reject
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
        Schema::dropIfExists('agents');
    }
};
