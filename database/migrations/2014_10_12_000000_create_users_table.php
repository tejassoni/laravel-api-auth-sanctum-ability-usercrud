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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname',100)->nullable();
            $table->string('lastname',100)->nullable();
            $table->string('email',100)->unique();
            $table->string('mobile',13)->unique();
            $table->char('gender',6)->nullable();
            $table->text('address')->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('country',100)->nullable();
            $table->char('pincode',6)->nullable();
            $table->date('birthdate')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->char('otp',6)->nullable();
            $table->timestamp('otp_send_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
