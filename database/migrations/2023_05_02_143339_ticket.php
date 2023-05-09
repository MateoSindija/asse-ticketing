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
        Schema::create("ticket", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("user_id");
            $table->uuid("client_id");
            $table->foreign("user_id")->references("id")->on("user");
            $table->foreign("client_id")->references("id")->on("client");
            $table->string("status", 20);
            $table->string("title", 100);
            $table->string("description", 1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop("ticket");
    }
};
