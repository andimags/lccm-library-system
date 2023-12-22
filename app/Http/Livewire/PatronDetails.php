<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PatronDetails extends Component
{
    public $patron;

    protected $listeners = ['updatePatron' => 'render'];

    public function render()
    {
        return view('livewire.patron-details');
    }
}
