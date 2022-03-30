<?php

namespace App\View\Components;

use Illuminate\View\Component;

class addPermissionModal extends Component
{
    public $all_users;
    public $id;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($allUsers, $id)
    {
        $this->all_users = $allUsers;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.add-permission-modal');
    }
}
