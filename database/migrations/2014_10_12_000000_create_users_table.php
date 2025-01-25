<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
<<<<<<< HEAD
     */
    public function up(): void
=======
     *
     * @return void
     */
    public function up()
>>>>>>> f99a6181e9ce69c2e3af34ec7847bb2c510d2edd
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
<<<<<<< HEAD
     */
    public function down(): void
=======
     *
     * @return void
     */
    public function down()
>>>>>>> f99a6181e9ce69c2e3af34ec7847bb2c510d2edd
    {
        Schema::dropIfExists('users');
    }
};
