<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UsersList extends Component
{
    /**
     * Create a new component instance.
     */
    public $users;
    public $usersCount;
    public function __construct(
        $users,
        $usersCount
    ) {
        $this->users = $users;
        $this->usersCount = $usersCount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.users-list');
    }
}
