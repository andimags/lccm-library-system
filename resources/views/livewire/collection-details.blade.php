<div class="card-body">
    <div class="d-flex justify-content-between mb-3">
        <h4 class="card-title">
            {{ $collection->title }}
        </h4>
    </div>
    <ul class="nav nav-primary mb-3 nav-line" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-details-tab-nobd" data-toggle="pill" href="#pills-details-nobd"
                role="tab" aria-controls="pills-details-nobd" aria-selected="true">Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-copies-history-tab-nobd" data-toggle="pill" href="#pills-copies-history-nobd"
                role="tab" aria-controls="pills-copies-history-nobd" aria-selected="false">Copies</a>
        </li>
    </ul>
    <div class="tab-content mb-3" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-details-nobd" role="tabpanel"
            aria-labelledby="pills-details-tab-nobd">
            <div class="d-flex justify-content-between mb-3">
                <h4 class="card-title">

                </h4>
                @auth
                    @if (auth()->user()->temp_role == 'librarian')
                        @if (!$collection->deleted_at)
                            <div class="float-right">
                                <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                                    id="btn-collection-edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Edit
                                </button>
                                <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                                    id="btn-collection-delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Delete
                                </button>
                            </div>
                        @else
                            <div class="float-right">
                                <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                                    id="btn-collection-force-delete">
                                    <i class="fa fa-trash"></i>
                                    Force delete
                                </button>
                                <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                                    id="btn-collection-restore">
                                    <i class="fa fa-trash"></i>
                                    Restore
                                </button>
                            </div>
                        @endif
                    @endif
                @endauth
            </div>
            <div class="container px-lg-5">
                <div class="row gx-4 gx-lg-5 align-items-center">
                    @php
                        $latestImage = $collection
                            ->images()
                            ->latest()
                            ->first();
                        $fileName = optional($latestImage)->file_name ?? 'default.jpg';
                    @endphp
                    <div class="col-md-6"><img class="card-img-top mb-5 mb-md-0"
                            src="{{ asset('storage/images/collections/' . $fileName) }}" alt="..."></div>
                    <div class="col-md-6">
                        <h1 class="display-5 fw-bolder">{{ $collection->title }}</h1>
                        <table class="table table-responsive table-condensed">
                            <tbody>
                                @if ($collection->deleted_at)
                                    <tr>
                                        <td>Deleted at</td>
                                        <td><strong class="text-danger">{{ $collection->deleted_at }}</strong></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>Format</td>
                                    <td><strong>{{ $collection->format }}</strong></td>
                                </tr>
                                @if ($collection->authors->isNotEmpty())
                                    <tr>
                                        <td>Authors</td>
                                        <td>
                                            @foreach ($collection->authors->pluck('author') as $author)
                                                <span
                                                    class="badge badge-count"><strong>{{ $author }}</strong></span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif

                                @if ($collection->subtitles->isNotEmpty())
                                    <tr>
                                        <td>Subtitles</td>
                                        <td>
                                            @foreach ($collection->subtitles->pluck('subtitle') as $subtitle)
                                                <span
                                                    class="badge badge-count"><strong>{{ $subtitle }}</strong></span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif

                                @if ($collection->edition)
                                    <tr>
                                        <td>Edition</td>
                                        <td><strong>{{ $collection->edition }}</strong></td>
                                    </tr>
                                @endif
                                @if ($collection->series_title)
                                    <tr>
                                        <td>Series title</td>
                                        <td><strong>{{ $collection->series_title }}</strong></td>
                                    </tr>
                                @endif
                                @if ($collection->isbn)
                                    <tr>
                                        <td>ISBN</td>
                                        <td><strong>{{ $collection->isbn }}</strong></td>
                                    </tr>
                                @endif
                                @if ($collection->publication_place)
                                    <tr>
                                        <td>Publication place</td>
                                        <td><strong>{{ $collection->publication_place }}</strong></td>
                                    </tr>
                                @endif
                                @if ($collection->publisher)
                                    <tr>
                                        <td>Publisher</td>
                                        <td><strong>{{ $collection->publisher }}</strong></td>
                                    </tr>
                                @endif
                                @if ($collection->copyright_year)
                                    <tr>
                                        <td>Copyright year</td>
                                        <td><strong>{{ $collection->copyright_year }}</strong></td>
                                    </tr>
                                @endif
                                @if ($collection->physical_description)
                                    <tr>
                                        <td>Physical description</td>
                                        <td><strong>{{ $collection->physical_description }}</strong></td>
                                    </tr>
                                @endif
                                @if ($collection->call_prefix || $collection->call_main || $collection->call_cutter || $collection->call_suffix)
                                    <tr>
                                        <td>Call Number</td>
                                        <td><strong>
                                                {{ $collection->call_prefix ? $collection->call_prefix . ' ' : '' }}
                                                {{ $collection->call_main ? $collection->call_main . ' ' : '' }}
                                                {{ $collection->call_cutter ? $collection->call_cutter . ' ' : '' }}
                                                {{ $collection->call_suffix ? $collection->call_suffix : '' }}
                                            </strong></td>
                                    </tr>
                                @endif
                                @if ($collection->subjects->isNotEmpty())
                                    <tr>
                                        <td>Subjects</td>
                                        <td>
                                            @foreach ($collection->subjects->pluck('subject') as $subject)
                                                <span
                                                    class="badge badge-count"><strong>{{ $subject }}</strong></span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                                @if ($collection->librarian_id)
                                    <tr>
                                        <td>Librarian</td>
                                        <td><strong><a
                                                    href="{{ route('patrons.show', ['id' => $collection->librarian->id]) }}">{{ $collection->librarian->last_name . ', ' . $collection->librarian->first_name }}</a></strong>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- <div class="container px-4 px-lg-5 mt-5">
                <h2 class="fw-bolder mb-4">Related products</h2>
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <div class="col mb-5">
                        <div class="card h-100">
                            <!-- Product image-->
                            <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="...">
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Product name-->
                                    <h5 class="fw-bolder">Fancy Product</h5>
                                    <!-- Product price-->
                                    $40.00 - $80.00
                                </div>
                            </div>
                            <!-- Product actions-->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">View options</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-5">
                        <div class="card h-100">
                            <!-- Sale badge-->
                            <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                            <!-- Product image-->
                            <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="...">
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Product name-->
                                    <h5 class="fw-bolder">Special Item</h5>
                                    <!-- Product reviews-->
                                    <div class="d-flex justify-content-center small text-warning mb-2">
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                    </div>
                                    <!-- Product price-->
                                    <span class="text-muted text-decoration-line-through">$20.00</span>
                                    $18.00
                                </div>
                            </div>
                            <!-- Product actions-->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-5">
                        <div class="card h-100">
                            <!-- Sale badge-->
                            <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                            <!-- Product image-->
                            <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="...">
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Product name-->
                                    <h5 class="fw-bolder">Sale Item</h5>
                                    <!-- Product price-->
                                    <span class="text-muted text-decoration-line-through">$50.00</span>
                                    $25.00
                                </div>
                            </div>
                            <!-- Product actions-->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-5">
                        <div class="card h-100">
                            <!-- Product image-->
                            <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="...">
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Product name-->
                                    <h5 class="fw-bolder">Popular Item</h5>
                                    <!-- Product reviews-->
                                    <div class="d-flex justify-content-center small text-warning mb-2">
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                    </div>
                                    <!-- Product price-->
                                    $40.00
                                </div>
                            </div>
                            <!-- Product actions-->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        <div class="tab-pane fade" id="pills-copies-history-nobd" role="tabpanel"
            aria-labelledby="pills-copies-history-tab-nobd">
            <div class="d-flex justify-content-between mb-3">
                <h4 class="card-title">

                </h4>
                @if (auth()->check() && auth()->user()->temp_role == 'librarian' && !$collection->deleted_at)
                    <div class="float-right">
                        <button class="btn btn-primary" id="copy-delete-all" disabled>
                            <i class="fa-solid fa-trash-can"></i>
                            Delete All
                            <span class="copy-count"></span>
                        </button>
                        <button class="btn btn-primary" id="copy-add">
                            <i class="fas fa-plus"></i>
                            Add copy
                        </button>
                    </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="display table table-striped table-hover w-100" id="table-copies">
                    <thead>
                        <tr>
                            @if (auth()->check() && auth()->user()->temp_role == 'librarian')
                                <th></th>
                            @endif            
                            <th>Barcode</th>
                            <th>Call Prefix</th>
                            <th>Fund</th>
                            <th>Vendor</th>
                            <th>Price</th>
                            <th>Date Acquired</th>
                            <th>Librarian</th>
                            <th>Availability</th>
                            @auth
                                <th>Action</th>
                            @endauth
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            @if (auth()->check() && auth()->user()->temp_role == 'librarian')
                                <th></th>
                            @endif                                
                            <th>Barcode</th>
                            <th>Call Prefix</th>
                            <th>Fund</th>
                            <th>Vendor</th>
                            <th>Price</th>
                            <th>Date Acquired</th>
                            <th>Librarian</th>
                            <th>Availability</th>
                            @auth
                                <th>Action</th>
                            @endauth
                        </tr>
                    </tfoot>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
