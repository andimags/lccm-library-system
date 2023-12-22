<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">
                        Transaction information
                    </h4>
                    <div class="float-right">
                        <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                            id="btn-transaction-edit" value="edit">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                            id="btn-transaction-delete" value="delete">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-condensed">
                    <tbody>
                        <tr>
                            <td>Transaction ID</td>
                            <td><b>{{ $transaction->id }}</b></td>
                            <td>Borrower name</td>
                            <td><b>{{ $user->last_name . ', ' . $user->first_name }}</b></td>
                        </tr>
                        <tr>
                            <td>Cancel reservation by</td>
                            <td><b>{{ $transaction->cancel_by }}</b></td>
                            <td>Date created</td>
                            <td><b>{{ $transaction->created_at }}</b></td>
                        </tr>
                        <tr>
                            <td>Due date</td>
                            <td><b>{!! $transaction->date_due !!}</b></td>
                            <td>Status</td>
                            <td>{!! $transactionStatus !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
