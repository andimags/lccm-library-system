<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var shelfItemsTable = $('#table-shelf-items').DataTable({
            'columns': [{
                    data: 'checkbox'
                },
                {
                    data: 'image'
                },
                {
                    data: 'barcode'
                },
                {
                    data: 'title'
                },
                {
                    data: 'availability'
                },
                {
                    data: 'action'
                }
            ],
            // drawCallback: function(settings) {
            //     var api = this.api();

            //     // Calculate the sum of the quantity column
            //     var total = $('.copy').toArray().reduce(function(sum, input) {
            //         var copy = parseInt($(input).val()) || 0;
            //         return sum + copy;
            //     }, 0);

            //     // Remove existing total row, if any
            //     $(api.table().footer()).find('.total-row').remove();

            //     // Append the last row with the total quantity
            //     var totalRow = '<tr class="total-row"><td colspan="7">Total # of Items: ' + total +
            //         '</td></tr>';
            //     $(api.table().footer()).append(totalRow);
            // },
            columnDefs: [{
                    targets: 0,
                    checkboxes: {
                        selectRow: true,
                        stateSave: true
                    },
                    className: 'select-checkbox',
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (rowData['availability'].includes('available')) {
                            this.api().cell(td).checkboxes.disable();
                        }
                    }
                },
                {
                    targets: [0, 1, 2, 3, 5, ],
                    orderable: false
                },
            ],
            stateSave: true, // enable state saving
            stateDuration: 0, // retain saved state until the user clears the cache or closes the browser
            // other options...
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,
            select: true,
            rowId: 'id', // Set the idSrc option
            serverSide: true,
            processing: true,
            ajax: "{{ route('shelf.items.index') }}"
        });

        shelfItemsTable.column(0).checkboxes.deselectAll();

        var userMaxReservationCount = {{ auth()->user()->role == 'faculty' ? 5 : 3 }};

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



        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('#reserve-all-btn').click(function() {
            $('#reservation-form').trigger('reset');
            $('#reservation-modal').modal('show');
            $('#image-container').html('');
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

        // █▀▄▀█ █▀▀█ █▀▀▄ █▀▀█ █▀▀▀ █▀▀ 　 █▀▀ █──█ █▀▀ █── █▀▀ 
        // █─▀─█ █▄▄█ █──█ █▄▄█ █─▀█ █▀▀ 　 ▀▀█ █▀▀█ █▀▀ █── █▀▀ 
        // ▀───▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀──
        function manageShelfItem(data, url, method, text, confirmButtonText, formData = null,
            showConfirmation = true, callback = null) {
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
                        doAjaxCall(data, formData, method, url, callback);
                    }
                });
            } else {
                doAjaxCall(data, formData, method, url, callback);
            }
        }


        // █▀▀█ ──▀ █▀▀█ █─█ 　 █▀▀ █▀▀█ █── █── 
        // █▄▄█ ──█ █▄▄█ ▄▀▄ 　 █── █▄▄█ █── █── 
        // ▀──▀ █▄█ ▀──▀ ▀─▀ 　 ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀
        function doAjaxCall(data, formData = null, method, url, callback = null) {

            let frm = null;

            if (formData != null) {
                frm = new FormData(formData);
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

                    shelfItemsTable.column(0).checkboxes.deselectAll();
                    toggleButtons();
                    shelfItemsTable.ajax.reload();
                    $('#reservation-modal').modal('hide');

                    if (typeof callback === "function") {
                        callback(data);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
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

            shelfItemsTable.column(0).checkboxes.deselectAll();
            shelfItemsTable.ajax.reload();
            toggleButtons();
        }


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#reservation-form').submit(function(e) {
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

            // var frm = new FormData(this);

            let selectedRows = shelfItemsTable.column(0).checkboxes.selected();

            let id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            manageShelfItem({
                    ids: id
                },
                "{{ route('reservations.store') }}",
                'post',
                null,
                null,
                null,
                false
            );
        })

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = shelfItemsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#shelf-item-reserve-all,#shelf-item-remove-all').removeAttr('disabled');
                $('.shelf-item-count').html('(' + selectedRows.length + ')');
            } else {
                $('#shelf-item-reserve-all,#shelf-item-remove-all').attr('disabled', true);
                $('.shelf-item-count').html('');
            }
        }


        // █▀▀ █▀▀▄ █▀▀█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // █▀▀ █──█ █▄▄█ █▀▀▄ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        $(document).on('change', 'td.select-checkbox, th.select-checkbox', function() {
            toggleButtons();
        });


        // █▀▀█ █▀▀ █▀▄▀█ █▀▀█ ▀█─█▀ █▀▀ 　 ─▀─ ▀▀█▀▀ █▀▀ █▀▄▀█ 
        // █▄▄▀ █▀▀ █─▀─█ █──█ ─█▄█─ █▀▀ 　 ▀█▀ ──█── █▀▀ █─▀─█ 
        // ▀─▀▀ ▀▀▀ ▀───▀ ▀▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀ ──▀── ▀▀▀ ▀───▀
        $('body').on('click', '.btn-shelf-item-remove', function() {
            let id = $(this).data('id');

            $.ajax({
                data: {
                    id: id
                },
                type: 'POST',
                headers: {
                    'X-HTTP-Method-Override': 'DELETE'
                },
                url: "{{ route('shelf.items.destroy') }}",
                success: function(data) {
                    ajaxSuccess(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });


        // █▀▀█ █▀▀ █▀▄▀█ █▀▀█ ▀█─█▀ █▀▀ 　 █▀▀█ █── █── 
        // █▄▄▀ █▀▀ █─▀─█ █──█ ─█▄█─ █▀▀ 　 █▄▄█ █── █── 
        // ▀─▀▀ ▀▀▀ ▀───▀ ▀▀▀▀ ──▀── ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀        
        $('#shelf-item-remove-all').click(function() {
            let selectedRows = shelfItemsTable.column(0).checkboxes.selected();

            let id = [];

            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            swalConfirmation(
                'Remove ' + id.length + ' shelf item(s)?',
                'Yes, remove it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        headers: {
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        url: "{{ route('shelf.items.destroy') }}",
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

        $('#shelf-item-reserve-all').click(function() {
            let selectedRows = shelfItemsTable.column(0).checkboxes.selected();

            let id = [];

            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });


            swalConfirmation(
                'Reserve ' + id.length + ' shelf item(s)?',
                'Yes, reserve it!',
                function() {
                    $.ajax({
                        data: {
                            id: id
                        },
                        type: 'POST',
                        url: "{{ route('reservations.store') }}",
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
