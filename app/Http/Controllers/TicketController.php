<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Requests\TicketsWIthFiltersRequest;
   use Carbon\Carbon;

use App\Models\Customer;
use App\Models\Ticket;
class TicketController extends Controller
{
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $customer = Customer::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name, 'phone' => $request->phone,'last_request' => now()]
        );
        $ticket = Ticket::create(

            [
                'customer_id' => $customer->id,
                'topic' => $request->topic,
                'text' => $request->text,
                'status'=> "new",
                'response' => null,
            ]
        );
        if ($request->hasFile('attachment')) {
            $ticket->addMedia($request->file('attachment'))->toMediaCollection('attachments','public');
        }
        return response()->json($ticket, 201);
    }
    public function update(UpdateTicketRequest $request,$id): JsonResponse
    {
        
        $changes = $request->only(['topic', 'text', 'status']);
        Ticket::where('id',$id)->update($changes);
        return response()->json(['message' => 'Ticket updated successfully', 'changes' => $changes]);
    }

        public function statistic(Request $request): JsonResponse
        {
            $date = $request->query('date');

            $query = Ticket::query();

            if ($date) {
                $date = Carbon::parse($date)->startOfDay();
                $query->where('created_at', '>=', $date);
            }

            $totalTickets = (clone $query)->count();

            $newTickets = (clone $query)
                ->where('status', 'new')
                ->count();

            $processingTickets = (clone $query)
                ->where('status', 'processing')
                ->count();

            $resolvedTickets = (clone $query)
                ->where('status', 'done')
                ->count();

            $unresolvedTickets = $totalTickets - $resolvedTickets;

            return response()->json([
                'total_tickets' => $totalTickets,
                'new_tickets' => $newTickets,
                'processing_tickets' => $processingTickets,
                'resolved_tickets' => $resolvedTickets,
                'unresolved_tickets' => $unresolvedTickets,
            ]);
        }
    public function TicketFilter(TicketsWithFiltersRequest $request): JsonResponse
    {
        $customerId = $request->header('customerId')?? null;
        $status     = $request->header('status')?? null;
        $date       = $request->header('X-Date');
        $email      = $request->header('email')?? null;
        $phone      = $request->header('phone')?? null;
        $query = Ticket::with(['customer','media']);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
        if ($status && $status != 'all') {
            $query->where('status', $status);
        }
        if ($date) {
            $query->where('created_at', '>', $date);
        }
        if ($email) {
            $query->whereHas('customer', function ($q) use ($email) {
                $q->where('email', $email);
            });
        }
        if ($phone) {
            $query->whereHas('customer', function ($q) use ($phone) {
                $q->where('phone', $phone);
            });
        }

        $tickets = $query->get();
        
        return response()->json($tickets);
    }
    public function download($ticket_id)
    {
        $ticket = Ticket::find($ticket_id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        $media = $ticket->getFirstMedia('attachments');
        if (!$media) {
            return response()->json(['message' => 'No attachment found'], 404);
        }
            return response()->download($media->getPath(), $media->file_name);

    }
}