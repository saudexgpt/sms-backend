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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('personal_access_tokens');
    }
};
