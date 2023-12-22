@extends('layout.app')
@section('title', 'GoogleBooks API')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                GoogleBooks API
                            </h4>
                            <ul class="breadcrumbs">
                                <li class="nav-home">
                                    <a href="{{ route('dashboard.index') }}">
                                        <i class="flaticon-home"></i>
                                    </a>
                                </li>
                                <li class="separator">
                                    <i class="flaticon-right-arrow"></i>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('collections.index') }}">Collections</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                Books From GoogleBooks API
                                            </h4>
                                            <div class="float-right">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <div class="input-group m-0 mb-3 row">
                                                <input type="text" class="form-control col-md-8" placeholder="Search"
                                                    aria-label="Search" aria-describedby="button-addon2"
                                                    id="search_keyword">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon3">Search by</span>
                                                </div>
                                                <select class="form-control col-md-2" id="search_by">
                                                    <option value="intitle">Title</option>
                                                    <option value="inauthor">Author</option>
                                                    <option value="inauthor">Publisher</option>
                                                    <option value="isbn">ISBN</option>
                                                </select>
                                                <button class="btn btn-primary col-md-2" type="button"
                                                    id="btn-search">Search</button>
                                            </div>

                                            <table class="display table table-striped table-hover w-100"
                                                id="table-books">
                                                <thead>
                                                    <th>ISBN</th>
                                                    <th>Title</th>
                                                    <th>Author</th>
                                                    <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>ISBN</th>
                                                        <th>Title</th>
                                                        <th>Author</th>
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

        {{-- GOOGLE BOOKS API MODAL --}}
        <div class="modal fade pr-0" id="collection-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="collection-modal-header">
                            Add Collection
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="collection-form" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-6">
                                    @csrf
                                    <input type="hidden" id="collection-form-action">
                                    <input type="hidden" id="collection-hidden-id" name="old_id">
                                    <div class="form-group" id="form_group_format">
                                        <label for="format">Format</label>
                                        <select class="form-control collection-input" id="format" name="format">
                                            @foreach ($formats as $format)
                                                <option value="{{ $format }}">{{ $format }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted text-danger input_msg" id="format_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_authors">
                                        <label for="authors">Authors <span class="badge badge-count">Max: 5</span></label>
                                        <input type="text" class="form-control patron-input p-0" id="authors"
                                            name="authors" placeholder="">
                                        <small class="form-text text-muted text-danger input_msg" id="authors_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_title">
                                        <label for="title">Title <span
                                            class="badge badge-count text-danger">Required</span></label>
                                        <input type="text" class="form-control collection-input" id="title"
                                            name="title" maxlength=50>
                                        <small class="form-text text-muted text-danger input_msg" id="title_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_subtitles">
                                        <label for="subtitles">Subtitles <span class="badge badge-count">Max: 5</span>
                                        </label>
                                        <input type="text" class="form-control collection-input p-0" id="subtitles"
                                            name="subtitles">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="subtitles_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_edition">
                                        <label for="edition">Edition </label>
                                        <input type="text" class="form-control collection-input" id="edition"
                                            name="edition" maxlength=30>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="edition_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_series_title">
                                        <label for="series_title">Series title </label>
                                        <input type="text" class="form-control collection-input" id="series_title"
                                            name="series_title" maxlength=30>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="series_title_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_isbn">
                                        <label for="isbn">ISBN</label>
                                        <input type="text" class="form-control collection-input" id="isbn"
                                            name="isbn" maxlength=13>
                                        <small class="form-text text-muted text-danger input_msg" id="isbn_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_publication_place">
                                        <label for="publication_place">Publication place </label>
                                        <input type="text" class="form-control collection-input"
                                            id="publication_place" name="publication_place" maxlength=30>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="publication_place_msg"></small>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group" id="form_group_publisher">
                                        <label for="publisher">Publisher </label>
                                        <input type="text" class="form-control collection-input" id="publisher"
                                            name="publisher" maxlength=30>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="publisher_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_copyright_year">
                                        <label for="copyright_year">Copyright Year </label>
                                        <input type="text" class="form-control collection-input" id="copyright_year"
                                            name="copyright_year" maxlength=4>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="copyright_year_msg"></small>
                                    </div>
                                    <div class="form-group pt-0" id="form_group_physical_description">
                                        <label for="physical_description">Physical Description </label>
                                        <textarea type="text" class="form-control collection-input" id="physical_description" name="physical_description"
                                            rows="4" maxlength=100></textarea>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="physical_description_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_subjects">
                                        <label for="subjects">Subjects <span class="badge badge-count">Max:
                                                5</span></label>
                                        <input type="text" class="form-control collection-input p-0" id="subjects"
                                            name="subjects">
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="subjects_msg"></small>
                                    </div>
                                    <div class="form-group" id="form_group_call_number">
                                        <label for="call_number">Call Number</label>
                                        <div class="input-group">
                                            <select class="form-control" name="call_prefix" id="call_prefix" placeholder="Prefix">
                                                <option value=""></option>
                                                @foreach ($prefixes as $prefix)
                                                    <option value="{{ $prefix }}">{{ $prefix }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control" name="call_main" id="call_main" placeholder="Main" maxlength=10>
                                            <input type="text" class="form-control" name="call_cutter" placeholder="Cutter"
                                                id="call_cutter" maxlength=10>
                                            <input type="text" class="form-control" name="call_suffix" placeholder="Suffix"
                                                id="call_suffix" maxlength=10>
                                        </div>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="call_prefix_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="call_main_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="call_cutter_msg"></small>
                                        <small class="form-text text-muted text-danger input_msg"
                                            id="call_suffix_msg"></small>
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
                                    Add Collection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.google-books-api.index')
@endsection
