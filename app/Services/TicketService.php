<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Ticket;
use App\Http\Requests\TicketsWithFiltersRequest;
   use Carbon\Carbon;

class TicketService
{
   public function find($id){
        return Ticket::findOrFail($id);
   }

    public function store(array $data): Ticket
    {
        
        $ticket = Ticket::create([
            'customer_id' => $data['customer_id'],
            'topic' => $data['topic'],
            'text' => $data['text'],
            'status' => 'new',
        ]);

        if (isset($data['attachment'])) {
            $ticket->addMedia($data['attachment'])
                   ->toMediaCollection('attachments', 'public');
        }

        return $ticket;
    }
    public function add_file(Ticket $ticket, $file)
    {
        $ticket->addMedia($file)
               ->toMediaCollection('attachments', 'public');
    }
    public function update_ticket(Ticket $ticket, array $data): Ticket
    {
        $ticket->update($data);
        return $ticket;
    }
    public function get_statistic($date): array
    {
        $query = Ticket::query();

        if ($date) {
            $date = Carbon::parse($date)->startOfDay();
            $query->where('created_at', '>=', $date);
        }
        $totalTickets = (clone $query)->count();

            $newTickets = (clone $query)
                ->where('status', 'new')->latest()->take(10)    
                ->count();

            $processingTickets = (clone $query)
                ->where('status', 'processing')->latest()->take(10)->count();

            $resolvedTickets = (clone $query)
                ->where('status', 'done')->latest()->take(10)->count();

            $unresolvedTickets = $totalTickets - $resolvedTickets;

        return [
            'total_tickets' => $totalTickets,
            'new_tickets' => $newTickets,
            'processing_tickets' => $processingTickets,
            'resolved_tickets' => $resolvedTickets,
            'unresolved_tickets' => $unresolvedTickets,
        ];
    }
    public function ticket_filter(TicketsWithFiltersRequest $request): array
    {
        $customerId = $request->query('customerId')?? null;
        $status     = $request->query('status')?? null;
        $date       = $request->query('date');
        $email      = $request->query('email')?? null;
        $phone      = $request->query('phone')?? null;
        $query = Ticket::with(['customer','media']);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
        if ($status && $status != 'all') {
            $query->where('status', $status);
        }
        if ($date) {
            $date = Carbon::parse($date)->startOfDay();
            $query->where('created_at', '>=', $date);
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

        return $query->get()->toArray();
    }
    public function getCount(): int
    {
        return Ticket::count();
    }

    public function getLatest()
    {
        return Ticket::with('customer')
            ->latest()
            ->take(10)
            ->get();
    }   
    
}