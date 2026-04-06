<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getCount(): int
    {
        return User::count();
    }

    public function getLatest()
    {
        return User::latest()->take(10)->get();
    }

  
}