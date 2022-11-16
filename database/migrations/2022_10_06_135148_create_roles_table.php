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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('role_translations', function(Blueprint $table)
        {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('desc')->nullable();
            $table->string('locale')->index();
            $table->unique(['role_id','locale']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_translations');
    }
};
