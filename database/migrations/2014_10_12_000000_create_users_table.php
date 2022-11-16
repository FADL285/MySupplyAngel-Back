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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone');
            $table->string('phone_code')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('whats')->nullable();
            $table->string('whats_code')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_admin_active_user')->default(true);
            $table->boolean('is_ban')->default(false)->nullable();
            $table->text('ban_reason')->nullable();
            $table->string('verified_code')->nullable();
            $table->string('user_type')->nullable(); //admin - superadmin - client
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_need_job')->default(false);
            $table->boolean('is_subcribed')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
