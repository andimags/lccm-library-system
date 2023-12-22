<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var inHouseCirculationsTable = $('#table-in-house-circulations').DataTable({
            'columns': [{
                    data: 'checkbox'
                },
                {
                    data: 'barcode',
                },
                {
                    data: 'title',
                },
                {
                    data: 'librarian',
                },
                {
                    data: 'date',
                },
                {
                    data: 'action',
                }
            ],
            'columnDefs': [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    },
                    className: 'select-checkbox'
                },
                {
                    targets: [0, 1, 2],
                    orderable: false
                },
                {
                    width: '150px',
                    // targets: [3]
                }
            ],
            // scrollX: true,
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,
            select: true,
            idSrc: 'id', // Set the idSrc option
            serverSide: true,
            processing: true,
            ajax: {
                url: @if (Route::currentRouteName() === 'in.house.circulations.archive')
                    "{{ route('in.house.circulations.archive') }}"
                @else
                    "{{ route('in.house.circulations.index') }}"
                @endif
            }
        });

        // ░█▀▀▀█ ░█──░█ ─█▀▀█ ░█─── █▀█ 
        // ─▀▀▀▄▄ ░█░█░█ ░█▄▄█ ░█─── ─▄▀ 
        // ░█▄▄▄█ ░█▄▀▄█ ░█─░█ ░█▄▄█ █▄▄
        function swal(title, icon) {
            Swal.fire({
                position: 'center',
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 1200
            })
        }

        // █▀▀ █───█ █▀▀█ █── 　 █▀▀ █▀▀█ █▀▀▄ █▀▀ ─▀─ █▀▀█ █▀▄▀█ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ▀▀█ █▄█▄█ █▄▄█ █── 　 █── █──█ █──█ █▀▀ ▀█▀ █▄▄▀ █─▀─█ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ▀▀▀ ─▀─▀─ ▀──▀ ▀▀▀ 　 ▀▀▀ ▀▀▀▀ ▀──▀ ▀── ▀▀▀ ▀─▀▀ ▀───▀ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        function swalConfirmation(text, confirmButtonText, callback) {
            Swal.fire({
                title: 'Are you sure?',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1572E8',
                confirmButtonText: confirmButtonText,
                iconColor: '#1572E8',
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof callback === "function") {
                        callback();
                    }
                }
            });
        }


        // █▀▀ █▀▀█ █▀▀ █▀▀█ ▀▀█▀▀ █▀▀ 
        // █░░ █▄▄▀ █▀▀ █▄▄█ ░░█░░ █▀▀ 
        // ▀▀▀ ▀░▀▀ ▀▀▀ ▀░░▀ ░░▀░░ ▀▀▀
        $('#btn-in-house-circulation-create').click(function() {
            window.location.href = '{{ route('off.site.circulations.create') }}';
        });

        $('#btn-in-house-circulation-check-in').click(function() {
            $('#barcode-modal').modal('show');
            $('#barcode-modal-header').html('Add in-house item');
            $('#barcode-modal-button').html('Add');
        });

        function removeErrorMessages() {
            $('.input_msg').each(function(i, obj) {
                $(this).html('');
            });

            $('.form-group').each(function(i, obj) {
                $(this).removeClass('has-error has-feedback');
            });
        }

        function ajaxSuccess(data) {
            if (data.code == '400') {
                showInputErrors(data);
                return;
            }

            $('#barcode-modal').modal('hide');

            if (data.error) {
                swal(data.error, 'error');
            }
            if (data.success) {
                swal(data.success, 'success');
            }

            inHouseCirculationsTable.column(0).checkboxes.deselectAll();
            // toggleButtons();
            inHouseCirculationsTable.ajax.reload();
        }

        $("#barcode-modal").on("hidden.bs.modal", function() {
            $('#barcode-form').trigger('reset');
        });

        // █▀▀ █──█ █▀▀ █▀▀ █─█ ── ─▀─ █▀▀▄ 
        // █── █▀▀█ █▀▀ █── █▀▄ ▀▀ ▀█▀ █──█ 
        // ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀─▀ ── ▀▀▀ ▀──▀
        $('#barcode-form').submit(function(e) {
            e.preventDefault();

            removeErrorMessages();

            $.ajax({
                type: 'POST',
                url: '{{ route('in.house.circulations.store', ['barcode' => ':barcode']) }}'
                    .replace(':barcode', $('#barcode').val()),
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        })


        // █▀▀ █▀▀▄ █▀▀█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // █▀▀ █──█ █▄▄█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        $(document).on('change', 'td.select-checkbox, th.select-checkbox', function() {
            var selectedRows = inHouseCirculationsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#in-house-circulation-delete-all, #in-house-circulation-force-delete-all, #in-house-circulation-restore-all')
                    .removeAttr(
                        'disabled');
                $('.in-house-circulation-count').html('(' + selectedRows.length + ')');
            } else {
                $('#in-house-circulation-delete-all, #in-house-circulation-force-delete-all, #in-house-circulation-restore-all')
                    .attr(
                        'disabled', true);
                $('.in-house-circulation-count').html('');
            }
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = inHouseCirculationsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-in-house-circulation-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('in.house.circulations.destroy') }}';

            swalConfirmation(
                'Delete this in-house circulation and all of its related information?',
                'Yes, delete it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀█ █── █── 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▄▄█ █── █── 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#in-house-circulation-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('in.house.circulations.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length +
                ' in-house circulation(s) and all of its related information?',
                'Yes, delete it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


        // █▀▀█ █▀▀ █▀▀ ▀▀█▀▀ █▀▀█ █▀▀█ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▄▄▀ █▀▀ ▀▀█ ──█── █──█ █▄▄▀ █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀─▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-in-house-circulation-restore', function() {
            let id = $(this).data('id');
            let url = '{{ route('in.house.circulations.restore') }}';

            swalConfirmation(
                'Restore this in-house circulation and all of its related information?',
                'Yes, restore it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


        // █▀▀█ █▀▀ █▀▀ ▀▀█▀▀ █▀▀█ █▀▀█ █▀▀ 　 █▀▀█ █── █── 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▄▄▀ █▀▀ ▀▀█ ──█── █──█ █▄▄▀ █▀▀ 　 █▄▄█ █── █── 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀─▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#in-house-circulation-restore-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('in.house.circulations.restore') }}';

            swalConfirmation(
                'Restore ' + id.length +
                ' in-house circulation(s) and all of its related information?',
                'Yes, restore it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


        // █▀▀ █▀▀█ █▀▀█ █▀▀ █▀▀ 　 █▀▀▄ █▀▀ █── 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▀▀ █──█ █▄▄▀ █── █▀▀ 　 █──█ █▀▀ █── 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-in-house-circulation-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('in.house.circulations.force.delete') }}';

            swalConfirmation(
                'Permanently delete this in-house circulation and all of its related information?',
                'Yes, delete it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });

        // █▀▀ █▀▀█ █▀▀█ █▀▀ █▀▀ 　 █▀▀▄ █▀▀ █── 　 █▀▀█ █── █── 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▀▀ █──█ █▄▄▀ █── █▀▀ 　 █──█ █▀▀ █── 　 █▄▄█ █── █── 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#in-house-circulation-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('in.house.circulations.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' in-house circulation(s) and all of its related information?',
                'Yes, delete it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


    });
</script>
