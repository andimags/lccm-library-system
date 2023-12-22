<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var offSiteCirculationsTable = $('#table-off-site-circulations').DataTable({
            'columns': [
                @if (auth()->user()->temp_role == 'librarian')
                    {
                        data: 'checkbox'
                    },
                @endif
                {
                    data: 'barcode',
                },
                {
                    data: 'checked_out',
                },
                {
                    data: 'checked_in_at',
                },
                {
                    data: 'due_at',
                },
                {
                    data: 'grace_period_days',
                },
                {
                    data: 'total_fines'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                }
            ],
            initComplete: function(settings) {
                var api = this.api();

                api.cells(
                    api.rows(function(idx, data, node) {
                        return data.disabled;
                    }).indexes(),
                    0
                ).checkboxes.disable();
            },
            columnDefs: [
                @if (auth()->user()->temp_role == 'librarian')
                    {
                        'targets': 0,
                        'checkboxes': {
                            'selectRow': true
                        },
                        className: 'select-checkbox'
                    },                
                @endif
                {
                    targets: [0, 1, 2],
                    orderable: false
                }
            ],
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,
            select: true,
            idSrc: 'id', // Set the idSrc option
            serverSide: true,
            processing: true,
            ajax: {
                url: @if (Route::currentRouteName() === 'off.site.circulations.archive')
                    "{{ route('off.site.circulations.archive') }}"
                @else
                    "{{ route('off.site.circulations.index') }}"
                @endif
            }
        });

        // █▀▀ ─▀─ █── █▀▀ █▀▀█ █▀▀█ █▀▀▄ █▀▀▄ 
        // █▀▀ ▀█▀ █── █▀▀ █──█ █──█ █──█ █──█ 
        // ▀── ▀▀▀ ▀▀▀ ▀▀▀ █▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀─
        $.fn.filepond.registerPlugin(FilePondPluginImagePreview);
        $.fn.filepond.registerPlugin(FilePondPluginFileValidateType);

        var imageFilePond = FilePond.create($('#image')[0], {
            imagePreviewHeight: 100,
            instantUpload: false,
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/webp'],
            maxFiles: 3,
            maxParallelUploads: 3,
            allowMultiple: true,
        });

        function toggleSubmitBtn(filepond, element) {
            if (filepond.getFile()) {
                element.attr("disabled", false);
            } else {
                element.attr("disabled", true);
            }
        }

        imageFilePond.on('addfile', (error, file) => {
            toggleSubmitBtn(imageFilePond, $('#payment-modal-button'));
        });

        imageFilePond.on('removefile', (error, file) => {
            toggleSubmitBtn(imageFilePond, $('#payment-modal-button'));
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
        $('#btn-off-site-circulation-create').click(function() {
            window.location.href = '{{ route('off.site.circulations.create') }}';
        });

        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('#btn-off-site-circulation-check-in').click(function() {
            $('#barcode-modal').modal('show');
            $('#barcode_form_action').val('check-in');
            $('#barcode-modal-header').html('Check-in item');
            $('#barcode-modal-button').html('Check-in');
        });

        $('#btn-off-site-circulation-renew').click(function() {
            $('#barcode-form').trigger('reset');
            $('#barcode-modal').modal('show');
            $('#barcode_form_action').val('renew');
            $('#barcode-modal-header').html('Renew circulation');
            $('#barcode-modal-button').html('Next');
        });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = offSiteCirculationsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#off-site-circulation-delete-all, #off-site-circulation-force-delete-all, #off-site-circulation-restore-all')
                    .removeAttr('disabled');
                $('.off-site-circulation-count').html('(' + selectedRows.length + ')');
            } else {
                $('#off-site-circulation-delete-all, #off-site-circulation-force-delete-all, #off-site-circulation-restore-all')
                    .attr(
                        'disabled', true);
                $('.off-site-circulation-count').html('');
            }
        }


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
            if (data.code == '400') {
                showInputErrors(data);
                return;
            }

            $('#barcode-modal').modal('hide');
            $('#payment-modal').modal('hide');
            imageFilePond.removeFiles();

            if (data.error) {
                swal(data.error, 'error');
            }
            if (data.success) {
                swal(data.success, 'success');
            }

            offSiteCirculationsTable.column(0).checkboxes.deselectAll();
            offSiteCirculationsTable.ajax.reload();
            toggleButtons();
        }

        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀█ █▀▀ █▀▀ █▀▀ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ──▀──
        $("#barcode-modal,#payment-modal").on("hidden.bs.modal", function() {
            $('#barcode-form,#payment-form').trigger('reset');
        });

        // █▀▀▄ █▀▀█ █▀▀█ █▀▀ █▀▀█ █▀▀▄ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 
        // █▀▀▄ █▄▄█ █▄▄▀ █── █──█ █──█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 
        // ▀▀▀─ ▀──▀ ▀─▀▀ ▀▀▀ ▀▀▀▀ ▀▀▀─ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀
        $('#barcode-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();

            if ($('#barcode_form_action').val() == 'check-in') {
                // █▀▀ █──█ █▀▀ █▀▀ █─█ ── ─▀─ █▀▀▄ 
                // █── █▀▀█ █▀▀ █── █▀▄ ▀▀ ▀█▀ █──█ 
                // ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀─▀ ── ▀▀▀ ▀──▀

                let url = '{{ route('off.site.circulations.check.in', ['barcode' => ':barcode']) }}'.replace(':barcode', $('#barcode').val());

                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    url: url,
                    success: function(data) {
                        ajaxSuccess(data);
                        offSiteCirculationsTable.ajax.reload();
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            } else if ($('#barcode_form_action').val() == 'renew') {
                // █▀▀█ █▀▀ █▀▀▄ █▀▀ █───█ 
                // █▄▄▀ █▀▀ █──█ █▀▀ █▄█▄█ 
                // ▀─▀▀ ▀▀▀ ▀──▀ ▀▀▀ ─▀─▀─

                let url = '{{ route('off.site.circulations.get.due.at', ['barcode' => ':barcode']) }}'
                    .replace(':barcode', $('#barcode').val());

                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(data) {
                        ajaxSuccess(data);

                        if (!data.error) {
                            $('#new_due_at').prop('min', data.due_at);
                            $('#new_due_at').val(data.due_at);
                            $('#hidden_barcode').val(data.barcode);
                            $('#circulation-renewal-modal').modal('show');
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        })


        // █▀▀█ █▀▀ █▀▀▄ █▀▀ █───█ █▀▀█ █── 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 
        // █▄▄▀ █▀▀ █──█ █▀▀ █▄█▄█ █▄▄█ █── 　 █▀▀ █──█ █▄▄▀ █─▀─█ 
        // ▀─▀▀ ▀▀▀ ▀──▀ ▀▀▀ ─▀─▀─ ▀──▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀
        $('#circulation-renewal-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();

            let frm = new FormData(this);
            let url = '{{ route('off.site.circulations.renew', ['barcode' => ':barcode']) }}'.replace(
                ':barcode', $('#hidden_barcode').val());

            $.ajax({
                data: frm,
                type: 'POST',
                url: url,
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data);
                    $('#circulation-renewal-modal').modal('hide');
                    offSiteCirculationsTable.ajax.reload();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        // █▀▀ █▀▀▄ █▀▀█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // █▀▀ █──█ █▄▄█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        $(document).on('change', 'td.select-checkbox, th.select-checkbox', function() {
            var selectedRows = offSiteCirculationsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#off-site-circulation-delete-all, #off-site-circulation-force-delete-all, #off-site-circulation-restore-all')
                    .removeAttr(
                        'disabled');
                $('.off-site-circulation-count').html('(' + selectedRows.length + ')');
            } else {
                $('#off-site-circulation-delete-all, #off-site-circulation-force-delete-all, #off-site-circulation-restore-all')
                    .attr(
                        'disabled', true);
                $('.off-site-circulation-count').html('');
            }
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = offSiteCirculationsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-off-site-circulation-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('off.site.circulations.destroy') }}';

            swalConfirmation(
                'Delete this off-site circulation and all of its related information?',
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
        $('#off-site-circulation-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('off.site.circulations.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length +
                ' off-site circulation(s) and all of its related information?',
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
        $('body').on('click', '.btn-off-site-circulation-restore', function() {
            let id = $(this).data('id');
            let url = '{{ route('off.site.circulations.restore') }}';

            swalConfirmation(
                'Restore this off-site circulation and all of its related information?',
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
        $('#off-site-circulation-restore-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('off.site.circulations.restore') }}';

            swalConfirmation(
                'Restore ' + id.length +
                ' off-site circulation(s) and all of its related information?',
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
        $('body').on('click', '.btn-off-site-circulation-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('off.site.circulations.force.delete') }}';

            swalConfirmation(
                'Permanently delete this off-site circulations and all of its related information?',
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
        $('#off-site-circulation-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('off.site.circulations.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' off-site circulations(s) and all of its related information?',
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

        $('body').on('click', '.btn-off-site-circulation-send-payment', function() {
            imageFilePond.removeFiles();
            let id = $(this).data('id');
            $('#off-site-circulation-hidden-id').val(id);
            $('#payment-modal-button').attr("disabled", true);
            $('#payment-modal').modal('show');
        });

        // █▀▀ █▀▀ █▀▀▄ █▀▀▄ 　 █▀▀█ █▀▀█ █──█ █▀▄▀█ █▀▀ █▀▀▄ ▀▀█▀▀ 
        // ▀▀█ █▀▀ █──█ █──█ 　 █──█ █▄▄█ █▄▄█ █─▀─█ █▀▀ █──█ ──█── 
        // ▀▀▀ ▀▀▀ ▀──▀ ▀▀▀─ 　 █▀▀▀ ▀──▀ ▄▄▄█ ▀───▀ ▀▀▀ ▀──▀ ──▀──
        $('#payment-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();

            let frm = new FormData(this);

            imageFilePond.getFiles().forEach(element => {
                const file = new File([element.file], element.file.name);
                frm.append('image[]', file);
                console.log(file);
            });

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('payments.store') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });


        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀█ █──█ ▀▀█▀▀ █▀▀█ █▀▄▀█ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀ 　 █▀▀ ─▀─ █▀▀▄ █▀▀ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▄▄█ █──█ ──█── █──█ █─▀─█ █▄▄█ ──█── ▀█▀ █── 　 █▀▀ ▀█▀ █──█ █▀▀ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀──▀ ─▀▀▀ ──▀── ▀▀▀▀ ▀───▀ ▀──▀ ──▀── ▀▀▀ ▀▀▀ 　 ▀── ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀
        $('#enable-automatic-fines').change(function() {
            let value = $(this).prop('checked') ? 'yes' : 'no';
            let url = '{{ route('settings.toggle.enable.automatic.fines', ['value' => ':value']) }}'.replace(':value', value);

            $.ajax({
                type: 'POST',
                url: url
            });
        });
    });
</script>
