@extends('layout.app')
@section('title', 'Dashboard')
@section('content')
    <div class="wrapper">

        @include('layout.nav')

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">
                        <div class="page-header">
                            <h4 class="page-title">Dashboard</h4>
                        </div>

                        @foreach ($announcements as $announcement)
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div class="h4">{{ $announcement->title }}</div>
                                {{ $announcement->content }}

                                <div class="row mt-2">
                                    @foreach ($announcement->images as $image)
                                        <div class="col-lg-4">
                                            <a href="{{ asset('storage/images/announcements/' . $image->file_name) }}"><img
                                                    src="{{ asset('storage/images/announcements/' . $image->file_name) }}"
                                                    class="img-thumbnail w-100 overflow-hidden" alt="..."
                                                    style="max-height: 40vh; object-fit: cover;">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        @if (auth()->user()->temp_role == 'librarian')
                                            <div class="row">
                                                <div class="col-sm-6 col-md-3">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body ">
                                                            <div class="row align-items-center">
                                                                <div class="col-icon">
                                                                    <div
                                                                        class="icon-big text-center icon-info bubble-shadow-small">
                                                                        <i class="fa-solid fa-user-gear"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="col col-stats ml-3 ml-sm-0">
                                                                    <div class="numbers">
                                                                        <p class="card-category">Librarians</p>
                                                                        <h4 class="card-title">{{ $librarians }}</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-3">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body ">
                                                            <div class="row align-items-center">
                                                                <div class="col-icon">
                                                                    <div
                                                                        class="icon-big text-center icon-info bubble-shadow-small">
                                                                        <i class="fa-solid fa-user-clock"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="col col-stats ml-3 ml-sm-0">
                                                                    <div class="numbers">
                                                                        <p class="card-category">Employees</p>
                                                                        <h4 class="card-title">{{ $employees }}</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-3">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body ">
                                                            <div class="row align-items-center">
                                                                <div class="col-icon">
                                                                    <div
                                                                        class="icon-big text-center icon-info bubble-shadow-small">
                                                                        <i class="fa-solid fa-chalkboard-user"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="col col-stats ml-3 ml-sm-0">
                                                                    <div class="numbers">
                                                                        <p class="card-category">Faculties</p>
                                                                        <h4 class="card-title">{{ $faculties }}</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-3">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body ">
                                                            <div class="row align-items-center">
                                                                <div class="col-icon">
                                                                    <div
                                                                        class="icon-big text-center icon-info bubble-shadow-small">
                                                                        <i class="fa-solid fa-user-graduate"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="col col-stats ml-3 ml-sm-0">
                                                                    <div class="numbers">
                                                                        <p class="card-category">Students</p>
                                                                        <h4 class="card-title">{{ $students }}</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Card With Icon States Color -->
                                        <div class="row">
                                            <div class="col-sm-6 col-md-3">
                                                <div class="card card-stats card-round">
                                                    <div class="card-body ">
                                                        <div class="row align-items-center">
                                                            <div class="col-icon">
                                                                <div
                                                                    class="icon-big text-center icon-info bubble-shadow-small">
                                                                    <i class="fa-solid fa-arrows-rotate"></i>
                                                                </div>
                                                            </div>
                                                            <div class="col col-stats ml-3 ml-sm-0">
                                                                <div class="numbers">
                                                                    <p class="card-category">Total Circulations</p>
                                                                    <h4 class="card-title">{{ $totalCirculations }}</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="card card-stats card-round">
                                                    <div class="card-body ">
                                                        <div class="row align-items-center">
                                                            <div class="col-icon">
                                                                <div
                                                                    class="icon-big text-center icon-info bubble-shadow-small">
                                                                    <i class="fa-solid fa-peso-sign"></i>
                                                                </div>
                                                            </div>
                                                            <div class="col col-stats ml-3 ml-sm-0">
                                                                <div class="numbers">
                                                                    <p class="card-category">Total Unpaid Fines</p>
                                                                    <h4 class="card-title">₱ {{ $totalUnpaidFines }}</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="card card-stats card-round">
                                                    <div class="card-body ">
                                                        <div class="row align-items-center">
                                                            <div class="col-icon">
                                                                <div
                                                                    class="icon-big text-center icon-info bubble-shadow-small">
                                                                    <i class="fa-solid fa-book"></i>
                                                                </div>
                                                            </div>
                                                            <div class="col col-stats ml-3 ml-sm-0">
                                                                <div class="numbers">
                                                                    <p class="card-category">On Loan</p>
                                                                    <h4 class="card-title">{{ $totalOnLoans }}</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="card card-stats card-round">
                                                    <div class="card-body ">
                                                        <div class="row align-items-center">
                                                            <div class="col-icon">
                                                                <div
                                                                    class="icon-big text-center icon-info bubble-shadow-small">
                                                                    <i class="fa-regular fa-copy"></i>
                                                                </div>
                                                            </div>
                                                            <div class="col col-stats ml-3 ml-sm-0">
                                                                <div class="numbers">
                                                                    <p class="card-category">Reservations</p>
                                                                    <h4 class="card-title">{{ $totalReservations }}</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12 col-md-6">
                                                <div id="calendar" class=""></div>
                                            </div>
                                            @if (auth()->user()->temp_role == 'librarian')
                                                <div class="col-sm-12 col-md-6">
                                                    <h3>Usage Statistics</h3>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-nowrap">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" style="height: 30px"></th>
                                                                    <th scope="col" style="height: 30px">Renewals</th>
                                                                    <th scope="col" style="height: 30px">Reservations
                                                                    </th>
                                                                    <th scope="col" style="height: 30px">Off-site</th>
                                                                    <th scope="col" style="height: 30px">In-house</th>
                                                                    <th scope="col" style="height: 30px">Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($usageStatistics as $monthAndYear => $statistics)
                                                                    <tr>
                                                                        <td style="height: 30px">
                                                                            <strong>{{ $monthAndYear }}</strong>
                                                                        </td>
                                                                        <td style="height: 30px">{{ $statistics[0] }}</td>
                                                                        <!-- Renewals Count -->
                                                                        <td style="height: 30px">{{ $statistics[1] }}</td>
                                                                        <!-- Reservations Count -->
                                                                        <td style="height: 30px">{{ $statistics[2] }}</td>
                                                                        <!-- OffSite Count -->
                                                                        <td style="height: 30px">{{ $statistics[3] }}</td>
                                                                        <!-- InHouse Count -->
                                                                        <td style="height: 30px">{{ $statistics[4] }}</td>
                                                                        <!-- Total Count -->
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="card-title fw-mediumbold">New Collections</div>
                                                            <div class="card-list">
                                                                @foreach ($newCollections as $collection)
                                                                    <div class="item-list">
                                                                        <div class="avatar avatar-lg">
                                                                            <img src="{{ asset('storage/images/collections/' . ($collection->images->isNotEmpty() ? $collection->images->first()->file_name : 'default.jpg')) }}" alt="..." class="avatar-img rounded">
                                                                        </div>
                                                                        <div class="info-user ml-3">
                                                                            <div class="h5">{{ $collection->title }}</div>
                                                                            @php
                                                                                $authors = $collection->authors()->pluck('author')->toArray();
                                                                            @endphp
                                                                            <div class="h6 text-muted">
                                                                                @foreach ($authors as $author)
                                                                                    <span class="badge badge-count"><strong>{{ $author }}</strong></span>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                        <a href="{{ route('collections.show', ['id' => $collection->id]) }}">
                                                                            <button
                                                                                class="btn btn-icon btn-primary btn-round btn-xs">
                                                                                <i class="fa fa-eye"></i>
                                                                            </button>
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DATATABLE SCRIPT -->
    @include('js.dashboard.index')
@endsection
