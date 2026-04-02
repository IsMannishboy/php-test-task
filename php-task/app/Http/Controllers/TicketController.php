<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTicketRequest;

class TicketController extends Controller
{
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $customer = Customer::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name, 'phone' => $request->phone],
            ['phone'=> $request->phone]
        );
        $ticket = Ticket::firstOrCreate([
            'customer' => $customer->id,
            'topic' => $request->topic,
            'text' => $request->text,
            'status'=> "new",
            'response' => null,
        ]);
        if ($request->hasFile('attachment')) {
            $ticket->addMedia($request->file('attachment'))->toMediaCollection('attachments');
        }
        return response()->json($ticket, 201);
    }
}
