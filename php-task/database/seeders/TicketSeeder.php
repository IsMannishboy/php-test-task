<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ticket;

class TicketSeeder extends Seeder{
       public function run(): void
{
    $user = User::factory()->create();

    Ticket::factory()
        ->count(5)
        ->for($user, 'customer')
        ->create();
}
}
