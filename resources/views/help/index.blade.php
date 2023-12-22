@extends('layout.app')
@section('title', 'Help')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">Help</h4>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="font-weight-bold">Borrowing process</h3>
                                        <table class="table table-striped table-hover">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 100px">Step 1</td>
                                                    <td>Requires the borrower to consult the Online Public Access Catalog (OPAC)/ card catalog to see if the needed material is available in the library.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 2</td>
                                                    <td>Requires the borrower to consult the Online Public Access Catalog (OPAC)/ card catalog to see if the needed material is available in the library.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 3</td>
                                                    <td>Pulls out the book card from the book pocket and checks against the accession number.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 4</td>
                                                    <td>Requires the borrower to write his/her name and student number at the book card and request to present his/her I.D. for proper charging.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 5</td>
                                                    <td>Checks the I.D. and the status of the borrower on the Athena database. <br>
                                                        <span class="text-muted">Note: If he/she has an overdue/fines it must be settled first.</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Step 6</td>
                                                    <td>Library staff encodes the accession number and/or title of borrowed materials in Athena database.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 7</td>
                                                    <td>Stamps due date on the date due slip affixes with signature and inserts to the book pocket.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 8</td>
                                                    <td>Requires the borrower to inspect the borrowed materials for any damage before leaving the counter.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 9</td>
                                                    <td>Files the book cards according to call number.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <h3 class="font-weight-bold">Returning process</h3>
                                        <table class="table table-striped table-hover">
                                            <tbody>
                                                <tr>
                                                    <td style="width:100px">Step 1</td>
                                                    <td>Receives book returned by borrower. <br>
                                                        <span class="text-muted">Note: If for renewal, updates the Athena database</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Step 2</td>
                                                    <td>Inspects the returned materials for damages or torn pages.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 3</td>
                                                    <td>Checks and collects fines for overdue materials borrowed by the borrower and issues O.R.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 4</td>
                                                    <td>Clears the account of the borrower in the Athena database.</td>
                                                </tr>
                                                <tr>
                                                    <td>Step 5</td>
                                                    <td>Removes the date due slip in the book pocket of the returned material and inserts book card.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Step 6</td>
                                                    <td>Shelves the library materials</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <h3 class="font-weight-bold">Rules or policies</h3> <br>
                                        <h3 class="font-weight-bold">Overdue materials</h3>
                                        <h4>4.4.1   Charges for overdue books are as follows:</h4>
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <th></th>
                                                <th>Fine</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Circulation/ Filipiniana books</td>
                                                    <td>Php 5.00/day/book</td>
                                                </tr>
                                                <tr>
                                                    <td>Reserved/ Newly Acquired books</td>
                                                    <td>Php 5.00/day/book if not returned after 8:00 o’clock additional (For College Library) Php 5.00 per succeeding hour</td>
                                                </tr>
                                                <tr>
                                                    <td>Electronic/ Cartographic materials</td>
                                                    <td>Php 5.00/hour</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <h5 class="font-weight-bold">A.  College Library</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th scope="col"></th>
                                                    <th scope="col">Student</th>
                                                    <th scope="col">Faculty</th>
                                                    <th scope="col">Employee</th>
                                                    <th scope="col">Librarian</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <form action="">
                                                    @foreach ($prefixes as $prefix)
                                                        <tr>
                                                            <th>{{ $prefix }}</th>
                                                            @php
                                                                $roles = ['Student', 'Faculty', 'Employee', 'Librarian'];
                                                            @endphp
                                                            @for ($i = 0; $i <= 3; $i++)
                                                                @php
                                                                    $holdingOption = \App\Models\HoldingOption::where('value', $prefix)->first();
                                                                    $role = \Spatie\Permission\Models\Role::findByName($roles[$i]);
                                                                    $loaningPeriod = \App\Models\LoaningPeriod::where('role_id', $role->id)
                                                                        ->where('holding_option_id', $holdingOption->id)
                                                                        ->first();

                                                                    $no_of_days = $loaningPeriod ? $loaningPeriod->no_of_days : 1;
                                                                @endphp
                                                                <td>
                                                                    <div class="form-group">
                                                                        @if ($no_of_days == 0)
                                                                            Room Use Only
                                                                        @else
                                                                            {{ $no_of_days }} day(s)
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            @endfor
                                                        </tr>
                                                    @endforeach
                                                </form>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
