@extends('layout.app')
@section('title', Route::currentRouteName() == 'imports.index' ? 'Imports' : 'Imports Archive')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'imports.index')
                                    Imports
                                @else
                                    Imports Archive
                                @endif
                            </h4>
                            <ul class="breadcrumbs">
                                <li class="nav-home">
                                    <a href="{{ route('dashboard.index') }}">
                                        <i class="flaticon-home"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            {{-- <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li><b>Users</b></li>
                                            <ul>
                                                <li><button class="btn btn-primary btn-link p-0">List of users</button>
                                                </li>
                                            </ul>

                                            <li><b>Collections</b></li>
                                            <ul>
                                                <li><button class="btn btn-primary btn-link p-0">List of collections</button></li>
                                                <li><button class="btn btn-primary btn-link p-0">List of copies</button></li>
                                            </ul>

                                            <li><b>Off-Site Circulations</b></li>
                                            <ul>
                                                <li><button class="btn btn-primary btn-link p-0">List of off-site circulations</button></li>
                                            </ul>

                                        </ul>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                All imports
                                            </h4>
                                            <div class="float-right">
                                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'imports.index')
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="import-delete-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Delete all
                                                        <span class="import-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="import-add">
                                                        <i class="fa fa-plus"></i>
                                                        Import Excel File
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="import-force-delete-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Force delete all
                                                        <span class="import-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="import-restore-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Restore all
                                                        <span class="import-count"></span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table w-100 table-striped table-hover nowrap" id="table-imports">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Table</th>
                                                        <th>Success Count</th>
                                                        <th>Failed Count</th>
                                                        <th>Total Records</th>
                                                        <th>Librarian</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Table</th>
                                                        <th>Success Count</th>
                                                        <th>Failed Count</th>
                                                        <th>Total Records</th>
                                                        <th>Librarian</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
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

        <!-- Modal -->
        <div class="modal fade pr-0" id="import-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="import-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <nav>
                            <div class="nav nav-tabs nav-line" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-patrons-tab" data-toggle="tab"
                                    href="#nav-patrons" role="tab" aria-controls="nav-patrons"
                                    aria-selected="true">Patrons</a>
                                <a class="nav-item nav-link" id="nav-collections-tab" data-toggle="tab"
                                    href="#nav-collections" role="tab" aria-controls="nav-collections"
                                    aria-selected="false">Collections</a>
                                <a class="nav-item nav-link" id="nav-copies-tab" data-toggle="tab" href="#nav-copies"
                                    role="tab" aria-controls="nav-copies" aria-selected="false">Copies</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-patrons" role="tabpanel"
                                aria-labelledby="nav-patrons-tab">
                                <div class="alert alert-primary mt-3" role="alert">
                                    <b>Note: The excel file's first row or headers should include something like the
                                        following, it is important to note that the columns can be in any order, allowing
                                        for a more flexible import process.
                                    </b><br><br>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Last name</th>
                                                    <th scope="col">First name</th>
                                                    <th scope="col">Email</th>
                                                    <th scope="col">Role</th>
                                                    <th scope="col">Groups</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>101</td>
                                                    <td>Ipsum</td>
                                                    <td>Lorem</td>
                                                    <td>ipsum.lorem@gmail.com</td>
                                                    <td>Student</td>
                                                    <td>Group1;Group2</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br>
                                    Multivalued inputs like <b>Groups</b> should be separated by ';' or semicolon.<br><br>
                                    Optional columns are <b>Email</b>, <b>Role</b> and <b>Groups</b>.<br><br>
                                    Optional inputs left blank if provided in Excel.
                                </div>
                                <form id="import-patrons-form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group" id="form_group_role">
                                        <label for="roles">Role select</label> 
                                        <select class="form-control patron-input" id="roles" name="roles">
                                            <option value=""></option>
                                            <option value="faculty">Faculty</option>
                                            <option value="employee">Employee</option>
                                            <option value="student">Student</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg" id="role_msg"></small>
                                    </div>
                                    <div class='form-group patron-input' id="form_group_patron_file">
                                        <label for="patron_file">Excel file <span
                                            class="badge badge-count text-danger">Required</span></label>
                                        <input type="file" class="form-control-file" id="patron-file" name="patron_file">
                                        <small class="form-text text-muted text-danger input_msg" id="patron_file_msg"></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block"
                                            id="patron-modal-button" disabled>
                                            Import Patrons Excel
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="nav-collections" role="tabpanel"
                                aria-labelledby="nav-collections-tab">
                                <div class="alert alert-primary mt-3" role="alert">
                                    <b>Note: The excel file's first row or headers should include something like the
                                        following, it is important to note that the columns can be in any order, allowing
                                        for a more flexible import process.
                                    </b><br><br>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Format</th>
                                                    <th scope="col">Authors</th>
                                                    <th scope="col">Title</th>
                                                    <th scope="col">Subtitles</th>
                                                    <th scope="col">Edition</th>
                                                    <th scope="col">Series Title</th>
                                                    <th scope="col">ISBN</th>
                                                    <th scope="col">Publication Place</th>
                                                    <th scope="col">Publisher</th>
                                                    <th scope="col">Copyright Year</th>
                                                    <th scope="col">Physical Description</th>
                                                    <th scope="col">Subjects</th>
                                                    <th scope="col">Call Number</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Books</td>
                                                    <td>Doe, John;Smith, Jane</td>
                                                    <td>The Mystery of the Hidden Treasure</td>
                                                    <td>Adventure;Mystery</td>
                                                    <td>1st Edition</td>
                                                    <td>Adventure Series</td>
                                                    <td>1234567890</td>
                                                    <td>New York</td>
                                                    <td>XYZ Publishing</td>
                                                    <td>2023</td>
                                                    <td>300 pages</td>
                                                    <td>Fiction;Mystery</td>
                                                    <td>PS3554.O3 M9 2023</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br>
                                    Multivalued inputs like <b>Authors, Subtitles & Subjects</b> should be separated by ';'
                                    or semicolon.<br><br>
                                    Required column is <b>Title.</b><br><br>
                                    Optional inputs left blank if provided in Excel.
                                </div>
                                <form id="import-collections-form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group" id="form_group_format">
                                        <label for="format">Format select</label> 
                                        <select class="form-control collection-input" id="format" name="format">
                                            <option value=""></option>
                                            <option value="books">Books</option>
                                            <option value="periodicals">Periodicals</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg" id="format_msg"></small>
                                    </div>
                                    <div class='form-group collection-input' id="form_group_collection_file">
                                        <label for="collection_file">Excel file <span
                                            class="badge badge-count text-danger">Required</span></label>
                                        <input type="file" class="form-control-file file" id="collection-file"
                                            name="collection_file">
                                        <small class="form-text text-muted text-danger input_msg" id="collection_file_msg"></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block"
                                            id="collection-modal-button" disabled>
                                            Import Collections Excel
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="nav-copies" role="tabpanel" aria-labelledby="nav-copies-tab">
                                <div class="alert alert-primary mt-3" role="alert">
                                    <b>Note: The excel file's first row or headers should include something like the
                                        following, it is important to note that the columns can be in any order, allowing
                                        for a more flexible import process.
                                    </b><br><br>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Call Number</th>
                                                    <th scope="col">Title</th>
                                                    <th scope="col">Authors</th>
                                                    <th scope="col">Barcode</th>
                                                    <th scope="col">Fund</th>
                                                    <th scope="col">Vendor</th>
                                                    <th scope="col">Price</th>
                                                    <th scope="col">Date Acquired</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>F 001.3 T161 2023</td>
                                                    <td>The Mystery of the Hidden Treasure</td>
                                                    <td>Doe, John;Smith, Jane</td>
                                                    <td>12345</td>
                                                    <td>Donated</td>
                                                    <td>Vendor sample</td>
                                                    <td>450.00</td>
                                                    <td>2023-12-30</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br>
                                    Multivalued inputs like <b>Authors</b> should be separated by ';'
                                    or semicolon.<br><br>
                                    Required column is <b>Call Number, Title & Barcode.</b><br><br>
                                    Optional inputs left blank if provided in Excel.
                                </div>
                                <form id="import-copies-form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group" id="form_group_fund">
                                        <label for="fund">Fund </label>
                                        <input type="text" class="form-control p-0" id="fund" name="fund"
                                            maxlength=30>
                                        <small class="form-text text-muted text-danger input_msg" id="fund_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_vendor">
                                        <label for="vendor">Vendor </label>
                                        <input type="text" class="form-control p-0" id="vendor" name="vendor"
                                            maxlength=30>
                                        <small class="form-text text-muted text-danger input_msg" id="vendor_msg"></small>
                                    </div>
                                    <div class='form-group copy-input' id="form_group_copy_file">
                                        <label for="copy_file">Excel file <span
                                            class="badge badge-count text-danger">Required</span></label>
                                        <input type="file" class="form-control-file file" id="copy-file"
                                            name="copy_file">
                                        <small class="form-text text-muted text-danger input_msg" id="copy_file_msg"></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block"
                                            id="copy-modal-button" disabled>
                                            Import Copies Excel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.imports.index')
@endsection
