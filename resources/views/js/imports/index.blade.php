<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var importsTable = $('#table-imports').DataTable({
            columns: [{
                    data: 'checkbox'
                },
                {
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'table'
                },
                {
                    data: 'success_count'
                },
                {
                    data: 'failed_count'
                },
                {
                    data: 'total_records'
                },
                {
                    data: 'librarian'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                    targets: [4],
                    orderable: true
                },
                {
                    targets: [3, 4],
                    searchable: true
                },
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
            ajax: "{{ \Illuminate\Support\Facades\Route::currentRouteName() == 'imports.index' ? route('imports.index') : route('imports.archive') }}",
        });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ ─▀─ █▀▀ █──█ 
        // ──█── █▄▄█ █─▀█ ▀█▀ █▀▀ █▄▄█ 
        // ──▀── ▀──▀ ▀▀▀▀ ▀▀▀ ▀── ▄▄▄█
        const tagifySettings = {
            mode: 'select',
            dropdown: {
                classname: "color-blue",
                enabled: 0, // disable suggestions
                maxItems: 5,
                position: "input", // place the dropdown near the typed text
                closeOnSelect: true, // keep the dropdown open after selecting a suggestion
                highlightFirst: true
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(';'),
            delimiters: [';'],
        };

        const tagifyFund = new Tagify($('#fund')[0], tagifySettings);
        const tagifyVendor = new Tagify($('#vendor')[0], tagifySettings);
        const tagifyLocation = new Tagify($('#location')[0], tagifySettings);

        $.ajax({
            type: 'GET',
            url: "{{ route('settings.get.holding.options') }}",
            data: {
                fields: ['fund', 'vendor', 'location']
            },
            success: function(data) {
                tagifyFund.whitelist = data.fund;
                tagifyVendor.whitelist = data.vendor;
                tagifyLocation.whitelist = data.location;
            },
            error: function(data) {
                console.log(data);
            }
        })

        // █▀▀ ─▀─ █── █▀▀ █▀▀█ █▀▀█ █▀▀▄ █▀▀▄ 
        // █▀▀ ▀█▀ █── █▀▀ █──█ █──█ █──█ █──█ 
        // ▀── ▀▀▀ ▀▀▀ ▀▀▀ █▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀─
        $.fn.filepond.registerPlugin(FilePondPluginImagePreview);
        $.fn.filepond.registerPlugin(FilePondPluginFileValidateType);

        var FilePondExcelSettings = {
            imagePreviewHeight: 175,
            instantUpload: false,
            acceptedFileTypes: ['application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ],
        }

        var patronFilePond = FilePond.create($('#patron-file')[0], FilePondExcelSettings);
        var collectionFilePond = FilePond.create($('#collection-file')[0], FilePondExcelSettings);
        var copyFilePond = FilePond.create($('#copy-file')[0], FilePondExcelSettings);

        function toggleSubmitBtn(filepond, element) {
            if (filepond.getFile()) {
                element.attr("disabled", false);
            } else {
                element.attr("disabled", true);
            }
        }

        patronFilePond.on('addfile', (error, file) => {
            toggleSubmitBtn(patronFilePond, $('#patron-modal-button'));
        });

        patronFilePond.on('removefile', (error, file) => {
            toggleSubmitBtn(patronFilePond, $('#patron-modal-button'));
        });

        collectionFilePond.on('removefile', (error, file) => {
            toggleSubmitBtn(collectionFilePond, $('#collection-modal-button'));
        });

        collectionFilePond.on('addfile', (error, file) => {
            toggleSubmitBtn(collectionFilePond, $('#collection-modal-button'));
        });

        copyFilePond.on('addfile', (error, file) => {
            toggleSubmitBtn(copyFilePond, $('#copy-modal-button'));
        });

        copyFilePond.on('removefile', (error, file) => {
            toggleSubmitBtn(copyFilePond, $('#copy-modal-button'));
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


        // █── █▀▀█ █▀▀█ █▀▀▄ ─▀─ █▀▀▄ █▀▀▀ 
        // █── █──█ █▄▄█ █──█ ▀█▀ █──█ █─▀█ 
        // ▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀──▀ ▀▀▀▀
        const showLoading = () => {
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we process your request.',
                icon: 'info',
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            })
        };

        const hideLoading = () => {
            Swal.close();
        };

        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('#import-add').click(function() {
            $('#import-patrons-form, #import-collections-form, #import-copies-form').trigger('reset');
            $('#import-modal-header').html('Import file');
            $('#import-modal-button').html('Add');
            $('#import-form-action').val('add');
            $('#import-modal').modal('show');

            removeInputErrors();
        });


        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = importsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#import-delete-all, #import-force-delete-all, #import-restore-all')
                    .removeAttr('disabled');
                $('.import-count').html('(' + selectedRows.length + ')');
            } else {
                $('#import-delete-all, #import-force-delete-all, #import-restore-all').attr(
                    'disabled', true);
                $('.import-count').html('');
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

            $('#import-modal').modal('hide');

            if (data.error) {
                swal(data.error, 'error');
            }
            if (data.success) {
                swal(data.success, 'success');
            }

            importsTable.column(0).checkboxes.deselectAll();
            importsTable.ajax.reload();
            toggleButtons();
        }


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#import-patrons-form').submit(function(e) {
            e.preventDefault();

            removeInputErrors();
            showLoading();

            let frm = new FormData(this);

            const files = patronFilePond.getFiles();
            const file = files[0]?.file ?? null;

            if (file) {
                frm.append('patron_file', file);
            }

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('imports.patrons') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    hideLoading();
                    ajaxSuccess(data);
                },
                error: function(data) {
                    hideLoading();
                }
            });
        })

        $('#import-collections-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();

            let frm = new FormData(this);

            const files = collectionFilePond.getFiles();
            const file = files[0]?.file ?? null;

            if (file) {
                frm.append('collection_file', file);
            }

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('imports.collections') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data);
                    hideLoading();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        })

        $('#import-copies-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();

            let frm = new FormData(this);

            const files = copyFilePond.getFiles();
            const file = files[0]?.file ?? null;

            if (file) {
                frm.append('copy_file', file);
            }

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('imports.copies') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data);
                    hideLoading();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        })


        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = importsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-import-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('imports.destroy') }}';

            swalConfirmation(
                'Delete this import and all of its related information?',
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
        $('#import-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('imports.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' import(s) and all of its related information?',
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
        $('body').on('click', '.btn-import-restore', function() {
            let id = $(this).data('id');
            let url = '{{ route('imports.restore') }}';

            swalConfirmation(
                'Restore this import and all of its related information?',
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
        $('#import-restore-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('imports.restore') }}';

            swalConfirmation(
                'Restore ' + id.length + ' import(s) and all of its related information?',
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
        $('body').on('click', '.btn-import-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('imports.force.delete') }}';

            swalConfirmation(
                'Permanently delete this import and all of its related information?',
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
        $('#import-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('imports.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' import(s) and all of its related information?',
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
