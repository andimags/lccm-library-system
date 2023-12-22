<?php

namespace App\Http\Livewire;

use Livewire\Component;

class OffSiteCirculationDetails extends Component
{
    public $offSiteCirculation;

    protected $listeners = ['fineAdded' => 'render'];

    public function render()
    {
        return view('livewire.off-site-circulation-details');
    }
}
