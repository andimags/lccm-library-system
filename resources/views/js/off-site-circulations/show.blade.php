<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var offSiteCirculationFinesTable = $('#table-off-site-circulation-fines').DataTable({
            'columns': [{
                    data: 'checkbox'
                },
                {
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'reason',
                },
                {
                    data: 'note',
                },
                {
                    data: 'price',
                    class: "text-nowrap"
                },
                {
                    data: 'librarian',
                    className: 'text-nowrap'
                },
                {
                    data: 'created_at',
                    className: 'text-nowrap'
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
                    width: '150px',
                    targets: [2, 3]
                }
            ],
            searching: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: '{{ $offSiteCirculationStatus == 'active' ? route('fines.index', ['circulation_id' => $offSiteCirculation->id]) : route('fines.archive', ['circulation_id' => $offSiteCirculation->id]) }}'
        });

        var renewalsTable = $('#table-renewals').DataTable({
            'columns': [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'new_due_at',
                },
                {
                    data: 'old_due_at',
                },
                {
                    data: 'librarian',
                },
                {
                    data: 'created_at',
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
            ajax: '{{ $offSiteCirculationStatus == 'active' ? route('renewals.index', ['circulation_id' => $offSiteCirculation->id]) : route('renewals.archive', ['circulation_id' => $offSiteCirculation->id]) }}'
        });

        // ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $.validator.addMethod('greaterThanZero', function(value, element) {
            const rawValue = priceCleave.getRawValue();
            const numericValue = parseFloat(rawValue.replace(/,/g, ''));
            return numericValue > 0.00;
        }, 'Value must be greater than or equal to 0.00');

        var offSiteCirculationFineValidator = $("#off-site-circulation-fine-form").validate({
            rules: {
                reason: {
                    required: true
                },
                price: {
                    required: true,
                    greaterThanZero: true
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
            let numberOfInvalids = offSiteCirculationFineValidator.numberOfInvalids();

            if (numberOfInvalids == 0 && $('#reason').val() && $('#price').val()) {
                $("#off-site-circulation-fine-modal-button").attr("disabled", false);
            } else {
                $("#off-site-circulation-fine-modal-button").attr("disabled", true);
            }
        }

        // █▀▀ █── █▀▀ █▀▀█ ▀█─█▀ █▀▀ 
        // █── █── █▀▀ █▄▄█ ─█▄█─ █▀▀ 
        // ▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ ──▀── ▀▀▀
        var priceCleave = new Cleave('#price', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalScale: 2,
            numeralDecimalMark: '.',
            numeralForceDecimal: true,
            numeralIntegerScale: 5,
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

        function removeErrorMessages() {
            $('.input_msg').each(function(i, obj) {
                $(this).html('');
            });

            $('.form-group').each(function(i, obj) {
                $(this).removeClass('has-error has-feedback');
            });
        }


        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('#off-site-circulation-fine-add').click(function() {
            $("#off-site-circulation-fine-modal-button").attr("disabled", true);
            $('#off-site-circulation-fine-modal-header').html('Add Fine');
            $('#off-site-circulation-fine-modal-button').html('Add Fine');
            $('#off-site-circulation-fine-form-action').val('add');
            $('#off-site-circulation-fine-modal').modal('show');
        });

        // █▀▀ █▀▀▄ █▀▀█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // █▀▀ █──█ █▄▄█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        $(document).on('change', 'td.select-checkbox, th.select-checkbox', function() {
            var selectedRows = offSiteCirculationFinesTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#off-site-circulation-fine-delete-all, #off-site-circulation-fine-force-delete-all, #off-site-circulation-fine-restore-all')
                    .removeAttr(
                        'disabled');
                $('.off-site-circulation-fine-count').html('(' + selectedRows.length + ')');
            } else {
                $('#off-site-circulation-fine-delete-all, #off-site-circulation-fine-force-delete-all, #off-site-circulation-fine-restore-all')
                    .attr(
                        'disabled', true);
                $('.off-site-circulation-fine-count').html('');
            }
        });

        // █▀▀ █░░█ █▀▀█ █░░░█ 　 ░▀░ █▀▀▄ █▀▀█ █░░█ ▀▀█▀▀ 　 █▀▀ █▀▀█ █▀▀█ 
        // ▀▀█ █▀▀█ █░░█ █▄█▄█ 　 ▀█▀ █░░█ █░░█ █░░█ ░░█░░ 　 █▀▀ █▄▄▀ █▄▄▀ 
        // ▀▀▀ ▀░░▀ ▀▀▀▀ ░▀░▀░ 　 ▀▀▀ ▀░░▀ █▀▀▀ ░▀▀▀ ░░▀░░ 　 ▀▀▀ ▀░▀▀ ▀░▀▀
        function showInputErrors(data) {
            for (let key in data.msg) {
                $('#' + String(key) + '_msg').html(String(data.msg[key][0]));
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

        function ajaxSuccess(data) {
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

            offSiteCirculationFinesTable.column(0).checkboxes.deselectAll();
            // toggleButtons();
            offSiteCirculationFinesTable.ajax.reload();
            $('#circulation-renewal-modal').modal('hide');
            $('#off-site-circulation-fine-modal').modal('hide');
        }

        $('#off-site-circulation-fine-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();

            let frm = new FormData(this);
            let circulation_id =
                '{{ $offSiteCirculation->id }}'; // Get the value of $offSiteCirculation->id


            // █▀▀█ █▀▀▄ █▀▀▄ 
            // █▄▄█ █──█ █──█ 
            // ▀──▀ ▀▀▀─ ▀▀▀─
            if ($('#off-site-circulation-fine-form-action').val() == 'add') {
                $.ajax({
                    data: frm,
                    type: 'POST',
                    url: '{{ route('fines.store', ['circulation_id' => ':circulation_id']) }}'
                        .replace(':circulation_id', circulation_id),
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        ajaxSuccess(data);
                        Livewire.emit('fineAdded');
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
            // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
            // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
            // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
            else if ($('#off-site-circulation-fine-form-action').val() == 'edit') {
                let id = $('#off-site-circulation-fine-hidden-id').val();
                let url = '{{ route('fines.update', ['id' => ':id']) }}'.replace(':id', id);

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
                        $('#off-site-circulation-fine-modal').modal('hide');
                        Livewire.emit('fineAdded');
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        });

        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ ▀█▀ ──█── 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀──
        $('body').on('click', '.btn-off-site-circulation-fine-edit', function() {

            removeInputErrors();

            let id = $(this).data('id');

            $('#off-site-circulation-fine-form-action').val('edit');
            $('#off-site-circulation-fine-modal-header').html('Update Fine');
            $('#off-site-circulation-fine-modal-button').html('Update Fine');
            $('#off-site-circulation-fine-modal').modal('show');
            $('#off-site-circulation-fine-form-action').val('edit');
            $('#off-site-circulation-fine-hidden-id').val(id);

            $.ajax({
                type: 'GET', // method shown on route:list
                url: '{{ route('fines.edit', ['id' => ':id']) }}'.replace(':id', id),
                success: function(data) {
                    $('#reason').val(data.reason);
                    $('#note').val(data.note);
                    $('#price').val(data.price);
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#off-site-circulation-fine-modal').modal('show');
            });
        });


        // █▀▀ █──█ █▀▀ █▀▀ █─█ ── ─▀─ █▀▀▄ 
        // █── █▀▀█ █▀▀ █── █▀▄ ▀▀ ▀█▀ █──█ 
        // ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀─▀ ── ▀▀▀ ▀──▀
        $('#btn-off-site-circulation-check-in').click(function() {
            let url = '{{ route('off.site.circulations.check.in', ['barcode' => ':barcode']) }}'
                .replace(':barcode', '{{ $offSiteCirculation->copy->barcode }}');

            swalConfirmation(
                'Check-in this circulation?',
                'Yes, check it in!',
                function() {
                    $.ajax({
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                            Livewire.emit('fineAdded');
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


        // █▀▀█ █▀▀ █▀▀▄ █▀▀ █───█ █▀▀█ █── 
        // █▄▄▀ █▀▀ █──█ █▀▀ █▄█▄█ █▄▄█ █── 
        // ▀─▀▀ ▀▀▀ ▀──▀ ▀▀▀ ─▀─▀─ ▀──▀ ▀▀▀
        $('#btn-off-site-circulation-renew').click(function() {
            $('#circulation-renewal-modal').modal('show');
        });


        // █▀▀█ █▀▀ █▀▀▄ █▀▀ █───█ █▀▀█ █── 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 
        // █▄▄▀ █▀▀ █──█ █▀▀ █▄█▄█ █▄▄█ █── 　 █▀▀ █──█ █▄▄▀ █─▀─█ 
        // ▀─▀▀ ▀▀▀ ▀──▀ ▀▀▀ ─▀─▀─ ▀──▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀
        $('#circulation-renewal-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();

            let frm = new FormData(this);
            let url = '{{ route('off.site.circulations.renew', ['barcode' => ':barcode']) }}'.replace(
                ':barcode', '{{ $offSiteCirculation->copy->barcode }}');

            $.ajax({
                data: frm,
                type: 'POST',
                url: url,
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data);
                    renewalsTable.ajax.reload();
                    Livewire.emit('fineAdded');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 　 █▀▀ ─▀─ █▀▀▄ █▀▀ █▀▀ 　 █▀▀ ▀▀█▀▀ █▀▀█ ▀▀█▀▀ █──█ █▀▀ 
        // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 　 █▀▀ ▀█▀ █──█ █▀▀ ▀▀█ 　 ▀▀█ ──█── █▄▄█ ──█── █──█ ▀▀█ 
        // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ 　 ▀── ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ 　 ▀▀▀ ──▀── ▀──▀ ──▀── ─▀▀▀ ▀▀▀
        $('#select-fines-status').change(function() {
            let id = '{{ $offSiteCirculation->id }}';
            let url = '{{ route('off.site.circulations.update.fines.status', ['id' => ':id']) }}'
                .replace(':id', id);

            $.ajax({
                type: 'POST',
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },
                data: {
                    status: $(this).val()
                },
                url: url,
                success: function(data) {
                    ajaxSuccess(data);
                    Livewire.emit('fineAdded');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = offSiteCirculationFinesTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-off-site-circulation-fine-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('fines.destroy') }}';

            swalConfirmation(
                'Delete this fine?',
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
                            Livewire.emit('fineAdded');
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
        $('#off-site-circulation-fine-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('fines.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' fine(s)?',
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
                            Livewire.emit('fineAdded');
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
        $('body').on('click', '.btn-off-site-circulation-fine-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('fines.force.delete') }}';

            swalConfirmation(
                'Permanently delete this fine?',
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
                            Livewire.emit('fineAdded');
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
        $('#off-site-circulation-fine-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('fines.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' fine(s)?',
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
                            Livewire.emit('fineAdded');
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


        $("#off-site-circulation-fine-modal").on("hidden.bs.modal", function() {
            $('#off-site-circulation-fine-form').trigger('reset');
            removeInputErrors();
        });

        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#btn-off-site-circulation-delete').click(function() {
            let url = '{{ route('off.site.circulations.destroy') }}';
            let id = '{{ $offSiteCirculation->id }}';

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
                            if (data.success) {
                                swal(data.success, 'success');
                            }

                            setTimeout(function() {
                                window.location.href =
                                    '{{ route('off.site.circulations.index') }}';
                            }, 2000); // 2000 milliseconds = 2 seconds
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
        $('#btn-off-site-circulation-restore').click(function() {
            let url = '{{ route('off.site.circulations.restore') }}';
            let id = '{{ $offSiteCirculation->id }}';

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
                            if (data.success) {
                                swal(data.success, 'success');
                            }

                            setTimeout(function() {
                                window.location.href =
                                    '{{ route('off.site.circulations.archive') }}';
                            }, 2000); // 2000 milliseconds = 2 seconds
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
        $(document).on('click', '#btn-off-site-circulation-force-delete', function() {
            let url = '{{ route('off.site.circulations.force.delete') }}';
            let id = '{{ $offSiteCirculation->id }}';

            swalConfirmation(
                'Delete off-site circulation permanently and all of its related information?',
                'Yes, Delete it!',
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
                            if (data.success) {
                                swal(data.success, 'success');
                            }

                            setTimeout(function() {
                                window.location.href =
                                    '{{ route('off.site.circulations.archive') }}';
                            }, 2000); // 2000 milliseconds = 2 seconds
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


        // █▀▄▀█ █▀▀█ █▀▀█ █─█ 　 █▀▀█ █▀▀ 　 █── █▀▀█ █▀▀ ▀▀█▀▀ 
        // █─▀─█ █▄▄█ █▄▄▀ █▀▄ 　 █▄▄█ ▀▀█ 　 █── █──█ ▀▀█ ──█── 
        // ▀───▀ ▀──▀ ▀─▀▀ ▀─▀ 　 ▀──▀ ▀▀▀ 　 ▀▀▀ ▀▀▀▀ ▀▀▀ ──▀──
        $(document).on('click', '#btn-mark-copy-as-lost', function() {
            let id = '{{ $offSiteCirculation->id }}';
            let url = '{{ route('off.site.circulations.mark.as.lost', ['id' => ':id']) }}'
                .replace(':id', id);

            swalConfirmation(
                'Mark this copy as lost?',
                'Yes, mark it!',
                function() {
                    $.ajax({
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                            Livewire.emit('fineAdded');
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            );
        });


        // █──█ █▀▀▄ █▀▀▄ █▀▀█ 　 █▀▄▀█ █▀▀█ █▀▀█ █─█ 　 █▀▀█ █▀▀ 　 █── █▀▀█ █▀▀ ▀▀█▀▀ 
        // █──█ █──█ █──█ █──█ 　 █─▀─█ █▄▄█ █▄▄▀ █▀▄ 　 █▄▄█ ▀▀█ 　 █── █──█ ▀▀█ ──█── 
        // ─▀▀▀ ▀──▀ ▀▀▀─ ▀▀▀▀ 　 ▀───▀ ▀──▀ ▀─▀▀ ▀─▀ 　 ▀──▀ ▀▀▀ 　 ▀▀▀ ▀▀▀▀ ▀▀▀ ──▀──
        $(document).on('click', '#btn-undo-mark-copy-as-lost', function() {
            let id = '{{ $offSiteCirculation->id }}';
            let url = '{{ route('off.site.circulations.undo.mark.as.lost', ['id' => ':id']) }}'
                .replace(':id', id);

            swalConfirmation(
                'Remove lost status for this copy?',
                'Yes, remove it!',
                function() {
                    $.ajax({
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        url: url,
                        success: function(data) {
                            ajaxSuccess(data);
                            Livewire.emit('fineAdded');
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            );
        });
    });
</script>
