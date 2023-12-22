<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var announcementsTable = $('#table-announcements').DataTable({
            columns: [{
                    data: 'checkbox'
                },
                {
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'title'
                },
                {
                    data: 'visibility'
                },
                {
                    data: 'start_at'
                },
                {
                    data: 'end_at'
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
            // scrollX: true,
            // scrollCollapse: true,
            // fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: "{{ \Illuminate\Support\Facades\Route::currentRouteName() == 'announcements.index' ? route('announcements.index') : route('announcements.archive') }}",
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

        imageFilePond.on('addfile', function(error, file) {
            console.log('Error:', error)
            console.log('File added:', file.filename);
        });

        // ── ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ █▀▀ ── 
        // ▀▀ ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ ▀▀█ ▀▀ 
        // ── ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀ ──        
        // █▀▀█ █▀▀ █▀▄▀█ █▀▀█ ▀█─█▀ █▀▀ 　 █───█ █──█ ─▀─ ▀▀█▀▀ █▀▀ █▀▀ █▀▀█ █▀▀█ █▀▀ █▀▀ █▀▀ 
        // █▄▄▀ █▀▀ █─▀─█ █──█ ─█▄█─ █▀▀ 　 █▄█▄█ █▀▀█ ▀█▀ ──█── █▀▀ ▀▀█ █──█ █▄▄█ █── █▀▀ ▀▀█ 
        // ▀─▀▀ ▀▀▀ ▀───▀ ▀▀▀▀ ──▀── ▀▀▀ 　 ─▀─▀─ ▀──▀ ▀▀▀ ──▀── ▀▀▀ ▀▀▀ █▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀
        $('#title').keyup(function() {
            var sanitizedValue = $(this).val().replace(/\s+/g, ' ');
            $(this).val(sanitizedValue);
        });

        // ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $.validator.addMethod("greaterThanOrEqual", function(value, element, param) {
            var startAt = Date.parse($("#start_at").val());
            var endAt = Date.parse(value);

            return endAt >= startAt;
        }, "End date must be greater than or equal to start date");

        var announcementValidator = $("#announcement-form").validate({
            rules: {
                title: {
                    required: true,
                },
                start_at: {
                    required: true,
                    dateISO: true
                },
                end_at: {
                    required: true,
                    greaterThanOrEqual: true,
                    dateISO: true
                },
            },
            onkeyup: function(element, event) {
                this.element(element);
                toggleSubmitBtn();
            },
            onfocusout: function(element, event) {
                this.element(element);
                toggleSubmitBtn();
            },
            errorPlacement: function(error, element) {
                let elementName = $(element).attr("name");
                let errorMessage = error.text();

                $('#' + elementName + '_msg').html(errorMessage);
                $('#form_group_' + elementName).addClass('has-error has-feedback');
            },
            success: function(label, element) {
                let elementName = $(element).attr("name");

                $('#' + elementName + '_msg').html('');
                $('#form_group_' + elementName).removeClass('has-error has-feedback');
            }
        });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 　 █▀▀▄ ──█── █──█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀── 　 ▀▀▀─ ──▀── ▀──▀
        function toggleSubmitBtn() {
            let numberOfInvalids = announcementValidator.numberOfInvalids();

            if (numberOfInvalids == 0 && $('#title').val() && $('#start_at').SelectedDate != '' && $('#end_at')
                .val() != '') {
                $("#announcement-modal-button").attr("disabled", false);
            } else {
                $("#announcement-modal-button").attr("disabled", true);
            }
        }

        $("#start_at").on('change', function() {
            $(this).valid();
            $("#end_at").valid();
            toggleSubmitBtn();
        });

        $("#end_at").on('change', function() {
            $(this).valid();
            toggleSubmitBtn();
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
        $('#announcement-add').click(function() {
            $("#announcement-modal-button").attr("disabled", true);
            removeInputErrors();
            $('#announcement-form').trigger('reset');
            $('#announcement-modal-header').html('Add Announcement');
            $('#announcement-modal-button').html('Add Announcement');
            $('#announcement-form-action').val('add');
            $('#announcement-modal').modal('show');
            imageFilePond.removeFiles();
        });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = announcementsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#announcement-delete-all, #announcement-force-delete-all, #announcement-restore-all')
                    .removeAttr('disabled');
                $('.announcement-count').html('(' + selectedRows.length + ')');
            } else {
                $('#announcement-delete-all, #announcement-force-delete-all, #announcement-restore-all')
                    .attr(
                        'disabled', true);
                $('.announcement-count').html('');
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

            if (data.code == 400) {
                showInputErrors(data);
                return;
            }
            if (data.success) {
                swal(data.success, 'success');
            }

            announcementsTable.column(0).checkboxes.deselectAll();
            toggleButtons();
            announcementsTable.ajax.reload();
            let action = $('#announcement-form-action').val();
            $('#announcement-form').trigger('reset');
            $('#announcement-form-action').val(action);
            imageFilePond.removeFiles();
        }


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#announcement-form').submit(function(e) {
            e.preventDefault();
            console.log('test');

            removeInputErrors();

            let frm = new FormData(this);

            imageFilePond.getFiles().forEach(element => {
                const file = new File([element.file], element.file.name);
                frm.append('image[]', file);
                console.log(file);
            });

            if ($('#announcement-form-action').val() == 'add') {
                // ─█▀▀█ ░█▀▀▄ ░█▀▀▄ 
                // ░█▄▄█ ░█─░█ ░█─░█ 
                // ░█─░█ ░█▄▄▀ ░█▄▄▀
                $.ajax({
                    data: frm,
                    type: 'POST',
                    url: '{{ route('announcements.store') }}',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        ajaxSuccess(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

            } else if ($('#announcement-form-action').val() == 'edit') {
                // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
                // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
                // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
                let id = $('#announcement-hidden-id').val();
                let url = '{{ route('announcements.update', ['id' => ':id']) }}'.replace(':id', id);

                $.ajax({
                    data: frm,
                    type: 'POST',
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    url: url,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        ajaxSuccess(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        })


        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ ▀█▀ ──█── 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀──
        $('body').on('click', '.btn-announcement-edit', function() {
            imageFilePond.removeFiles();
            removeInputErrors();

            var id = $(this).data('id');

            $('#announcement-form').trigger('reset');
            $('#announcement-modal-header').html('Update Announcement');
            $('#announcement-modal-button').html('Update');
            $('#announcement-modal').modal('show');
            $('#announcement-form-action').val('edit');
            $('#announcement-hidden-id').val(id);

            $.ajax({
                type: 'GET', // method shown on route:list
                url: "{{ route('announcements.index') }}" + "/" + id + "/edit",
                success: function(data) {
                    console.log(data);
                    $('#title').val(data.announcement.title);
                    $('#content').val(data.announcement.content);
                    $('#visibility').val(data.announcement.visibility);
                    $('#start_at').val(data.start_at);
                    $('#end_at').val(data.end_at);

                    imageFilePond.addFiles(data.images);
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#announcement-modal').modal('show');
            });
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = announcementsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-announcement-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('announcements.destroy') }}';

            swalConfirmation(
                'Delete this announcement and all of its related information?',
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
        $('#announcement-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('announcements.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' announcement(s) and all of its related information?',
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
        $('body').on('click', '.btn-announcement-restore', function() {
            let id = $(this).data('id');
            let url = '{{ route('announcements.restore') }}';

            swalConfirmation(
                'Restore this announcement and all of its related information?',
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
        $('#announcement-restore-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('announcements.restore') }}';

            swalConfirmation(
                'Restore ' + id.length + ' announcement(s) and all of its related information?',
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
        $('body').on('click', '.btn-announcement-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('announcements.force.delete') }}';

            swalConfirmation(
                'Permanently delete this announcement and all of its related information?',
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
        $('#announcement-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('announcements.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' announcement(s) and all of its related information?',
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

        // █▀▀█ █▀▀█ █▀▀ ▀█─█▀ ─▀─ █▀▀ █───█ 
        // █──█ █▄▄▀ █▀▀ ─█▄█─ ▀█▀ █▀▀ █▄█▄█ 
        // █▀▀▀ ▀─▀▀ ▀▀▀ ──▀── ▀▀▀ ▀▀▀ ─▀─▀─
        $('body').on('click', '.btn-announcement-view', function() {
            let id = $(this).data('id');

            $.ajax({
                type: 'GET', // method shown on route:list
                url: "{{ route('announcements.index') }}" + "/" + id + "/edit",
                success: function(data) {
                    console.log(data);
                    $('#preview-title').html(data.announcement.title);
                    $('#preview-content').html(data.announcement.content);

                    if (data.images) {
                        let html = '';

                        data.images.forEach(image => {
                            html += '<div class="col-lg-4"><a href="' + image +
                                '"><img src="' + image +
                                '" class="img-thumbnail w-100 overflow-hidden" alt="..." style="max-height: 40vh; object-fit: cover;"></a></div>'
                        });

                        $('#preview-images').html(html);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#announcement-preview-modal').modal('show');
            });
        });
    });
</script>
