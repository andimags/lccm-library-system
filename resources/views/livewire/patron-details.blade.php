<div class="card-body text-center">
    @php
        $latestImage = $patron
            ->images()
            ->latest()
            ->first();
        $fileName = optional($latestImage)->file_name;
    @endphp
    <div class="avatar-xxl mt-3 mb-4 m-auto">
        <img src="{{ $latestImage ? asset('storage/images/patrons/' . $fileName) : Avatar::create($patron->first_name . ', ' . $patron->last_name)->setFontFamily('Lato')->toBase64() }}" alt="User image" class="avatar-img rounded-circle"/>
    </div>
    <h4 class="my-2 font-weight-bold">
        <strong>{{ $patron->last_name . ', ' . $patron->first_name }}</strong>
    </h4>

    @if ($patron->deleted_at != null)
        <p class="text-muted mb-1 text-danger">Deleted at <span class="mx-2">|</span>
            <strong>{{ $patron->deleted_at }}</strong>
        </p>
    @endif

    <p class="text-muted mb-1">ID <span class="mx-2">|</span>
        <strong>{{ $patron->id2 }}</strong>
    </p>
    <p class="text-muted mb-1">Role <span class="mx-2">|</span>
        <strong>
            {{ Str::title(
                $patron->roles->pluck('name')->map(function ($role) {
                        return Str::title($role);
                    })->implode(', '),
            ) }}
        </strong>
    </p>
    <p class="text-muted mb-1">Email <span class="mx-2">|</span>
        <strong>{{ $patron->email }}</strong>
    </p>
    @if (!$patron->groups->isEmpty())
        <p class="text-muted mb-1">Groups <span class="mx-2">|</span>
            <strong>{{ $patron->groups->pluck('group')->implode(', ') }}</strong>
        </p>
    @endif
    @if ($patron->librarian)
        <p class="text-muted mb-1">Added by <span class="mx-2">|</span>
            <a href="{{ route('patrons.index') . '/' . $patron->librarian_id }}"><strong>{{ $patron->librarian->last_name . ', ' . $patron->librarian->first_name }}</strong></a>
        </p>
    @endif
    {{-- <div class="my-4 py-2">
        <button type="button" class="btn btn-outline-primary btn-floating">
            <i class="fab fa-facebook-f fa-lg"></i>
        </button>
        <button type="button" class="btn btn-outline-primary btn-floating">
            <i class="fab fa-twitter fa-lg"></i>
        </button>
        <button type="button" class="btn btn-outline-primary btn-floating">
            <i class="fab fa-skype fa-lg"></i>
        </button>
    </div> --}}
    @if ($patron->id != auth()->user()->id)
        <a href="mailto:{{ $patron->email }}">
            <button type="button" class="btn btn-outline-primary btn-lg mt-4 pt-2">
                <i class="fa-solid fa-envelope"></i>
                Email now
            </button>
        </a>
    @endif
    <div class="d-flex justify-content-between text-center mt-5 mb-2">
        <div>
            <p class="mb-2 h5"><strong>{{ number_format($patron->offSiteCirculations()->count(), 0, '.', ',') }}</strong></p>
            <p class="text-muted mb-0">Total circulations</p>
        </div>
        <div>
            <p class="mb-2 h5 {{ $patron->totalUnpaidFines() > 0 ? 'text-danger' : '' }}"><strong>₱ {{ number_format($patron->totalUnpaidFines(), 2, '.', ',') }}</strong></p>
            <p class="text-muted mb-0">Total unpaid fines</p>
        </div>
        <div class="px-3">
            <p class="mb-2 h5"><strong>{{ number_format($patron->offSiteCirculations()->where('status', 'checked-out')->count(), 0, '.', ',') }}</strong></p>
            <p class="text-muted mb-0">On loan</p>
        </div>
        <div class="px-3">
            <p class="mb-2 h5"><strong>{{ number_format($patron->reservations()->count(), 0, '.', ',') }}</strong></p>
            <p class="text-muted mb-0">Reservations</p>
        </div>
    </div>

    {{-- @include('js.patrons.show') --}}
</div>
