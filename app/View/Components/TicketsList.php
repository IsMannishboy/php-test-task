<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TicketsList extends Component
{
    public  $customersCount;
    public  $customers;
    public  $ticketsCount;
    public  $tickets;
    public  $usersCount;
    public  $users;

    public function __construct(
        $customersCount,
        $customers,
        $ticketsCount,
        $tickets,
        $usersCount,
        $users
    ) {
        $this->customersCount = $customersCount;
        $this->customers = $customers;
        $this->ticketsCount = $ticketsCount;
        $this->tickets = $tickets;
        $this->usersCount = $usersCount;
        $this->users = $users;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tickets-list');
    }
}
