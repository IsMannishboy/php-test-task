<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\User;

use App\Models\Ticket;
class AdminController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();
            $user = Auth::user();
            $roles = $user->roles->pluck('name');
            $request->session()->put('roles', $roles);
            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user(),
                'roles' => $roles
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    public function dashboard()
    {
       

        $customersCount = Customer::count();
        $customers = Customer::latest()->take(10)->get();


        $ticketsCount = Ticket::count();
        $tickets = Ticket::with('customer')->get(); 
        $usersCount = User::count();
        $users = User::latest()->take(10)->get();

        return view('admin', compact('customersCount', 'customers', 'ticketsCount', 'tickets', 'usersCount', 'users'));
    }
}
