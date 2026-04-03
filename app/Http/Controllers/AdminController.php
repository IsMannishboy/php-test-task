<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Ticket;
class AdminController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user()
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
