<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Certificate;

class addCertificateModal extends Component
{
    public $root_certificates;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->root_certificates = Certificate::all()->where('self_signed', true);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.add-certificate-modal');
    }
}
