<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerService;
use App\Services\TicketService;
use App\Services\UserService;

use App\Http\Requests\LoginRequest;
use App\Models\Ticket;
class AdminController extends Controller
{
    protected $ticketService;
    protected $customerService;
    protected $userService;
    public function __construct(
        TicketService $ticketService,
        CustomerService $customerService,
      UserService $userService
    ) {
        $this->ticketService = $ticketService;
        $this->customerService = $customerService;
        $this->userService = $userService;
    }
    public function login(LoginRequest $request): JsonResponse
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
    public function dashboard()
    {
       

        
    $customersCount = $this->customerService->getCount();
    $customers = $this->customerService->getLatest();

    $ticketsCount = $this->ticketService->getCount();
    $tickets = $this->ticketService->getLatest();

    $usersCount = $this->userService->getCount();
    $users = $this->userService->getLatest();

        return view('admin', compact('customersCount', 'customers', 'ticketsCount', 'tickets', 'usersCount', 'users'));
    }
}
