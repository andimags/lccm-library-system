<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var materialsTable = $('#table-materials').DataTable({
            'columns': [{
                    data: 'checkbox'
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
                    data: 'author',
                },
                {
                    data: 'type',
                },
                {
                    data: 'availability',
                },
                {
                    data: 'action',
                }
            ],
            'columnDefs': [{
                    targets: [0, 1, 4, 5],
                    orderable: false
                },
                {
                    targets: [2, 3, 4, 6],
                    searchable: true
                },
                {
                    width: '150px',
                    targets: [3, 4]
                },
                {
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    },
                    className: 'select-checkbox'
                }
            ],
            searching: true,
            scrollX: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: {
                url: @if (Route::currentRouteName() === 'materials.manage')
                    "{{ route('materials.manage') }}"
                @elseif (Route::currentRouteName() === 'materials.archive')
                    "{{ route('materials.archive') }}"
                @else
                    "{{ route('materials.index') }}"
                @endif
            }
        });

        // █▀▀ ─▀─ █── █▀▀ █▀▀█ █▀▀█ █▀▀▄ █▀▀▄ 
        // █▀▀ ▀█▀ █── █▀▀ █──█ █──█ █──█ █──█ 
        // ▀── ▀▀▀ ▀▀▀ ▀▀▀ █▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀─
        $.fn.filepond.registerPlugin(FilePondPluginImagePreview);
        $.fn.filepond.registerPlugin(FilePondPluginFileValidateType);

        $('#image').filepond();

        var imageInput = document.querySelector('#image');
        var imageFilePond = FilePond.create(imageInput, {
            imagePreviewHeight: 175,
            instantUpload: false,
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/webp'],
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

        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('#material-add').click(function() {
            resetForm();
            $('#material-form').trigger('reset');
            $('#material-modal-header').html('Add material');
            $('#material-modal-button').html('Add');
            $('#material-form-action').val('add');
            $('#material-modal').modal('show');
            $('#image-container').html('');
        });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            var selectedRows = materialsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#material-delete-all, #material-force-delete-all, #material-restore-all').removeAttr(
                    'disabled');
                $('.material-count').html('(' + selectedRows.length + ')');
            } else {
                $('#material-delete-all, #material-force-delete-all, #material-restore-all').attr(
                    'disabled', true);
                $('.material-count').html('');
            }
        }

        // █▀▀ █▀▀▄ █▀▀█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // █▀▀ █──█ █▄▄█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
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

        // █▀▀█ █▀▀ █▀▀ █▀▀ ▀▀█▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 
        // █▄▄▀ █▀▀ ▀▀█ █▀▀ ░░█░░ 　 █▀▀ █░░█ █▄▄▀ █░▀░█ 
        // ▀░▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ░░▀░░ 　 ▀░░ ▀▀▀▀ ▀░▀▀ ▀░░░▀
        function resetForm(){
            imageFilePond.removeFiles();

            $('#material-form').trigger('reset');
            $('#quantity').val(1);
            $('#available_quantity').val(1);
        }

        // █▀▄▀█ █▀▀█ █▀▀▄ █▀▀█ █▀▀▀ █▀▀ 　 █▀▄▀█ █▀▀█ ▀▀█▀▀ █▀▀ █▀▀█ ─▀─ █▀▀█ █── 
        // █─▀─█ █▄▄█ █──█ █▄▄█ █─▀█ █▀▀ 　 █─▀─█ █▄▄█ ──█── █▀▀ █▄▄▀ ▀█▀ █▄▄█ █── 
        // ▀───▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀───▀ ▀──▀ ──▀── ▀▀▀ ▀─▀▀ ▀▀▀ ▀──▀ ▀▀▀
        function manageMaterial(data, url, method, text, confirmButtonText, formData = null, showConfirmation =
            true, callback = null) {
            if (showConfirmation) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmButtonText,
                    iconColor: '#F25961'
                }).then((result) => {
                    if (result.isConfirmed) {
                        doAjaxCall(data, formData, method, url);
                    }
                });
            } else {
                doAjaxCall(data, formData, method, url);
            }
        }


        // █▀▀█ ──▀ █▀▀█ █─█ 　 █▀▀ █▀▀█ █── █── 
        // █▄▄█ ──█ █▄▄█ ▄▀▄ 　 █── █▄▄█ █── █── 
        // ▀──▀ █▄█ ▀──▀ ▀─▀ 　 ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀
        function doAjaxCall(data, formData = null, method, url, callback = null) {
            let frm = formData != null ? new FormData(formData) : null;

            let files = imageFilePond.getFiles();

            if (files.length > 0) {
                const imageFile = files[0].file;
                frm.append('image', imageFile);
            }

            $.ajax({
                data: formData != null ? frm : data,
                type: 'POST',
                headers: {
                    'X-HTTP-Method-Override': method
                },
                url: url,
                processData: formData != null ? false : true,
                contentType: formData != null ? false :
                    'application/x-www-form-urlencoded; charset=UTF-8',
                success: function(data) {
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

                    materialsTable.column(0).checkboxes.deselectAll();
                    toggleButtons();
                    $('#reservation-modal').modal('hide');
                    materialsTable.ajax.reload();
                    resetForm();

                    if (callback) {
                        callback(data);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#material-form').submit(function(e) {
            e.preventDefault();

            removeInputErrors();

            var frm = new FormData(this);

            // ─█▀▀█ ░█▀▀▄ ░█▀▀▄ 
            // ░█▄▄█ ░█─░█ ░█─░█ 
            // ░█─░█ ░█▄▄▀ ░█▄▄▀
            if ($('#material-form-action').val() == 'add') {

                let url = "{{ route('materials.store') }}";

                manageMaterial(
                    null,
                    url,
                    'POST',
                    null,
                    null,
                    this,
                    false
                );

                // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
                // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
                // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
            } else if ($('#material-form-action').val() == 'edit') {

                let id = $('#material-hidden-id').val();

                frm.append('_method', 'PUT')

                $.ajax({
                    type: "post",
                    url: "{{ route('materials.index') }}" + "/" + id,
                    data: frm,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.code == 400) {
                            showInputErrors(data);
                        } else {
                            materialsTable.draw();
                            $('#material-modal').modal('hide');

                            swal(data.success);
                        }

                        resetForm();
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        })


        // █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █▀▀█ █▀▀ █▀▀ █▀▀ █▀▀█ ▀█─█▀ █▀▀ 　 █▀▄▀█ █▀▀█ ▀▀█▀▀ █▀▀ █▀▀█ ─▀─ █▀▀█ █── 
        // █▀▀▄ ──█── █──█ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ █▄▄▀ ─█▄█─ █▀▀ 　 █─▀─█ █▄▄█ ──█── █▀▀ █▄▄▀ ▀█▀ █▄▄█ █── 
        // ▀▀▀─ ──▀── ▀──▀ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀─▀▀ ──▀── ▀▀▀ 　 ▀───▀ ▀──▀ ──▀── ▀▀▀ ▀─▀▀ ▀▀▀ ▀──▀ ▀▀▀
        $('body').on('click', '.btn-material-reserve', function() {
            let id = $(this).data('id');

            $('#reservation-modal').modal('show');
            $('#reservation-hidden-id').val(id);
        });


        // █▀▀ █▀▀ █▀▀▄ █▀▀▄ 　 █▀▀█ █▀▀ █▀▀ █▀▀ █▀▀█ ▀█─█▀ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ▀▀█ █▀▀ █──█ █──█ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ █▄▄▀ ─█▄█─ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ▀▀▀ ▀▀▀ ▀──▀ ▀▀▀─ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀─▀▀ ──▀── ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $('#reservation-form').submit(function(e) {
            e.preventDefault();

            showInputErrors(e);

            let id = $('#reservation-hidden-id').val();

            manageMaterial({
                    directRequest: true,
                    id: id,
                    type: 'App\\Models\\Material'
                },
                "{{ route('reservations.store') }}",
                'post',
                null,
                null,
                'Reservation has been sent!',
                false
            );
        })

        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ ▀█▀ ──█── 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀──
        $('body').on('click', '.btn-material-edit', function() {

            $('.input_msg').html('');
            $('.form-group').removeClass('has-error has-feedback');
            $('#material-form-action').val('edit');

            var id = $(this).data('id');

            $('#material-modal-header').html('Update Material');
            $('#material-form').trigger('reset');
            $('#material-modal-button').html('Update');
            $('#material-modal').modal('show');
            $('#material-form-action').val('edit');
            $('#material-hidden-id').val(id);

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('materials.index') }}" + "/" + id + "/edit",
                success: function(data) {

                    console.log(data);
                    $('#name').val(data.name);
                    $('#note').val(data.note);
                    $('#description').val(data.description);
                    $('#acquisition_method').val(data.acquisition_method);
                    $('#type').val(data.type);
                    $('#quantity').val(data.quantity);
                    $('#available_quantity').val(data.available_quantity);
                    $('#author').val(data.author);
                    $('#barcode').val(data.barcode);

                    if (data.images[0].file_name != null) {
                        $('#image-container').html(
                            '<div class="avatar avatar-xl mt-3"><img src="{{ asset('images/materials') }}/' +
                            data.images[0].file_name +
                            '" alt="..." class="avatar-img rounded"></div>');
                    }

                    $('#material-modal').modal('show');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-material-delete', function() {
            let id = $(this).data('id');

            manageMaterial({
                    id: id
                },
                "{{ route('materials.destroy') }}",
                'DELETE',
                'Delete material and all of its related information?',
                'Yes, delete it!'
            );
        });


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀█ █── █── 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▄▄█ █── █── 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#material-delete-all').click(function() {
            let selectedRows = materialsTable.column(0).checkboxes.selected();

            let id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            manageMaterial({
                    id: id
                },
                "{{ route('materials.destroy') }}",
                'DELETE',
                'Delete ' + selectedRows.length.toString() +
                ' material(s) and all of its related information?',
                'Yes, delete it!'
            );
        });


        // █▀▀█ █▀▀ █▀▀ ▀▀█▀▀ █▀▀█ █▀▀█ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▄▄▀ █▀▀ ▀▀█ ──█── █──█ █▄▄▀ █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀─▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-material-restore', function() {
            let id = $(this).data('id');

            manageMaterial({
                    id: id
                },
                "{{ route('materials.restore') }}",
                'PUT',
                'Restore material and all of its related information?',
                'Yes, restore it!'
            );
        });


        // █▀▀█ █▀▀ █▀▀ ▀▀█▀▀ █▀▀█ █▀▀█ █▀▀ 　 █▀▀█ █── █── 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▄▄▀ █▀▀ ▀▀█ ──█── █──█ █▄▄▀ █▀▀ 　 █▄▄█ █── █── 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀─▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#material-restore-all').click(function() {
            let selectedRows = materialsTable.column(0).checkboxes.selected();

            let id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            manageMaterial({
                    id: id
                },
                "{{ route('materials.restore') }}",
                'PUT',
                'Delete ' + selectedRows.length.toString() +
                ' material(s) and all of its related information?',
                'Yes, restore it!'
            );
        });



        // █▀▀ █▀▀█ █▀▀█ █▀▀ █▀▀ 　 █▀▀▄ █▀▀ █── 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▀▀ █──█ █▄▄▀ █── █▀▀ 　 █──█ █▀▀ █── 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-material-force-delete', function() {
            let id = $(this).data('id');

            manageMaterial({
                    id: id
                },
                "{{ route('materials.force.delete') }}",
                'DELETE',
                'Delete material permanently and all of its related information?',
                'Yes, delete it!'
            );
        });


        // █▀▀ █▀▀█ █▀▀█ █▀▀ █▀▀ 　 █▀▀▄ █▀▀ █── 　 █▀▀█ █── █── 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▀▀ █──█ █▄▄▀ █── █▀▀ 　 █──█ █▀▀ █── 　 █▄▄█ █── █── 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#material-force-delete-all').click(function() {
            let selectedRows = materialsTable.column(0).checkboxes.selected();

            let id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            manageMaterial({
                    id: id
                },
                "{{ route('materials.force.delete') }}",
                'DELETE',
                'Delete ' + selectedRows.length.toString() +
                ' material(s) permanently and all of its related information?',
                'Yes, delete it!'
            );
        });

        // █▀▀█ █▀▀▄ █▀▀▄ 　 ▀▀█▀▀ █▀▀█ 　 █▀▀ █▀▀█ █▀▀█ ▀▀█▀▀ 
        // █▄▄█ █──█ █──█ 　 ──█── █──█ 　 █── █▄▄█ █▄▄▀ ──█── 
        // ▀──▀ ▀▀▀─ ▀▀▀─ 　 ──▀── ▀▀▀▀ 　 ▀▀▀ ▀──▀ ▀─▀▀ ──▀──
        $('body').on('click', '.btn-material-cart', function() {
            let id = $(this).data('id');

            manageMaterial({
                    id: id,
                    type: 'App\\Models\\Material'
                },
                "{{ route('materials.store') }}",
                'POST',
                null,
                null,
                null,
                false
            );
        });
    });
</script>
