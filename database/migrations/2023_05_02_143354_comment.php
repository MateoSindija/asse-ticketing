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
        Schema::create("comment", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("ticket_id");
            $table->uuid("user_id");
            $table->foreign("ticket_id")->references("id")->on("ticket");
            $table->foreign("user_id")->references("id")->on("user");
            $table->string("comment", 300);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop("comment");
    }
};
