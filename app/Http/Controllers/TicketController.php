<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Customer;
use App\Models\Ticket;
class TicketController extends Controller
{
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $customer = Customer::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name, 'phone' => $request->phone]
        
        );
        $ticket = Ticket::create(

            [
                'customer' => $customer->id,
                'topic' => $request->topic,
                'text' => $request->text,
                'status'=> "new",
                'response' => null,
            ]
        );
        if ($request->hasFile('attachment')) {
<<<<<<< HEAD
            $ticket->addMedia($request->file('attachment'))->toMediaCollection('attachments');
=======
            $ticket->addMedia($request->file('attachment'))->toMediaCollection('attachments','public');
        }else {
>>>>>>> tickets-logic
        }
        return response()->json($ticket, 201);
    }
}
