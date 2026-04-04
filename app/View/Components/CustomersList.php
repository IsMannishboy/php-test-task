<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CustomersList extends Component
{
    /**
     * Create a new component instance.
     */
    public $customers;
    public $customersCount;
    public function __construct(
        $customers,
        $customersCount
    ) {
        $this->customers = $customers;
        $this->customersCount = $customersCount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.customers-list');
    }
}
