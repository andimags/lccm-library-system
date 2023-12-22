<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ReservationInformation extends Component
{
    public $reservation;
    public $user;
    public $reservationStatus;

    protected $listeners = ['refreshReservationInformation' => 'refresh'];

    public function refresh(){
        $this->mount($this->reservation);
    }

    public function mount($reservation)
    {
        $this->reservation = $reservation;
        $this->user = $reservation->user()->first();

        $array = array(
            'pending' => 'warning',
            'accepted' => 'success',
            'partially accepted' => 'success',
            'declined' => 'danger'
        );

        $this->reservationStatus = '<span class="badge badge-' . $array[$reservation->status] . '">' . $reservation->status . '</span>';
    }

    public function render()
    {
        return view('livewire.reservation-information');
    }
}
