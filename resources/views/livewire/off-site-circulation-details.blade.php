<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">
                        Details
                    </h4>
                    <div class="float-right">
                        @if (auth()->user()->temp_role == 'librarian')
                            @if (!$offSiteCirculation->deleted_at)
                                @if (!$offSiteCirculation->checked_in_at)
                                    @if ($offSiteCirculation->status == 'lost')
                                        <button class="btn btn-primary" id="btn-undo-mark-copy-as-lost">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            Undo mark copy as lost
                                        </button>
                                    @else
                                        <button class="btn btn-primary" id="btn-mark-copy-as-lost">
                                            <i class="fa-solid fa-file-circle-exclamation"></i>
                                            Mark copy as lost
                                        </button>
                                    @endif
                                    <button class="btn btn-primary " data-toggle="modal"
                                        data-target="#exampleModalCenter" id="btn-off-site-circulation-check-in"
                                        value="check-in">
                                        <i class="fa fa-plus"></i> Check-In
                                    </button>
                                    <button class="btn btn-primary " data-toggle="modal"
                                        id="btn-off-site-circulation-renew">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        Renew
                                    </button>
                                @endif
                                @if (
                                    $offSiteCirculation->status == 'checked-in' ||
                                        $offSiteCirculation->status == 'lost' ||
                                        ($offSiteCirculation->total_fines > 0 && $offSiteCirculation->fines_status == 'unpaid'))
                                    <button class="btn btn-primary " data-toggle="modal"
                                        data-target="#exampleModalCenter" id="btn-off-site-circulation-delete"
                                        value="delete">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                                    id="btn-off-site-circulation-force-delete">
                                    <i class="fa fa-trash"></i>
                                    Force delete
                                </button>
                                <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModalCenter"
                                    id="btn-off-site-circulation-restore">
                                    <i class="fa fa-trash"></i>
                                    Restore
                                </button>
                            @endif
                        @endif
                    </div>

                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-condensed">
                    <tbody>
                        <tr>
                            <td>#</td>
                            <td><b>{{ $offSiteCirculation->id }}</b></td>
                            <td>Borrower name</td>
                            <td><b><a
                                        href="{{ route('patrons.index') . '/' . $offSiteCirculation->borrower_id }}">{{ $offSiteCirculation->borrower->last_name . ', ' . $offSiteCirculation->borrower->first_name }}</a></b>
                            </td>
                        </tr>
                        <tr>
                            <td>Copy barcode</td>
                            <td> <a
                                    href="{{ route('collections.index') . '/' . $offSiteCirculation->copy->collection_id }}"><b>{{ $offSiteCirculation->copy->barcode }}</b></a>
                            </td>
                            <td>Due date</td>
                            <td><b>{{ $offSiteCirculation->due_at->format('F j, Y') }}</b></td>
                        </tr>
                        <tr>
                            <td>Total fines</td>
                            <td @php
$totalFines = $offSiteCirculation->fines();
                                if($offSiteCirculation->deleted_at){
                                    $totalFines = $totalFines->withTrashed();
                                }
                                $totalFines = $totalFines->sum('price'); @endphp
                                class="{{ ($totalFines > 0) & ($offSiteCirculation->fines_status == 'unpaid') ? 'text-danger' : '' }}">
                                <b>₱ {{ number_format($totalFines, 2, '.', ',') }}</b>
                            </td>
                            <td>Fines status</td>
                            <td>
                                @if ($offSiteCirculation->total_fines > 0)
                                    @if ($offSiteCirculation->status != 'checked-out')
                                        @if (auth()->user()->temp_role == 'librarian')
                                            <div class="form-group">
                                                <select class="form-control" id="select-fines-status"
                                                    {{ $offSiteCirculation->deleted_at ? 'disabled' : '' }}>
                                                    <option value="paid"
                                                        {{ $offSiteCirculation->fines_status == 'paid' ? 'selected' : '' }}>
                                                        Paid
                                                    </option>
                                                    <option value="unpaid"
                                                        {{ $offSiteCirculation->fines_status == 'unpaid' ? 'selected' : '' }}>
                                                        Unpaid</option>
                                                </select>
                                            </div>
                                        @else
                                            @php
                                                $fineStatuses = [
                                                    'unpaid' => 'warning',
                                                    'paid' => 'success',
                                                ];
                                            @endphp

                                            <span
                                                class="badge badge-{{ $fineStatuses[$offSiteCirculation->fines_status] }}">{{ $offSiteCirculation->fines_status }}</span>
                                        @endif
                                    @else
                                        @php
                                            $fineStatuses = [
                                                'unpaid' => 'warning',
                                                'paid' => 'success',
                                            ];
                                        @endphp

                                        <span
                                            class="badge badge-{{ $fineStatuses[$offSiteCirculation->fines_status] }}">{{ $offSiteCirculation->fines_status }}</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Grace Period Days</td>
                            <td><b>{{ $offSiteCirculation->grace_period_days }}</b></td>
                            <td>Checked-in</td>
                            <td><b>{{ optional($offSiteCirculation->checked_in_at)->format('F j, Y g:i A') }}</b></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                @php
                                    $statuses = [
                                        'checked-out' => 'warning',
                                        'checked-in' => 'primary',
                                        'lost' => 'danger',
                                    ];
                                @endphp

                                <b><span
                                        class="badge badge-{{ $statuses[$offSiteCirculation->status] }}">{{ $offSiteCirculation->status }}</span></b>
                            </td>
                            <td>Librarian</td>
                            <td>
                                <a
                                    href="{{ route('patrons.index') . '/' . $offSiteCirculation->librarian_id }}"><b>{{ $offSiteCirculation->librarian->last_name . ', ' . $offSiteCirculation->librarian->first_name }}</b></a>
                            </td>
                        </tr>
                        <tr>
                            @if ($offSiteCirculation->deleted_at)
                                <td>Deleted at</td>
                                <td class="text-danger">
                                    <b>{{ $offSiteCirculation->deleted_at->format('F j, Y g:i A') }}</b>
                                </td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
