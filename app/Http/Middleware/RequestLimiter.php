<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Customer;
use Carbon\Carbon;

class RequestLimiter
{
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->input('email');
        $phone = $request->input('phone');
        $customer = Customer::where(function ($query) use ($email, $phone) {
            $query->where('email', $email)
                  ->orWhere('phone', $phone);
        })->first();

        if (!$customer) {
            return $next($request);
        }

        if (
            $customer->last_request &&
            Carbon::parse($customer->last_request)->diffInHours(now()) < 24
        ) {
            abort(429, 'Too many requests');
        }

        $customer->last_request = now();
        $customer->save();

        return $next($request);
    }
}