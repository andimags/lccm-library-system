<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CollectionDetails extends Component
{
    public $collection;

    protected $listeners = ['updateCollection' => 'render'];

    public function render()
    {
        return view('livewire.collection-details');
    }
}
