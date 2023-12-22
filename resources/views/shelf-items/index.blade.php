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
                                Shelf Items
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
                                                All Shelf Items
                                            </h4>
                                            <div class="float-right">
                                                <button class="btn btn-primary " data-toggle="modal" id="shelf-item-remove-all"
                                                    disabled>
                                                    <i class="fa-solid fa-xmark"></i>
                                                     Remove all
                                                    <span class="shelf-item-count"></span>
                                                </button>
                                                <button class="btn btn-primary " data-toggle="modal" id="shelf-item-reserve-all"
                                                    disabled>
                                                    <i class="fa-solid fa-clipboard"></i>
                                                     Reserve all
                                                    <span class="shelf-item-count"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100" id="table-shelf-items">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Barcode</th>
                                                        <th>Title</th>
                                                        <th>Availability</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Barcode</th>
                                                        <th>Title</th>
                                                        <th>Availability</th>
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
        <div class="modal fade pr-0" id="collection-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="collection-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="collection-form" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <input type="hidden" id="collection-form-action">
                                    <input type="hidden" id="collection-hidden-id" name="old_id">
                                    <div class="form-group" id="form_group_barcode">
                                        <label for="barcode">Barcode</label>
                                        <input type="text" class="form-control collection-input" id="barcode"
                                            name="barcode">
                                        <small class="form-text text-muted text-danger input_msg" id="barcode_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_type">
                                        <label for="type">Type</label>
                                        <select class="form-control collection-input" id="type" name="type">
                                            <option value="Books">Books</option>
                                            <option value="Periodicals">Periodicals</option>
                                            <option value="Audiovisual Materials">Audiovisual Materials</option>
                                            <option value="E-books and Digital Resources">E-books and Digital Resources</option>
                                            <option value="Manuscripts and Archives">Manuscripts and Archives</option>
                                            <option value="Maps and Atlases">Maps and Atlases</option>
                                            <option value="Art and Artifacts">Art and Artifacts</option>
                                            <option value="Government Publications">Government Publications</option>
                                            <option value="Reference Resources">Reference Resources</option>
                                            <option value="Special Collections">Special Collections</option>
                                            <option value="Theses and Dissertations">Theses and Dissertations</option>
                                            <option value="Microforms">Microforms</option>
                                            <option value="Others">Others</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg" id="type_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_authors">
                                        <label for="authors">Authors <span
                                                class="badge badge-count">Optional</span></label>
                                        <input type="text" class="form-control collection-input" id="authors"
                                            name="authors" data-role="tagsinput">
                                        <small class="form-text text-muted text-danger input_msg" id="authors_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_title">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control collection-input" id="title"
                                            name="title">
                                        <small class="form-text text-muted text-danger input_msg" id="title_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_subtitles">
                                        <label for="subtitles">Subtitles <span
                                                class="badge badge-count">Optional</span></label>
                                        <input type="text" class="form-control collection-input" id="subtitles"
                                            name="subtitles" data-role="tagsinput">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="subtitles_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_edition">
                                        <label for="edition">Edition <span
                                                class="badge badge-count">Optional</span></label>
                                        <input type="number" class="form-control collection-input" id="edition"
                                            name="edition">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="edition_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_series_title">
                                        <label for="series_title">Series title <span
                                                class="badge badge-count">Optional</span></label>
                                        <input type="text" class="form-control collection-input" id="series_title"
                                            name="series_title">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="series_title_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_isbn">
                                        <label for="isbn">ISBN</label>
                                        <input type="number" class="form-control collection-input" id="isbn"
                                            name="isbn">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="isbn_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_publication_place">
                                        <label for="publication_place">Publication place <span
                                                class="badge badge-count">Optional</span></label>
                                        <input type="text" class="form-control collection-input" id="publication_place"
                                            name="publication_place">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="publication_place_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_publisher">
                                        <label for="publisher">Publisher <span
                                                class="badge badge-count">Optional</span></label>
                                        <input type="text" class="form-control collection-input" id="publisher"
                                            name="publisher">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="publisher_msg"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="form_group_copyright_year">
                                        <label for="copyright_year">Copyright Year <span
                                                class="badge badge-count">Optional</span></label>
                                        <input type="text" class="form-control collection-input" id="copyright_year"
                                            name="copyright_year">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="copyright_year_msg"></small>
                                    </div>
                                    <div class="form-group pt-0" id="form_group_physical_description">
                                        <label for="physical_description">Physical Description <span class="badge badge-count">Optional</span></label>
                                        <textarea type="text" class="form-control collection-input" id="physical_description" name="physical_description" rows="4"></textarea>
                                        <small class="form-text text-muted text-danger input_msg" id="physical_description_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_subjects">
                                        <label for="subjects">Subjects <span
                                                class="badge badge-count">Optional</span></label>
                                        <input type="text" class="form-control collection-input" id="subjects"
                                            name="subjects" data-role="tagsinput">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="subjects_msg"></small>
                                    </div>
                                    <div class="form-group pt-0" id="form_group_note">
                                        <label for="note">Note <span class="badge badge-count">Optional</span></label>
                                        <textarea type="text" class="form-control collection-input" id="note" name="note" rows="4"></textarea>
                                        <small class="form-text text-muted text-danger input_msg" id="note_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_copy">
                                        <label for="copy">Copy</label>
                                        <input type="text" class="form-control collection-input" id="copy"
                                            name="copy" value="1">
                                        <small class="form-text text-muted text-danger input_msg" id="copy_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_call_number">
                                        <label for="call_number">Call Number</label>
                                        <input type="text" class="form-control collection-input" id="call_number"
                                            name="call_number">
                                        <small class="form-text text-muted text-danger input_msg" id="call_number_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_amount">
                                        <label for="amount">Amount</label>
                                        <input type="text" value="0.00" class="form-control collection-input amount"
                                            id="amount" name="amount">
                                        <small class="form-text text-muted text-danger input_msg" id="amount_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_acquisition_method">
                                        <label for="acquisition_method">Acquisition Method</label>
                                        <select class="form-control collection-input" id="acquisition_method"
                                            name="acquisition_method">
                                            <option value="donated">Donated</option>
                                            <option value="purchased">Purchased</option>
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="acquisition_method_msg"></small>
                                    </div>

                                    <div class='form-group' id="form_group_image">
                                        <label for="image">Displayed picture
                                            <span class="badge badge-count">Optional</span></label>
                                        </label>
                                        <input type="file" class="form-control-file" id="image" name="image">
                                        <div id="image-container"></div>
                                        <small class="form-text text-muted text-danger input_msg" id="image_msg"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="collection-modal-button">

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade pr-0" id="reservation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="" id="collection-modal-header">
                        Request for Reservation
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="reservation-form" enctype="multipart/form-data">
                        <div class="form-group" id="form_group_cancel_by">
                            <label for="cancel_by">Automatically cancel by: <span
                                    class="badge badge-count">Optional</span></label>
                            <input type="date" class="form-control collection-input" id="cancel_by"
                                name="cancel_by" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}">
                            <small class="form-text text-muted text-danger input_msg"
                                id="cancel_by_msg"></small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary" id="collection-modal-button">
                                Send Reservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.shelf-items.index')
@endsection
