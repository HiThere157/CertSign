<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\User;

class changeOwnerModal extends Component
{
    public $all_users;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->all_users = User::all();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.change-owner-modal');
    }
}
