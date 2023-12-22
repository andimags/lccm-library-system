<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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


        // First register any plugins
        $.fn.filepond.registerPlugin(FilePondPluginImagePreview);

        // Turn input element into a pond
        $('.my-pond').filepond();

        // Set allowMultiple property to true
        $('.my-pond').filepond('allowMultiple', true);

        // Listen for addfile event
        $('.my-pond').on('FilePond:addfile', function(e) {
            console.log('file added event', e);
        });

        $('#patron-add').click(function() {
            $('#patron-modal-header').html('Add patron');
            $('#patron-modal-button').html('Add');
            $('#patron-form-action').val('add');
            $('#patron-modal').modal('show');
        });


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#patron-form').submit(function(e) {
            e.preventDefault();

            $('.input_msg').each(function(i, obj) {
                $(this).html('');
            });

            $('.form-group').each(function(i, obj) {
                $(this).removeClass('has-error has-feedback');
            });

            function showInputErrors(data) {
                for (var key in data.msg) {
                    $('#' + String(key) + '_msg').html(String(data.msg[key]));
                    $('#form_group_' + String(key)).addClass(
                        'has-error has-feedback');
                }
            }

            var frm = new FormData(this);


            // ─█▀▀█ ░█▀▀▄ ░█▀▀▄ 
            // ░█▄▄█ ░█─░█ ░█─░█ 
            // ░█─░█ ░█▄▄▀ ░█▄▄▀
            if ($('#patron-form-action').val() == 'add') {

                $.ajax({
                    type: "post",
                    url: "{{ route('patrons.store') }}",
                    data: frm,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.code == 400) {
                            showInputErrors(data);
                        } else {
                            usersTable.draw();
                            $('#patron-form').trigger('reset');

                            swal(data.success);
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

                // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
                // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
                // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
            } else if ($('#patron-form-action').val() == 'edit') {

                var user_id = $('#patron-hidden-id').val();

                // for (var pair of frm.entries()){
                //     console.log(pair[0] + ', ' + pair[1]);
                // }
                frm.append('_method', 'PUT')

                $.ajax({
                    type: "post",
                    url: "{{ route('patrons.index') }}" + "/" + user_id,
                    data: frm,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data.name);
                        if (data.code == 400) {
                            showInputErrors(data);
                        } else {
                            usersTable.draw();
                            $('#patron-modal').modal('hide');

                            swal(data.success);
                        }
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
        $('body').on('click', '.btn-patron-edit', function() {

            $('.input_msg').html('');

            $('.form-group').removeClass('has-error has-feedback');

            var user_id = $(this).data('id');

            $('#patron-modal-header').html('Update User');
            $('#patron-form').trigger('reset');
            $('#patron-modal-button').html('Update');
            $('#patron-modal').modal('show');
            $('#patron-form-action').val('edit');
            $('#patron-hidden-id').val(user_id);

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('patrons.index') }}" + "/" + user_id + "/edit",
                success: function(data) {

                    console.log(data.image)
                    $('#role').val(data.role);
                    $('#id').val(data.patron.id);
                    $('#last_name').val(data.patron.last_name);
                    $('#first_name').val(data.patron.first_name);
                    $('#email').val(data.patron.email);
                    if (data.image != null) {

                        image.src = "{{ asset('images/patrons') }}/" + data.image;

                        $('#image-container').html(data.image);
                    } else {
                        $('#image-container').html('');
                    }
                    $('#patron-modal').modal('show');
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
            var selectedRows = usersTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#patron-delete-all, #patron-force-delete-all, #patron-restore-all').removeAttr('disabled');
                $('.patron-count').html('(' + selectedRows.length + ')');
            } else {
                $('#patron-delete-all, #patron-force-delete-all, #patron-restore-all').attr('disabled', true);
                $('.patron-count').html('');
            }
        });

        // █▀▄▀█ █▀▀█ █▀▀▄ █▀▀█ █▀▀▀ █▀▀ 　 █──█ █▀▀ █▀▀ █▀▀█ 
        // █─▀─█ █▄▄█ █──█ █▄▄█ █─▀█ █▀▀ 　 █──█ ▀▀█ █▀▀ █▄▄▀ 
        // ▀───▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ─▀▀▀ ▀▀▀ ▀▀▀ ▀─▀▀
        function manageUser(id, url, method, text, confirmButtonText, successMessage) {
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

                    $.ajax({
                        type: 'post', // method shown on route:list
                        data: {
                            id: id
                        },
                        headers: {
                            'X-HTTP-Method-Override': method
                        },
                        url: url,
                        success: function(data) {
                            usersTable.draw();
                            swal(successMessage);
                            $('#patron-delete-all, #patron-force-delete-all, #patron-restore-all')
                                .attr('disabled', true);
                            $('.patron-count').html('');
                            usersTable.column(0).checkboxes.deselectAll();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            })
        }

        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-patron-delete', function() {
            let id = $(this).data('id');

            manageUser(
                id,
                "{{ route('patrons.destroy') }}",
                'delete',
                'Delete record and all of its related information?',
                'Yes, delete it!',
                'User has been deleted!'
            );
        });


        // █▀▀█ █▀▀ █▀▀ ▀▀█▀▀ █▀▀█ █▀▀█ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █▄▄▀ █▀▀ ▀▀█ ──█── █──█ █▄▄▀ █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀─▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀▀ ▀─▀▀ ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-patron-restore', function() {
            let id = $(this).data('id');
            manageUser(
                id,
                "{{ route('patrons.restore') }}",
                'put',
                'Restore record and all of its related information?',
                'Yes, restore it!',
                'User has been restored!'
            );
        });
    });
</script>