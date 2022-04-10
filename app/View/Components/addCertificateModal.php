<?php

namespace App\View\Components;

use Illuminate\View\Component;

class addCertificateModal extends Component
{
    public $root_certificates;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($rootCertificates)
    {
        $this->root_certificates = $rootCertificates;
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
