<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var itemsTable = $('#table-items').DataTable({
            'columns': [{
                    data: 'DT_RowIndex',
                },
                {
                    data: 'image',
                },
                {
                    data: 'id',
                },
                {
                    data: 'name',
                },
                {
                    data: 'quantity',
                },
                {
                    data: 'status',
                }
            ],
            'columnDefs': [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    select: true,
                },
                {
                    targets: [0, 1, 2],
                    orderable: false
                },
                {
                    width: '150px',
                    targets: [4]
                }
            ],
            scrollX: true,
            scrollCollapse: true,
            paging: true,
            fixedColumns: true,
            select: {
                style: 'multi',
                selector: 'td:first-child'
            },
            idSrc: 'id', // Set the idSrc option
            serverSide: true,
            processing: true,
            ajax: '{{ route('reservations.show.items', $id) }}'
        });

        var statusesTable = $('#table-statuses').DataTable({
            'columns': [{
                    data: 'DT_RowIndex',
                },
                {
                    data: 'status',
                },
                {
                    data: 'reason',
                },
                {
                    data: 'date',
                },
                {
                    data: 'librarian',
                }
            ],
            'columnDefs': [{
                    orderable: false,
                    targets: 0,
                    select: true,
                },
                {
                    targets: [0, 1, 2],
                    orderable: false
                }
            ],
            scrollX: true,
            scrollCollapse: true,
            paging: true,
            fixedColumns: true,
            select: {
                style: 'multi',
                selector: 'td:first-child'
            },
            idSrc: 'id', // Set the idSrc option
            serverSide: true,
            processing: true,
            ajax: '{{ route('reservations.show.statuses', $id) }}'
        });

        var acceptReservationTable = $('#accept-reservation-table').DataTable({
            'columns': [{
                    data: 'checkbox'
                },
                {
                    data: 'DT_RowIndex',
                },
                {
                    data: 'image',
                },
                {
                    data: 'reservation_itemable_id',
                },
                {
                    data: 'name',
                },
                {
                    data: 'availability',
                }
            ],
            'columnDefs': [{
                orderable: false,
                className: 'select-checkbox',
                targets: 0,
                checkboxes: true,
                select: true,
                'createdCell': function(td, cellData, rowData, row, col) {
                    if (rowData['availability'].includes('unavailable')) {
                        this.api().cell(td).checkboxes.disable();
                    } else {
                        this.api().cell(td).checkboxes.select();
                    }
                }
            }],
            lengthChange: false,
            searching: false,
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,
            idSrc: 'id', // Set the idSrc option
        });


        // ░█▀▀▀█ ░█──░█ ─█▀▀█ ░█─── █▀█ 
        // ─▀▀▀▄▄ ░█░█░█ ░█▄▄█ ░█─── ─▄▀ 
        // ░█▄▄▄█ ░█▄▀▄█ ░█─░█ ░█▄▄█ █▄▄
        function swal(title) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: title,
                showConfirmButton: false,
                timer: 1200
            })
        }

        // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
        // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
        // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
        $(document).on('click', '#btn-reservation-accept, #btn-reservation-decline, #btn-reservation-cancel',
            function() {
                var id = {{ $reservation->id }};
                var value = $(this).data('value');

                if (value == "declined") {
                    Swal.fire({
                        title: 'Decline reservation reason',
                        input: 'textarea',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Decline',
                        confirmButtonColor: '#F25961',
                        showLoaderOnConfirm: true,
                        preConfirm: (note) => {
                            if (!note) {
                                Swal.showValidationMessage(
                                    'Please enter a reason for declining the reservation');
                            }
                            return note;
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            handleRequest(id, value, result.value);
                        }
                    })
                } else if (value == "accepted") {
                    acceptReservationTable.ajax.url(
                        "{{ route('reservations.show.items', ['id' => ':id']) }}".replace(
                            ':id', id)).load(function() {
                        toggleButtons();
                        $('#accept-reservation-modal-header').html('Reservation #' + id + ' items')
                        $('#accept-reservation-modal-id').val(id);
                        $('#accept-reservation-modal').modal('show');
                    });
                } else if (value == "pending") {
                    handleRequest(id, 'pending');
                }

                function handleRequest(id, value, note) {
                    var requestData = {
                        id: id,
                        value: value
                    };

                    if (note) {
                        requestData.note = note;
                    }

                    manageReservation(
                        requestData,
                        "{{ route('reservations.index') }}" + "/" + id,
                        'PUT',
                        "Set the reservation's status to " + value + "?",
                        'Yes, set status to ' + value + '!',
                    );
                }
            });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let itemsTableRows = acceptReservationTable.column(0).checkboxes.selected();

            if (itemsTableRows.length > 0) {
                $('#items-accept-all').removeAttr('disabled');
                $('.accept-reservation-count').html('(' + itemsTableRows.length + ')');
            } else {
                $('#items-accept-all').attr('disabled', true);
                $('.accept-reservation-modal-count').html('');
            }

        }

        $(document).on('change', 'td.select-checkbox, th.select-checkbox', function() {
            toggleButtons();
        });

        // ─█▀▀█ ░█▀▀█ ░█▀▀█ ░█▀▀▀ ░█▀▀█ ▀▀█▀▀ 　 ─█▀▀█ ░█─── ░█─── 
        // ░█▄▄█ ░█─── ░█─── ░█▀▀▀ ░█▄▄█ ─░█── 　 ░█▄▄█ ░█─── ░█─── 
        // ░█─░█ ░█▄▄█ ░█▄▄█ ░█▄▄▄ ░█─── ─░█── 　 ░█─░█ ░█▄▄█ ░█▄▄█
        $('#items-accept-all').click(function() {
            let selectedRows = acceptReservationTable.column(0).checkboxes.selected();
            let reservationId = '{{ $reservation->id }}';

            let id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            manageReservation({
                    ids: id,
                    value: 'accepted'
                },
                "{{ route('reservations.update', ['id' => ':id']) }}".replace(':id', reservationId),
                'put',
                'Accept ' + selectedRows.length.toString() +
                ' reservation item(s)?',
                'Yes, accept all!'
            );
        });


        // █▀▄▀█ █▀▀█ █▀▀▄ █▀▀█ █▀▀▀ █▀▀ 　 █▀▀█ █▀▀ █▀▀ █▀▀ █▀▀█ ▀█─█▀ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // █─▀─█ █▄▄█ █──█ █▄▄█ █─▀█ █▀▀ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ █▄▄▀ ─█▄█─ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ▀───▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀─▀▀ ──▀── ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        function manageReservation(data, url, method, text, confirmButtonText,
            showConfirmation = true, showSwal = true) {
            if (showConfirmation) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1572E8',
                    cancelButtonColor: '#48ABF7',
                    confirmButtonText: confirmButtonText,
                    iconColor: '#F25961'
                }).then((result) => {
                    if (result.isConfirmed) {
                        doAjaxCall();
                    }
                });
            } else {
                doAjaxCall();
            }

            function doAjaxCall() {
                $.ajax({
                    type: 'post', // method shown on route:list
                    data: data,
                    headers: {
                        'X-HTTP-Method-Override': method
                    },
                    url: url,
                    success: function(data) {
                        Livewire.emit('refreshReservationInformation');
                        itemsTable.draw();
                        statusesTable.draw();

                        if (data.error) {
                            swal(data.error, 'error');
                        }

                        if (data.success) {
                            swal(data.success, 'success');
                        }

                        $('#reservation-delete-all, #reservation-force-delete-all, #reservation-restore-all')
                            .attr('disabled', true);
                        $('.reservation-count').html('');
                        itemsTable.column(0).checkboxes.deselectAll();
                        $('#accept-reservation-modal').modal('hide');
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#btn-reservation-delete').on('click', function() {
            let id = '{{ $reservation->id }}';

            Swal.fire({
                title: 'Are you sure?',
                text: 'Delete this reservation and all of its related information?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1572E8',
                cancelButtonColor: '#48ABF7',
                confirmButtonText: 'Yes, delete it!',
                iconColor: '#F25961'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.setAttribute('method', 'POST');
                    form.setAttribute('id', id);
                    form.setAttribute('action', '{{ route('reservations.destroy') }}');
                    form.innerHTML =
                        '<input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="id" value="' +
                        id + '">';

                    document.body.appendChild(form);

                    form.submit();
                }
            });
        });


        // █▀▀█ █▀▀ █▀▀ ▀▀█▀▀ █▀▀█ █▀▀█ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▄▄▀ █▀▀ ▀▀█ ──█── █──█ █▄▄▀ █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀─▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-reservation-restore', function() {
            let id = $(this).data('id');

            manageReservation({
                    id: id
                },
                "{{ route('reservations.restore') }}",
                'put',
                'Restore reservation and all of its related information?',
                'Yes, restore it!'
            );
        });
    });
</script>
