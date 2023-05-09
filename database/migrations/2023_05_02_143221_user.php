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

        Schema::create("user", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("first_name", 50);
            $table->string("last_name", 50);
            $table->string("email")->unique();
            $table->string("password");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop("user");
    }
};
