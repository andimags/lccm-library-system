<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var holdingOptionsTable = $('#table-holding-options').DataTable({
            columns: [{
                    data: 'checkbox'
                },
                {
                    data: 'DT_RowIndex'
                },
                {
                    data: 'value'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                    targets: [],
                    orderable: true
                },
                {
                    targets: [],
                    searchable: true
                },
                {
                    width: '50px',
                    targets: [3]
                },
                {
                    targets: 0,
                    checkboxes: {
                        'selectRow': true
                    },
                    className: 'select-checkbox'
                }
            ],
            searching: false,
            paging: false,
            scrollX: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            // serverSide: true,
            processing: true,
            ajax: "{{ route('settings.index', ['field' => ':field']) }}".replace(':field',
                $('#field').val())
        });

        // ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        var holdingOptionValidator = $("#holding-option-form").validate({
            rules: {
                value: {
                    required: true,
                }
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
            let numberOfInvalids = holdingOptionValidator.numberOfInvalids();

            if (numberOfInvalids == 0 && $('#value').val()) {
                $("#holding-option-modal-button").attr("disabled", false);
            } else {
                $("#holding-option-modal-button").attr("disabled", true);
            }
        }

        // █▀▀ █── █▀▀ █▀▀█ ▀█─█▀ █▀▀ 
        // █── █── █▀▀ █▄▄█ ─█▄█─ █▀▀ 
        // ▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ ──▀── ▀▀▀
        new Cleave('.loaning-period-input', {
            numeral: true,
            numeralPositiveOnly: true,
            delimiter: '',
            numeralIntegerScale: 3,
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
        $('#holding-option-add').click(function() {
            removeInputErrors();
            $("#holding-option-modal-button").attr("disabled", true);
            $('#holding-option-modal-header').html('Add Value');
            $('#holding-option-modal-button').html('Add Value');
            $('#holding-option-form-action').val('add');
            $('#holding-option-modal').modal('show');
        });

        $('#field').change(function() {
            let field = $(this).val();

            holdingOptionsTable.ajax.url(
                "{{ route('settings.index', ['field' => ':field']) }}".replace(
                    ':field', field)).load();
        });


        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = holdingOptionsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#holding-option-delete-all').removeAttr('disabled');
                $('.holding-option-count').html('(' + selectedRows.length + ')');
            } else {
                $('#holding-option-delete-all').attr('disabled', true);
                $('.holding-option-count').html('');
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

            holdingOptionsTable.column(0).checkboxes.deselectAll();
            toggleButtons();
            holdingOptionsTable.ajax.reload();
            let action = $('#patron-form-action').val();
            $('#holding-option-form').trigger('reset');
            $('#holding-option-form-action').val(action);
        }

        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#holding-option-form').submit(function(e) {
            e.preventDefault();

            removeInputErrors();

            $.ajax({
                data: {
                    field: $('#field').val(),
                    value: $('#value').val(),
                },
                type: 'POST',
                url: '{{ route('holding.options.store') }}',
                success: function(data) {
                    ajaxSuccess(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        })


        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ ▀█▀ ──█── 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀──
        $('body').on('click', '.btn-patron-edit', function() {

            $('.input_msg').html('');

            imageFilePond.removeFiles();

            removeInputErrors();


            var user_id = $(this).data('id');

            $('#patron-modal-header').html('Update Patron');
            $('#patron-form').trigger('reset');
            $('#patron-modal-button').html('Update');
            $('#patron-modal').modal('show');
            $('#patron-form-action').val('edit');
            $('#patron-hidden-id').val(user_id);

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('patrons.index') }}" + "/" + user_id + "/edit",
                success: function(data) {
                    $('#role').val(data.patron.role);
                    $('#id2').val(data.patron.id2);
                    $('#last_name').val(data.patron.last_name);
                    $('#first_name').val(data.patron.first_name);
                    $('#email').val(data.patron.email);
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#patron-modal').modal('show');
            });
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = holdingOptionsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-holding-option-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('holding.options.destroy') }}';

            swalConfirmation(
                'Delete this holding option and all of its related information?',
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
        $('#holding-option-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('holding.options.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' holding option(s) and all of its related information?',
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


        $('body').on('change', '.loaning-period-input', function() {
            let details = $(this).prop('name').split('_');
            let input = $(this).val();

            if(!input){
                $(this).val(1);
                return;
            }

            $.ajax({
                data: {
                    role: details[0],
                    prefix: details[1],
                    field: 'no_of_days',
                    input: input
                },
                type: 'POST',
                url: '{{ route('loaning.periods.store') }}',
                success: function(data) {
                    ajaxSuccess(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        $('body').on('change', '.grace-period-input', function() {
            let details = $(this).prop('name').split('_');
            let input = $(this).val();

            if(!input){
                $(this).val(0);
                return;
            }

            $.ajax({
                data: {
                    role: details[0],
                    prefix: details[1],
                    field: 'grace_period_days',
                    input: input
                },
                type: 'POST',
                url: '{{ route('loaning.periods.store') }}',
                success: function(data) {
                    ajaxSuccess(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    });
</script>
