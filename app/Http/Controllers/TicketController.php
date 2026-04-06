<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Requests\StatisticRequest;

use App\Http\Requests\TicketsWithFiltersRequest;
   use Carbon\Carbon;
use App\Services\TicketService;
use App\Services\CustomerService;
use App\Models\Customer;
use App\Models\Ticket;
class TicketController extends Controller
{
    protected $ticketService;
    protected $customerService;

    public function __construct(TicketService $ticketService, CustomerService $customerService)
    {
        $this->ticketService = $ticketService;
        $this->customerService = $customerService;
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $customer = $this->customerService->firstOrCreate([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        $ticket = $this->ticketService->store([
            'customer_id' => $customer->id,
            'topic' => $request->topic,
            'text' => $request->text,
            'status'=> "new",
            'response' => null,
        ]);
       
        if ($request->hasFile('attachment')) {
            $this->ticketService->add_file($ticket, $request->file('attachment'));
        }
        return response()->json($ticket, 201);
    }
    public function update(UpdateTicketRequest $request, $id): JsonResponse
    {
        $changes = $request->only(['topic', 'text', 'status']);

        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }


        $this->ticketService->update_ticket($ticket, $changes);

        return response()->json([
            'message' => 'Ticket updated successfully',
            'changes' => $changes
        ]);
    }

        public function statistic(StatisticRequest $request): JsonResponse
        {
            $date = $request->query('date');
            $statistics = $this->ticketService->get_statistic($date);

            return response()->json($statistics);
        }
    public function TicketFilter(TicketsWithFiltersRequest $request): JsonResponse
    {
        $tickets = $this->ticketService->ticket_filter($request);
        
        
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