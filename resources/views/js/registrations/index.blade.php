<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // https://github.com/yajra/laravel-datatables/discussions/2758
        var registrationsTable = $('#table-registrations').DataTable({
            columns: [{
                    data: 'checkbox',
                },
                {
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id2',
                },
                {
                    data: 'last_name',
                },
                {
                    data: 'first_name',
                },
                {
                    data: 'roles',
                },
                {
                    data: 'action',
                }
            ],
            columnDefs: [
                // {
                //     targets: [4, 5, 6],
                //     orderable: true
                // // },
                // {
                //     targets: [3, 4, 5, 6],
                //     searchable: true
                // },
                // {
                //     width: '125px',
                //     targets: [4, 5]
                // },
                {
                    targets: 0,
                    checkboxes: {
                        'selectRow': true
                    },
                    className: 'select-checkbox'
                }
            ],
            searching: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: "{{ \Illuminate\Support\Facades\Route::currentRouteName() == 'registrations.index' ? route('registrations.index') : route('registrations.archive') }}",
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

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = registrationsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#registration-accept-all, #registration-decline-all')
                    .removeAttr('disabled');
                $('.registration-count').html('(' + selectedRows.length + ')');
            } else {
                $('#registration-accept-all, #registration-decline-all').attr(
                    'disabled', true);
                $('.registration-count').html('');
            }
        }

        // █▀▀ █──█ █▀▀ █▀▀ █─█ █▀▀▄ █▀▀█ █─█ 　 █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 
        // █── █▀▀█ █▀▀ █── █▀▄ █▀▀▄ █──█ ▄▀▄ 　 █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 
        // ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀─▀ ▀▀▀─ ▀▀▀▀ ▀─▀ 　 ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀
        $(document).on('change', 'td.select-checkbox, th.select-checkbox', function() {
            toggleButtons();
        });

        // █▀▀ █░░█ █▀▀█ █░░░█ 　 ░▀░ █▀▀▄ █▀▀█ █░░█ ▀▀█▀▀ 　 █▀▀ █▀▀█ █▀▀█ 
        // ▀▀█ █▀▀█ █░░█ █▄█▄█ 　 ▀█▀ █░░█ █░░█ █░░█ ░░█░░ 　 █▀▀ █▄▄▀ █▄▄▀ 
        // ▀▀▀ ▀░░▀ ▀▀▀▀ ░▀░▀░ 　 ▀▀▀ ▀░░▀ █▀▀▀ ░▀▀▀ ░░▀░░ 　 ▀▀▀ ▀░▀▀ ▀░▀▀
        function showInputErrors(data) {
            for (let key in data.msg) {
                $('#' + String(key) + '_msg').html(String(data.msg[key]));
                $('#form_group_' + String(key)).addClass(
                    'has-error has-feedback');
            }
        }

        // █▀▀█ █▀▀ █▀▄▀█ █▀▀█ ▀█─█▀ █▀▀ 　 ─▀─ █▀▀▄ █▀▀█ █──█ ▀▀█▀▀ 　 █▀▀ █▀▀█ █▀▀█ 
        // █▄▄▀ █▀▀ █─▀─█ █──█ ─█▄█─ █▀▀ 　 ▀█▀ █──█ █──█ █──█ ──█── 　 █▀▀ █▄▄▀ █▄▄▀ 
        // ▀─▀▀ ▀▀▀ ▀───▀ ▀▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀ ▀──▀ █▀▀▀ ─▀▀▀ ──▀── 　 ▀▀▀ ▀─▀▀ ▀─▀▀
        function removeInputErrors() {
            $('.input_msg').html('');
            $('.form-group').removeClass('has-error has-feedback');
        }

        // █▀▀█ ──▀ █▀▀█ █─█ 　 █▀▀ █──█ █▀▀ █▀▀ █▀▀ █▀▀ █▀▀ 
        // █▄▄█ ──█ █▄▄█ ▄▀▄ 　 ▀▀█ █──█ █── █── █▀▀ ▀▀█ ▀▀█ 
        // ▀──▀ █▄█ ▀──▀ ▀─▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀
        function ajaxSuccess(data) {
            console.log(data);

            if (data.code == '400') {
                showInputErrors(data);
                return;
            }

            if (data.error) {
                swal(data.error, 'error');
            }
            if (data.success) {
                swal(data.success, 'success');
            }

            registrationsTable.column(0).checkboxes.deselectAll();
            toggleButtons();
            registrationsTable.ajax.reload();
            $('#registration-form').trigger('reset');
        }

        // █▀▀█ ──▀ █▀▀█ █─█ 　 █▀▀ █──█ █▀▀ █▀▀ █▀▀ █▀▀ █▀▀ 
        // █▄▄█ ──█ █▄▄█ ▄▀▄ 　 ▀▀█ █──█ █── █── █▀▀ ▀▀█ ▀▀█ 
        // ▀──▀ █▄█ ▀──▀ ▀─▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀
        function ajaxSuccess(data) {
            console.log(data);
            if (data.code == '400') {
                showInputErrors(data);
                return;
            }

            if (data.error) {
                swal(data.error, 'error');
            }
            if (data.success) {
                swal(data.success, 'success');
            }

            registrationsTable.column(0).checkboxes.deselectAll();
            registrationsTable.ajax.reload();
            toggleButtons();
        }


        // █▀▀█ █▀▀ █▀▀ █▀▀ █▀▀█ ▀▀█▀▀ 
        // █▄▄█ █── █── █▀▀ █──█ ──█── 
        // ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀ █▀▀▀ ──▀──
        $('body').on('click', '.btn-registration-accept', function() {
            let id = $(this).data('id');
            let url = '{{ route('registrations.accept') }}';

            $.ajax({
                data: {
                    id: id
                },
                type: 'POST',
                url: url,
                success: function(data) {
                    ajaxSuccess(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });


        // █▀▀▄ █▀▀ █▀▀ █── ─▀─ █▀▀▄ █▀▀ 
        // █──█ █▀▀ █── █── ▀█▀ █──█ █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ ▀▀▀
        $('body').on('click', '.btn-registration-decline', function() {
            let id = $(this).data('id');
            let url = '{{ route('registrations.decline') }}';

            swalConfirmation('Decline this registration?', 'Yes, decline it!', function() {
                $.ajax({
                    data: {
                        id: id
                    },
                    type: 'DELETE',
                    url: url,
                    success: function(data) {
                        ajaxSuccess(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            })
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = registrationsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }

        // █▀▀█ █▀▀ █▀▀ █▀▀ █▀▀█ ▀▀█▀▀ 　 █▀▀█ █── █── 
        // █▄▄█ █── █── █▀▀ █──█ ──█── 　 █▄▄█ █── █── 
        // ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀ █▀▀▀ ──▀── 　 ▀──▀ ▀▀▀ ▀▀▀
        $('#registration-accept-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('registrations.accept') }}';

            swalConfirmation(
                'Accept ' + id.length + ' registration(s) and all of its related information?',
                'Yes, accept it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'POST'
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

        // █▀▀▄ █▀▀ █▀▀ █── ─▀─ █▀▀▄ █▀▀ 　 █▀▀█ █── █── 
        // █──█ █▀▀ █── █── ▀█▀ █──█ █▀▀ 　 █▄▄█ █── █── 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀
        $('#registration-decline-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('registrations.decline') }}';

            swalConfirmation(
                'Decline ' + id.length + ' registration(s) and all of its related information?',
                'Yes, decline it!',
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
