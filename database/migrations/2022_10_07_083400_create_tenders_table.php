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
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('desc', 255)->nullable();
            $table->double('tender_specifications_value')->nullable();
            $table->double('insurance_value')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->string('company_name')->nullable();
            $table->string('status')->nullable(); // pending - admin_accept - admin_reject
            $table->boolean('is_free')->default(false);
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
        Schema::dropIfExists('tenders');
    }
};
