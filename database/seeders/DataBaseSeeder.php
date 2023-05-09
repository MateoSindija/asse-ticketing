<?php

use App\Models\Client;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataBaseSeeder extends Seeder
{
    // @return void
    public function run()
    {
        User::factory()->count(20)->create();
        Client::factory()->count(20)->create();
        Ticket::factory()->count(20)->create();
        Comment::factory()->count(20)->create();
    }
}
