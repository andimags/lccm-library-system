<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">
                        Reservation information
                    </h4>
                    <div class="float-right">
                        @if ($reservation->status == 'pending')
                            <button class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModalCenter" data-value="accepted" id="btn-reservation-accept">
                                <i class="fa-solid fa-check"></i> Accept
                            </button>
                            <button class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModalCenter" data-value="declined" id="btn-reservation-decline">
                                <i class="fa-solid fa-xmark"></i> Decline
                            </button>
                        @else
                            <button class="btn btn-outline-primary mr-1" data-toggle="modal"
                                data-target="#exampleModalCenter" data-value="pending" id="btn-reservation-cancel">
                                <i class="fa-solid fa-ban"></i> Set status to pending
                            </button>
                        @endif

                        <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                            id="btn-reservation-delete" value="delete">
                            <i class="fa-solid fa-trash-can"></i> Delete
                            <span class="transaction-count"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-condensed">
                    <tbody>
                        <tr>
                            <td>Reservation ID</td>
                            <td><b>{{ $reservation->id }}</b></td>
                            <td>Borrower name</td>
                            <td><b>{{ $user->last_name . ', ' . $user->first_name }}</b></td>
                        </tr>
                        <tr>
                            <td>Cancel reservation by</td>
                            <td><b>{{ $reservation->cancel_by }}</b></td>
                            <td>Date created</td>
                            <td><b>{{ $reservation->created_at }}</b></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>{!! $reservationStatus !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
