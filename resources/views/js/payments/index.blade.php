<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var paymentColumns = [
            @if (auth()->user()->temp_role == 'librarian')
                {
                    data: 'checkbox',
                },      
            @endif
            {
                data: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'circulation_id',
            },
            {
                data: 'status',
            },
            {
                data: 'created_at',
                class: "text-nowrap"
            },
            {
                data: 'action',
            }
        ];

        @if (auth()->user()->temp_role == 'librarian')
            paymentColumns.splice(3, 0, {
                data: 'message',
            });

            paymentColumns.splice(4, 0, {
                data: 'borrower',
                class: "text-nowrap"
            });
        @else
            paymentColumns.splice(3, 0, {
                data: 'remark',
            });
        @endif

        // https://github.com/yajra/laravel-datatables/discussions/2758
        var paymentsTable = $('#table-payments').DataTable({
            columns: paymentColumns,
            @if (auth()->user()->temp_role == 'librarian')
            columnDefs: [
                {
                targets: 0,
                checkboxes: {
                    'selectRow': true
                },
                className: 'select-checkbox'
            }],            
            @endif
            searching: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: "{{ \Illuminate\Support\Facades\Route::currentRouteName() == 'payments.index' ? route('payments.index') : route('payments.archive') }}",
        });



        // ▀▀█▀▀ █▀▀█ █▀▀▀ ─▀─ █▀▀ █──█ 
        // ──█── █▄▄█ █─▀█ ▀█▀ █▀▀ █▄▄█ 
        // ──▀── ▀──▀ ▀▀▀▀ ▀▀▀ ▀── ▄▄▄█
        const tagifyGroups = new Tagify($('#groups')[0], {
            dropdown: {
                classname: "color-blue",
                enabled: 0, // disable suggestions
                maxItems: 5,
                position: "input", // place the dropdown near the typed text
                closeOnSelect: false, // keep the dropdown open after selecting a suggestion
                highlightFirst: true
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(';'),
            delimiters: [';'],
        });

        $.ajax({
            type: 'GET',
            url: "{{ route('settings.get.holding.options') }}",
            data: {
                fields: ['group']
            },
            success: function(data) {
                tagifyGroups.whitelist = data.group;
            },
            error: function(data) {
                console.log(data);
            }
        })

        const tagifyRoles = new Tagify($('#roles')[0], {
            whitelist: ['Student', 'Employee', 'Faculty', 'Librarian'],
            maxTags: 2,
            dropdown: {
                classname: "color-blue",
                position: "input", // place the dropdown near the typed text
                closeOnSelect: false, // keep the dropdown open after selecting a suggestion
                highlightFirst: true,
                enabled: 0, // <- show suggestions on focus
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(';'),
            delimiters: [';'],
            enforceWhitelist: true
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

        function toTitleCase(str) {
            return str.toLowerCase().replace(/(^|\s)\w/g, function(match) {
                return match.toUpperCase();
            });
        }

        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('#payment-add').click(function() {
            $('#payment-form').trigger('reset');
            $('#payment-modal-header').html('Add payment');
            $('#payment-modal-button').html('Add');
            $('#payment-form-action').val('add');
            $('#payment-modal').modal('show');
            imageFilePond.removeFiles();
        });


        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = paymentsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#payment-delete-all, #payment-force-delete-all, #payment-restore-all').removeAttr(
                    'disabled');
                $('.payment-count').html('(' + selectedRows.length + ')');
            } else {
                $('#payment-delete-all, #payment-force-delete-all, #payment-restore-all').attr('disabled',
                    true);
                $('.payment-count').html('');
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

            paymentsTable.column(0).checkboxes.deselectAll();
            toggleButtons();
            paymentsTable.ajax.reload();
            let action = $('#payment-form-action').val();
            $('#payment-form').trigger('reset');
            $('#payment-form-action').val(action);
        }

        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ ▀█▀ ──█── 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀──
        $('body').on('click', '.btn-payment-edit', function() {
            imageFilePond.removeFiles();
            removeInputErrors();
            $('#payment-form').trigger('reset');

            var user_id = $(this).data('id');

            $('#payment-modal-header').html('Update Payment');
            $('#payment-form').trigger('reset');
            $('#payment-modal-button').html('Update');
            $('#payment-modal').modal('show');
            $('#payment-form-action').val('edit');
            $('#payment-hidden-id').val(user_id);

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('payments.index') }}" + "/" + user_id + "/edit",
                success: function(data) {
                    $('#role').val(data.payment.role);
                    $('#id2').val(data.payment.id2);
                    $('#last_name').val(data.payment.last_name);
                    $('#first_name').val(data.payment.first_name);
                    $('#email').val(data.payment.email);
                    tagifyGroups.addTags(data.groups);
                    tagifyRoles.addTags(data.roles);

                    if (data.image) {
                        imageFilePond.addFile(data.image);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#payment-modal').modal('show');
            });
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = paymentsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }

        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-payment-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('payments.destroy') }}';

            swalConfirmation(
                'Delete this payment?',
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
        $('#payment-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('payments.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' payment(s)?',
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
        $('body').on('click', '.btn-payment-restore', function() {
            let id = $(this).data('id');
            let url = '{{ route('payments.restore') }}';

            swalConfirmation(
                'Restore this payment?',
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
        $('#payment-restore-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('payments.restore') }}';

            swalConfirmation(
                'Restore ' + id.length + ' payment(s)?',
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
        $('body').on('click', '.btn-payment-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('payments.force.delete') }}';

            swalConfirmation(
                'Permanently delete this payment?',
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
        $('#payment-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('payments.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' payment(s)?',
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


        // ▀█─█▀ ─▀─ █▀▀ █───█ 
        // ─█▄█─ ▀█▀ █▀▀ █▄█▄█ 
        // ──▀── ▀▀▀ ▀▀▀ ─▀─▀─
        $('body').on('click', '.btn-payment-view', function() {
            let id = $(this).data('id');
            let url = '{{ route('payments.get', ['id' => ':id']) }}'.replace(':id', id);

            $.ajax({
                type: 'get', // method shown on route:list
                url: url,
                success: function(data) {
                    console.log(data);
                    return;
                    $('#payment-modal-header').html('Payment #' + data.payment.id);
                    $('#message').val(data.payment.message);
                    $('#remark').val(data.payment.remark);

                    if (data.images) {
                        let html = '';

                        data.images.forEach(image => {
                            html += '<div><a href="' + image +
                                '" target="_blank"><img src="' + image +
                                '" class="img-thumbnail w-100 overflow-hidden" alt="..." style="max-height: 40vh; object-fit: cover;"></a></div>'
                        });

                        $('#payment-view-images').html(html);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#payment-modal').modal('show');
            });
        });

        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ ▀▀█▀▀ █▀▀█ ▀▀█▀▀ █──█ █▀▀ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 ▀▀█ ──█── █▄▄█ ──█── █──█ ▀▀█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀▀▀ ──▀── ▀──▀ ──▀── ─▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        function changeStatus(id, newStatus, remark = null) {
            let url = '{{ route('payments.change.status', ['id' => ':id']) }}'
                .replace(':id', id);

            $.ajax({
                type: 'POST',
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },
                data: {
                    status: newStatus,
                    remark: remark
                },
                url: url,
                success: function(data) {
                    ajaxSuccess(data);
                    paymentsTable.ajax.reload();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }


        // █▀▀█ █▀▀ █▀▀ █▀▀ █▀▀█ ▀▀█▀▀ 
        // █▄▄█ █── █── █▀▀ █──█ ──█── 
        // ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀ █▀▀▀ ──▀──
        $('body').on('click', '.btn-payment-accept', function() {
            let id = $(this).data('id');

            swalConfirmation('Accept this payment?', 'Yes, accept it!', function() {
                changeStatus(id, 'accepted');
            });
        });


        // █▀▀▄ █▀▀ █▀▀ █── ─▀─ █▀▀▄ █▀▀ 
        // █──█ █▀▀ █── █── ▀█▀ █──█ █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ ▀▀▀
        $('body').on('click', '.btn-payment-decline', async function() {
            const {
                value: remark
            } = await Swal.fire({
                input: 'textarea',
                inputLabel: 'Enter remarks',
                inputPlaceholder: 'Type your remark here...',
                inputAttributes: {
                    'aria-label': 'Type your remark here'
                },
                showCancelButton: true,
                confirmButtonColor: '#1572E8',
                confirmButtonText: 'Decline payment'
            });

            if (remark !== null && remark.trim() !== '') {
                changeStatus($(this).data('id'), 'declined', remark);
            } else {
                swal('Remark is required!', 'error');
            }
        });

    });
</script>
