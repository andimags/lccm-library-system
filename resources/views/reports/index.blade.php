@extends('layout.app')
@section('title', Route::currentRouteName() == 'reports.index' ? 'Reports' : 'Reports Archive')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'reports.index')
                                    Reports
                                @else
                                    Reports Archive
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
                                                All reports
                                            </h4>
                                            <div class="float-right">
                                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'reports.index')
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="report-delete-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Delete all
                                                        <span class="report-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#report-modal" id="report-add">
                                                        <i class="fa fa-plus"></i>
                                                        Add report
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="report-force-delete-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Force delete all
                                                        <span class="report-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="report-restore-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Restore all
                                                        <span class="report-count"></span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table w-100 table-striped table-hover" id="table-reports">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Report Type</th>
                                                        <th>File Type</th>
                                                        <th>Librarian</th>
                                                        <th>Created at</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Report Type</th>
                                                        <th>File Type</th>
                                                        <th>Librarian</th>
                                                        <th>Created at</th>
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
        <div class="modal fade pr-0" id="report-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="report-modal-header">
                            Add Report
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
                                <a class="nav-item nav-link" id="nav-off-site-circulations-tab" data-toggle="tab"
                                    href="#nav-off-site-circulations" role="tab"
                                    aria-controls="nav-off-site-circulations" aria-selected="false">Off-Site
                                    Circulations</a>
                                <a class="nav-item nav-link" id="nav-in-house-circulations-tab" data-toggle="tab"
                                    href="#nav-in-house-circulations" role="tab"
                                    aria-controls="nav-in-house-circulations" aria-selected="false">In-House
                                    Circulations</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-patrons" role="tabpanel"
                                aria-labelledby="nav-patrons-tab">
                                <form id="patrons-list-form">
                                    @csrf
                                    <div class="form-group form_group_fields">
                                        <label class="form-label">Fields</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" value="id2"
                                                    class="selectgroup-input" checked="">
                                                <span class="selectgroup-button">ID</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" value="first_name"
                                                    class="selectgroup-input" checked="">
                                                <span class="selectgroup-button">First Name</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" value="last_name"
                                                    class="selectgroup-input" checked="">
                                                <span class="selectgroup-button">Last Name</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" value="email"
                                                    class="selectgroup-input" checked="">
                                                <span class="selectgroup-button">Email</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" value="roles"
                                                    class="selectgroup-input" checked="">
                                                <span class="selectgroup-button">Roles</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" value="groups"
                                                    class="selectgroup-input" checked="">
                                                <span class="selectgroup-button">Groups</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" value="created_at"
                                                    class="selectgroup-input" checked="">
                                                <span class="selectgroup-button">Created at</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" value="total_unpaid_fines"
                                                    class="selectgroup-input" checked="">
                                                <span class="selectgroup-button">Total Unpaid Fines</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg fields_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_report_type">
                                        <label for="report_type">Report type</label>
                                        <select class="form-control" name="report_type">
                                            <option value="Patrons List">Patrons List</option>
                                            <option value="Patron Registrations List">Patron Registrations List</option>
                                            <option value="Delinquent Patrons List">Delinquent Patrons List</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="report_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_roles">
                                        <label for="roles">Role Select</label>
                                        <select class="form-control" name="roles">
                                            <option value="all">All</option>
                                            <option value="student">Student</option>
                                            <option value="faculty">Faculty</option>
                                            <option value="employee">Employee</option>
                                            <option value="librarian">Librarian</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg" id="roles_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_sort_by">
                                        <label for="sort_by">Sort by</label>
                                        <div class="input-group">
                                            <select class="form-control" name="sort_by">
                                                <option value="id2">ID</option>
                                                <option value="first_name">First name</option>
                                                <option value="last_name">Last name</option>
                                                <option value="email">Email</option>
                                                <option value="roles">Roles</option>
                                                <option value="created_at" selected>Created at</option>
                                            </select>
                                            <select class="form-control" name="sort_order">
                                                <option value="asc">Ascending</option>
                                                <option value="desc">Descending</option>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_by_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_order_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_file_type">
                                        <label for="file_type">File Type</label>
                                        <select class="form-control" name="file_type">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="file_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_limit">
                                        <label for="limit">Limit Rows </label>
                                        <input type="number" class="form-control copy-input limit" id="limit"
                                            name="limit">
                                        <small class="form-text text-muted text-danger input_msg" id="limit_msg"></small>
                                    </div>
                                    <div class="form-group form_group_created_at_start form_group_created_at_end">
                                        <label>Created At Range </label>
                                        <div class="input-group mb-3">
                                            <input type="date" class="form-control created_at_start"
                                                name="created_at_start">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">to</span>
                                            </div>
                                            <input type="date" class="form-control created_at_end"
                                                name="created_at_end">
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg created_at_start_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg created_at_end_msg"></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block submit-button">
                                            Add Patron Report
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="nav-collections" role="tabpanel"
                                aria-labelledby="nav-collections-tab">
                                <form id="collections-list-form">
                                    @csrf
                                    <div class="form-group form_group_fields">
                                        <label class="form-label">Fields</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="format" />
                                                <span class="selectgroup-button">Format</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="authors" />
                                                <span class="selectgroup-button">Authors</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="title" />
                                                <span class="selectgroup-button">Title</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="subtitles" />
                                                <span class="selectgroup-button">Subtitles</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="edition" />
                                                <span class="selectgroup-button">Edition</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="series_title" />
                                                <span class="selectgroup-button">Series Title</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="isbn" />
                                                <span class="selectgroup-button">ISBN</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="publication_place" />
                                                <span class="selectgroup-button">Publication Place</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="publisher" />
                                                <span class="selectgroup-button">Publisher</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="copyright_year" />
                                                <span class="selectgroup-button">Copyright Year</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="physical_description" />
                                                <span class="selectgroup-button">Physical Description</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="subjects" />
                                                <span class="selectgroup-button">Subjects</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="call_number" />
                                                <span class="selectgroup-button">Call Number</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="created_at" />
                                                <span class="selectgroup-button">Created At</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg fields_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_report_type">
                                        <label for="report_type">Report type</label>
                                        <select class="form-control" name="report_type">
                                            <option value="Collections List">Collections List</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="report_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_format">
                                        <label for="format">Format Select</label>
                                        <select class="form-control" name="format">
                                            <option value="all">All</option>
                                            @foreach ($formats as $format)
                                                <option value="{{ strtolower($format) }}">{{ $format }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg" id="format_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_sort_by">
                                        <label for="sort_by">Sort by</label>
                                        <div class="input-group">
                                            <select class="form-control" name="sort_by">
                                                <option value="format">Format</option>
                                                <option value="title">Title</option>
                                                <option value="edition">Edition</option>
                                                <option value="series_title">Series Title</option>
                                                <option value="isbn">ISBN</option>
                                                <option value="publication_place">Publication Place</option>
                                                <option value="publisher">Publisher</option>
                                                <option value="copyright_year">Copyright Year</option>
                                                <option value="physical_description">Physical Description</option>
                                                <option value="created_at" selected>Created At</option>
                                            </select>
                                            <select class="form-control" name="sort_order">
                                                <option value="asc">Ascending</option>
                                                <option value="desc">Descending</option>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_by_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_order_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_file_type">
                                        <label for="file_type">File Type</label>
                                        <select class="form-control" name="file_type">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="file_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_limit">
                                        <label for="limit">Limit Rows </label>
                                        <input type="number" class="form-control copy-input limit" id="limit"
                                            name="limit">
                                        <small class="form-text text-muted text-danger input_msg" id="limit_msg"></small>
                                    </div>
                                    <div class="form-group ">
                                        <label>Created At Range </label>
                                        <div class="input-group mb-3 form_group_created_at_start form_group_created_at_end">
                                            <input type="date" class="form-control created_at_start"
                                                name="created_at_start">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">to</span>
                                            </div>
                                            <input type="date" class="form-control created_at_end"
                                                name="created_at_end">
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg created_at_start_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg created_at_end_msg"></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block submit-button">
                                            Add Collection Report
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="nav-copies" role="tabpanel" aria-labelledby="nav-copies-tab">
                                <form id="copies-list-form">
                                    <div class="form-group form_group_fields">
                                        <label class="form-label">Fields</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="title" />
                                                <span class="selectgroup-button">Title</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="authors" />
                                                <span class="selectgroup-button">Authors</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="barcode" />
                                                <span class="selectgroup-button">Barcode</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="price" />
                                                <span class="selectgroup-button">Price</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="fund" />
                                                <span class="selectgroup-button">Fund</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="vendor" />
                                                <span class="selectgroup-button">Vendor</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="call_number" />
                                                <span class="selectgroup-button">Call Number</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="availability" />
                                                <span class="selectgroup-button">Availability</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="acquired_at" />
                                                <span class="selectgroup-button">Acquired At</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="created_at" />
                                                <span class="selectgroup-button">Created At</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg fields_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_report_type">
                                        <label for="report_type">Report type</label>
                                        <select class="form-control" name="report_type">
                                            <option value="Copies_List">Copies List</option>
                                            <option value="Available_Copies_List">Available Copies List</option>
                                            <option value="On_Loan_Copies_List">On Loan Copies List</option>
                                            <option value="Reserved_Copies_List">Reserved Copies List</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="report_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_sort_by">
                                        <label for="sort_by">Sort by</label>
                                        <div class="input-group">
                                            <select class="form-control" name="sort_by">
                                                <option value="title">Title</option>
                                                <option value="barcode">Barcode</option>
                                                <option value="price">Price</option>
                                                <option value="fund">Fund</option>
                                                <option value="vendor">Vendor</option>
                                                <option value="call_number">Call Number</option>
                                                <option value="acquired_at">Acquired At</option>
                                                <option value="created_at" selected>Created At</option>
                                            </select>
                                            <select class="form-control" name="sort_order">
                                                <option value="asc">
                                                    Ascending
                                                </option>
                                                <option value="desc">
                                                    Descending
                                                </option>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_by_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_order_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_file_type">
                                        <label for="file_type">File Type</label>
                                        <select class="form-control" name="file_type">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="file_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_limit">
                                        <label for="limit">Limit Rows
                                            </label>
                                        <input type="number" class="form-control copy-input limit" id="limit"
                                            name="limit" />
                                        <small class="form-text text-muted text-danger input_msg" id="limit_msg"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Acquired At Range
                                            </label>
                                        <div class="input-group mb-3 form_group_acquired_at_start form_group_acquired_at_end">
                                            <input type="date" class="form-control acquired_at_start"
                                                name="acquired_at_start" />
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">to</span>
                                            </div>
                                            <input type="date" class="form-control acquired_at_end"
                                                name="acquired_at_end" />
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg acquired_at_start_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg acquired_at_end_msg"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Created At Range
                                            </label>
                                        <div class="input-group mb-3 form_group_created_at_start form_group_created_at_end">
                                            <input type="date" class="form-control created_at_start"
                                                name="created_at_start"/>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">to</span>
                                            </div>
                                            <input type="date" class="form-control created_at_end"
                                                name="created_at_end" />
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg created_at_start_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg created_at_end_msg"></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block submit-button">
                                            Add Report
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="nav-off-site-circulations" role="tabpanel"
                                aria-labelledby="nav-off-site-circulations-tab">
                                <form id="off-site-circulations-list-form">
                                    <div class="form-group form_group_fields">
                                        <label class="form-label">Fields</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="title" />
                                                <span class="selectgroup-button">Title</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="barcode" />
                                                <span class="selectgroup-button">Barcode</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="checked_out_at" />
                                                <span class="selectgroup-button">Checked-Out At</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="checked_in_at" />
                                                <span class="selectgroup-button">Checked-In At</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="due_at" />
                                                <span class="selectgroup-button">Due At</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="fines_status" />
                                                <span class="selectgroup-button">Fines Status</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="total_fines" />
                                                <span class="selectgroup-button">Total Fines</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="borrower" />
                                                <span class="selectgroup-button">Borrower</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="created_at" />
                                                <span class="selectgroup-button">Created At</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg fields_msg"></small>
                                    </div>
                                    <div class="form-group form_group_report_type">
                                        <label for="report_type">Report type</label>
                                        <select class="form-control" name="report_type">
                                            <option value="Off-Site_Circulations_List">Off-Site Circulations List</option>
                                            <option value="On_Loan_Off-Site_Circulations_List">On Loan Off-Site
                                                Circulations List</option>
                                            <option value="Outstanding_Off-Site_Circulations_List">Outstanding Off-Site
                                                Circulations List</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="report_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_sort_by">
                                        <label for="sort_by">Sort by</label>
                                        <div class="input-group">
                                            <select class="form-control" name="sort_by">
                                                <option value="checked_out_at">Checked-Out At</option>
                                                <option value="checked_in_at">Checked-In At</option>
                                                <option value="due_at">Due At</option>
                                                <option value="fines_status">Fines Status</option>
                                                <option value="total_fines">Total Fines</option>
                                                <option value="created_at" selected>Created At</option>
                                            </select>
                                            <select class="form-control" name="sort_order">
                                                <option value="asc">
                                                    Ascending
                                                </option>
                                                <option value="desc">
                                                    Descending
                                                </option>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_by_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_order_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_file_type">
                                        <label for="file_type">File Type</label>
                                        <select class="form-control" name="file_type">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="file_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_limit">
                                        <label for="limit">Limit Rows
                                            </label>
                                        <input type="number" class="form-control copy-input limit" id="limit"
                                            name="limit" />
                                        <small class="form-text text-muted text-danger input_msg" id="limit_msg"></small>
                                    </div>
                                    <div class="form-group form_group_created_at_start form_group_created_at_end">
                                        <label>Created At Range
                                            </label>
                                        <div class="input-group mb-3">
                                            <input type="date" class="form-control created_at_start"
                                                name="created_at_start" />
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">to</span>
                                            </div>
                                            <input type="date" class="form-control created_at_end"
                                                name="created_at_end" />
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg created_at_start_msg"
                                            ></small>
                                        <small class="form-text text-muted text-danger input_msg created_at_end_msg"
                                            ></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block submit-button">
                                            Add Report
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="nav-in-house-circulations" role="tabpanel"
                                aria-labelledby="nav-in-house-circulations-tab">
                                <form id="in-house-circulations-list-form">
                                    <div class="form-group form_group_fields">
                                        <label class="form-label">Fields</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="title" />
                                                <span class="selectgroup-button">Title</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="barcode" />
                                                <span class="selectgroup-button">Barcode</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="librarian" />
                                                <span class="selectgroup-button">Librarian</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="fields[]" class="selectgroup-input"
                                                    checked="" value="created_at" />
                                                <span class="selectgroup-button">Created At</span>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg fields_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_report_type">
                                        <label for="report_type">Report type</label>
                                        <select class="form-control" name="report_type">
                                            <option value="In-House_Circulations_List">In-House Circulations List</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="report_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_sort_by">
                                        <label for="sort_by">Sort by</label>
                                        <div class="input-group">
                                            <select class="form-control" name="sort_by">
                                                <option value="created_at" selected>Created At</option>
                                            </select>
                                            <select class="form-control" name="sort_order">
                                                <option value="asc">
                                                    Ascending
                                                </option>
                                                <option value="desc">
                                                    Descending
                                                </option>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_by_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="sort_order_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_file_type">
                                        <label for="file_type">File Type</label>
                                        <select class="form-control" name="file_type">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="file_type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_limit">
                                        <label for="limit">Limit Rows
                                            </label>
                                        <input type="number" class="form-control copy-input limit" id="limit"
                                            name="limit" />
                                        <small class="form-text text-muted text-danger input_msg" id="limit_msg"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Created At Range
                                            </label>
                                        <div class="input-group mb-3 form_group_created_at_end form_group_created_at_start">
                                            <input type="date" class="form-control created_at_start"
                                                name="created_at_start" />
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">to</span>
                                            </div>
                                            <input type="date" class="form-control created_at_end"
                                                name="created_at_end" />
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg created_at_start_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg created_at_end_msg"></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-block submit-button">
                                            Add Report
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
    @include('js.reports.index')
@endsection
