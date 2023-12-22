@extends('layout.app')

@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (Route::currentRouteName() === 'materials.manage')
                                    Manage Library Materials
                                @elseif (Route::currentRouteName() === 'materials.archive')
                                    Library Materials Archive
                                @else
                                    Library Materials
                                @endif
                            </h4>
                            <ul class="breadcrumbs">
                                <li class="nav-home">
                                    <a href="#">
                                        <i class="flaticon-home"></i>
                                    </a>
                                </li>
                                <li class="separator">
                                    <i class="flaticon-right-arrow"></i>
                                </li>
                                <li class="nav-item">
                                    <a href="#">Tables</a>
                                </li>
                                <li class="separator">
                                    <i class="flaticon-right-arrow"></i>
                                </li>
                                <li class="nav-item">
                                    <a href="#">Datatables</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                All materials
                                            </h4>
                                            <div class="float-right">
                                                @if (Route::currentRouteName() === 'materials.manage')
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="material-delete-all" disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Delete all
                                                        <span class="material-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#material-modal" id="material-add">
                                                        <i class="fa fa-plus"></i>
                                                        Add material
                                                    </button>
                                                @elseif (Route::currentRouteName() === 'materials.archive')
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="material-force-delete-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Force delete all
                                                        <span class="material-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="material-restore-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Restore all
                                                        <span class="material-count"></span>
                                                    </button>
                                                @endif

                                                <div class="btn-group dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                                        id="dropdownMenu1" data-toggle="dropdown">
                                                        <i class="fa fa-filter"></i>
                                                        Filter by roles
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
                                                        <a class="dropdown-item filter-item" data-val=""
                                                            href="#">Show
                                                            all</a>
                                                        <a class="dropdown-item filter-item" data-val="admin"
                                                            href="#">Admin</a>
                                                        <a class="dropdown-item filter-item" data-val="librarian"
                                                            href="#">Librarian</a>
                                                        <a class="dropdown-item filter-item" data-val="faculty"
                                                            href="#">Faculty</a>
                                                        <a class="dropdown-item filter-item" data-val="student"
                                                            href="#">Student</a>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover" id="table-materials">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Author</th>
                                                        <th>Type</th>
                                                        <th>Availability</th>
                                                        <th style="width: 10%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Author</th>
                                                        <th>Type</th>
                                                        <th>Availability</th>
                                                        <th style="width: 10%">Action</th>
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
        <div class="modal fade pr-0" id="material-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="material-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="material-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="material-form-action">
                            <input type="hidden" id="material-hidden-id" name="old_id">
                            <div class="form-group" id="form_group_name">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                                <small class="form-text text-muted text-danger input_msg" id="name_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_barcode">
                                <label for="barcode">Barcode</label>
                                <input type="text" class="form-control" id="barcode" name="barcode">
                                <small class="form-text text-muted text-danger input_msg" id="barcode_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_subtitle">
                                <label for="author">Author <span class="badge badge-count">Optional</span></label>
                                <input type="text" class="form-control" id="author" name="author">
                                <small class="form-text text-muted text-danger input_msg" id="author_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_type">
                                <label for="type">Type</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="others">Others</option>
                                    <option value="periodicals">Periodicals</option>
                                    <option value="audiovisual">Audiovisual materials</option>
                                    <option value="ebooks">E-books and digital resources</option>
                                    <option value="manuscripts">Manuscripts and archives</option>
                                    <option value="maps">Maps and atlases</option>
                                    <option value="artifacts">Art and artifacts</option>
                                    <option value="govpubs">Government publications</option>
                                    <option value="reference">Reference resources</option>
                                    <option value="specialcollections">Special collections</option>
                                    <option value="theses">Theses and dissertations</option>
                                    <option value="microforms">Microforms</option>
                                </select>
                                <small class="form-text text-muted text-danger input_msg" id="type_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_quantity">
                                <label for="quantity">Quantity</label>
                                <input type="number" value="1" class="form-control" id="quantity"
                                    name="quantity">
                                <small class="form-text text-muted text-danger input_msg" id="quantity_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_quantity">
                                <label for="available_quantity">Available Quantity</label>
                                <input type="number" value="1" class="form-control" id="available_quantity"
                                    name="available_quantity">
                                <small class="form-text text-muted text-danger input_msg"
                                    id="available_quantity_msg"></small>
                            </div>
                            <div class="form-group pt-0" id="form_group_note">
                                <label for="note">Note <span class="badge badge-count">Optional</span></label>
                                <textarea type="text" class="form-control" id="note" name="note"></textarea>
                                <small class="form-text text-muted text-danger input_msg" id="note_msg"></small>
                            </div>
                            <div class="form-group pt-0" id="form_group_description">
                                <label for="description">Description <span
                                        class="badge badge-count">Optional</span></label>
                                <textarea type="text" class="form-control" id="description" name="description"></textarea>
                                <small class="form-text text-muted text-danger input_msg" id="description_msg"></small>
                            </div>
                            <div class='form-group' id="form_group_image">
                                <label for="image">Displayed picture
                                    <span class="badge badge-count">Optional</span>
                                </label>
                                <input type="file" class="form-control-file" id="image" name="image">
                                <div id="image-container"></div>
                                <small class="form-text text-muted text-danger input_msg" id="image_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="material-modal-button">

                                </button>
                            </div>
                        </form>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade pr-0" id="reservation-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="cart-modal-header">
                            Reserve Library Material
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="reservation-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="reservation-hidden-id" name="id">
                            <div class="form-group" id="form_group_cancel_by">
                                <label for="name">Cancel reservation by <span
                                        class="badge badge-count">Optional</span></label>
                                <input type="datetime-local" class="form-control cart-input" id="cancel_by"
                                    name="cancel_by">
                                <small class="form-text text-muted text-danger input_msg" id="cancel_by_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="cart-modal-button">
                                    Send reservation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.materials.index')
@endsection
