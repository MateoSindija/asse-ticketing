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
        Schema::create('reply', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("comment_id");
            $table->uuid("user_id");
            $table->foreign("comment_id")->references("id")->on("comment");
            $table->foreign("user_id")->references("id")->on("user");
            $table->string("reply", 300);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reply');
    }
};
