@extends('layout.app')
@section('title', Route::currentRouteName() == 'announcements.index' ? 'Announcements' : 'Announcements Archive')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">
                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'announcements.index')
                                    Announcements
                                @else
                                    Announcements Archive
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
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                All Announcements
                                            </h4>
                                            <div class="float-right">
                                                @if (\Illuminate\Support\Facades\Route::currentRouteName() == 'announcements.index')
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="announcement-delete-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Delete all
                                                        <span class="announcement-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                    data-target="#announcement-modal" id="announcement-add">
                                                        <i class="fa fa-plus"></i>
                                                        Add Announcement
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="announcement-force-delete-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Force delete all
                                                        <span class="announcement-count"></span>
                                                    </button>
                                                    <button class="btn btn-primary " data-toggle="modal"
                                                        data-target="#exampleModalCenter" id="announcement-restore-all"
                                                        disabled>
                                                        <i class="fa fa-trash"></i>
                                                        Restore all
                                                        <span class="announcement-count"></span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-hover w-100 nowrap"
                                                id="table-announcements">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Visibility</th>
                                                        <th>Start at</th>
                                                        <th>End at</th>
                                                        <th>Librarian</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Title</th>
                                                        <th>Visibility</th>
                                                        <th>Start at</th>
                                                        <th>End at</th>
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
        <div class="modal fade pr-0" id="announcement-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" id="announcement-modal-header">

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="announcement-form" enctype="multipart/form-data">
                            @csrf
                            <input type="text" id="announcement-form-action" hidden>
                            <input type="text" id="announcement-hidden-id" name="id" hidden>
                            <div class="form-group" id="form_group_title">
                                <label for="title">Title <span
                                    class="badge badge-count text-danger">Required</span></label>
                                <input type="text" class="form-control announcement-input" id="title" name="title"
                                    maxlength=30>
                                <small class="form-text text-muted text-danger input_msg" id="title_msg"></small>
                            </div>
                            <div class="form-group pt-0" id="form_group_content">
                                <label for="content">Content </label>
                                <textarea type="text" class="form-control collection-input" id="content" name="content" rows="4"></textarea>
                                <small class="form-text text-muted text-danger input_msg" id="content_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_visibility">
                                <label for="visibility">Visibility <span
                                    class="badge badge-count text-danger">Required</span></label>
                                <select class="form-control announcement-input" id="visibility" name="visibility">
                                    <option value="all">All</option>
                                    <option value="librarian">Librarian</option>
                                    <option value="faculty">Faculty</option>
                                    <option value="employee">Employee</option>
                                    <option value="student">Student</option>
                                </select>
                                <small class="form-text text-muted text-danger input_msg" id="visibility_msg"></small>
                            </div>
                            <div class="form-group" id="form_group_end_at">
                                <label>Date Range <span
                                    class="badge badge-count text-danger">Required</span></label>
                                <div class="input-group mb-3">
                                    <input type="date" class="form-control" id="start_at" name="start_at" min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">to</span>
                                    </div>
                                    <input type="date" class="form-control" id="end_at" name="end_at" min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <small class="form-text text-muted text-danger input_msg" id="start_at_msg"></small>
                                <small class="form-text text-muted text-danger input_msg" id="end_at_msg"></small>
                            </div>
                            <div class='form-group announcement-input' id="form_group_image">
                                <label for="image">Displayed picture
                                    <span class="badge badge-count">Max: 3 images</span></label>
                                </label>
                                <input type="file" class="form-control-file" id="image" name="image">
                                <div id="image-container"></div>
                                <small class="form-text text-muted text-danger input_msg" id="image_msg"></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-border" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" id="announcement-modal-button">

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ANNOUNCEMENT PREVIEW --}}
        <div class="modal fade pr-0" id="announcement-preview-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="">
                        Announcement Preview
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="h4" id="preview-title"></div>
                        <div id="preview-content"></div>

                        <div class="row mt-2" id="preview-images">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.announcements.index')
@endsection
