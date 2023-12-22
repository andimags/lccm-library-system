<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var tempCheckOutItemsTable = $('#table-temp-check-out-items').DataTable({
            'columns': [{
                    data: 'DT_RowIndex',
                },
                {
                    data: 'barcode',
                },
                {
                    data: 'title',
                    class: "text-nowrap"
                },
                {
                    data: 'availability'
                },
                {
                    data: 'date_due_input',
                },
                {
                    data: 'grace_period_days',
                },
                {
                    data: 'action',
                }
            ],
            searching: false,
            'columnDefs': [{
                    targets: [0, 1, 2],
                    orderable: false
                },
                {
                    width: '100px',
                    targets: [2]
                }
            ],
            drawCallback: function(settings) {
                var table = this.api();

                if (table.rows().count() === 0) {
                    $('#btn-remove-all,#btn-submit').attr('disabled', true);
                } else {
                    $('#btn-remove-all').removeAttr('disabled');
                    if ($('#id2').val() != '') {
                        $('#btn-submit').removeAttr('disabled');
                    }
                }
            },
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,
            select: true,
            idSrc: 'id', // Set the idSrc option
            // serverSide: true,
            processing: true,
            ajax: '{{ route('temp.check.out.items.index') }}'
        });

        const tagifySettings = {
            mode: 'select',
            dropdown: {
                position: 'all', // place the dropdown near the typed text
                closeOnSelect: true
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(';'),
            delimiters: [';'],
            // enforceWhitelist: true,
        };

        const tagifySearchPatron = new Tagify($('#search-patron')[0], tagifySettings);
        // const tagifySearchCopy = new Tagify($('#search-copy')[0], tagifySettings);


        // ▀▀█▀▀ ─▀─ ▀▀█▀▀ █── █▀▀ 　 █▀▀ █▀▀█ █▀▀ █▀▀ 
        // ──█── ▀█▀ ──█── █── █▀▀ 　 █── █▄▄█ ▀▀█ █▀▀ 
        // ──▀── ▀▀▀ ──▀── ▀▀▀ ▀▀▀ 　 ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀
        function toTitleCase(str) {
            return str.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        }

        // tagifySearchCopy.on('input', $.debounce(500, function(e) {
        //     tagifySearchCopy.whitelist = [];
        //     let input = e.detail.value;

        //     if (!input) {
        //         return;
        //     }

        //     let url = '{{ route('copies.search', ['barcode' => ':barcode']) }}'.replace(':barcode',
        //         input);

        //     $.ajax({
        //         type: 'GET',
        //         url: url,
        //         success: function(data) {
        //             const whitelist = [];

        //             data.forEach(element => {
        //                 whitelist.push({
        //                     value: element.barcode,
        //                     title: element.collection.title,
        //                 })
        //             });

        //             tagifySearchCopy.whitelist = whitelist;
        //             tagifySearchCopy.loading(false).dropdown.show(
        //                 input) // render the suggestions dropdown
        //         },
        //         error: function(data) {
        //             console.log(data);
        //         }
        //     })
        // }));

        // tagifySearchCopy.on('dropdown:select', function(e) {
        //     let barcode = e.detail.data.value;  
        //     tempCheckOutItemsStore(barcode);
        // });

        $('#search-copy').on('keydown', function(e) {
            if (e.key === 'Enter') {
                let barcode = $(this).val();
                tempCheckOutItemsStore(barcode);
                $(this).val('');
            }
        });

        function tempCheckOutItemsStore(barcode){
            let url = '{{ route('temp.check.out.items.store') }}';

            $.ajax({
                data: {
                    barcode: barcode,
                    borrowerId: $('#id2').val()
                },
                type: 'POST',
                url: url,
                success: function(data) {
                    console.log(data);
                    // return;
                    ajaxSuccess(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        tagifySearchPatron.on('input', $.debounce(500, function(e) {
            tagifySearchPatron.whitelist = [];
            let input = e.detail.value;

            if (!input) {
                return;
            }

            let url = '{{ route('patrons.search', ['id' => ':id']) }}'.replace(':id',
                input);

            $.ajax({
                type: 'GET',
                url: url,
                success: function(data) {
                    const whitelist = [];

                    data.forEach(row => {
                        let roles = [];

                        row.roles.forEach(element => {
                            roles.push(toTitleCase(element.name));
                        });

                        whitelist.push({
                            value: row.id2,
                            title: row.last_name + ', ' + row
                                .first_name,
                            roles: roles.join(', '),
                            totalOnLoanItems: row
                                .off_site_circulations_count
                        })
                    });

                    tagifySearchPatron.whitelist = whitelist;
                    tagifySearchPatron.loading(false).dropdown.show(
                        input) // render the suggestions dropdown
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }));

        tagifySearchPatron.on('dropdown:select', function(e) {
            let patron = e.detail.data;

            $('#name_td').html(patron.title);
            $('#id_td').html(patron.value);
            $('#id2').val(patron.value);
            $('#roles_td').html(patron.roles);
            $('#total_on_loan_items_td').html(patron.totalOnLoanItems);
            // tagifySearchCopy.setDisabled(false);
            $("#search-copy").removeAttr("disabled");

            if (tempCheckOutItemsTable.rows().count() != 0) {
                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    url: '{{ route('temp.check.out.items.remove.all') }}',
                    success: function(data) {
                        tempCheckOutItemsTable.ajax.reload();
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        });


        // █▀▀█ ──▀ █▀▀█ █─█ 　 █▀▀ █──█ █▀▀ █▀▀ █▀▀ █▀▀ █▀▀ 
        // █▄▄█ ──█ █▄▄█ ▄▀▄ 　 ▀▀█ █──█ █── █── █▀▀ ▀▀█ ▀▀█ 
        // ▀──▀ █▄█ ▀──▀ ▀─▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀
        function ajaxSuccess(data) {
            if (data.code == '400') {
                showInputErrors(data);
                return;
            }

            if (data.error) {
                swal(data.error, 'error');
                return;
            }
            if (data.success) {
                swal(data.success, 'success');
            }
            if (data.info) {
                swal(data.info, 'info');
            }

            tempCheckOutItemsTable.column(0).checkboxes.deselectAll();
            tempCheckOutItemsTable.ajax.reload();
            toggleButtons();
        }

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            if (
                $('#id').val() === '' || tempCheckOutItemsTable.data().rows().count() == 0) {
                $('#btn-submit').attr('disabled', true);
            } else {
                $('#btn-submit').removeAttr('disabled');
            }

            if (tempCheckOutItemsTable.data().rows().count() == 0) {
                $('#btn-remove-all').attr('disabled', true);
            } else {
                $('#btn-remove-all').removeAttr('disabled');
            }
        }

        $('#status, #due_at').on('change', function() {
            toggleButtons();
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



        // █▀▄▀█ █▀▀█ █▀▀▄ █▀▀█ █▀▀▀ █▀▀ 　 ▀▀█▀▀ █▀▀ █▀▄▀█ █▀▀█ 　 ─▀─ ▀▀█▀▀ █▀▀ █▀▄▀█ █▀▀ 
        // █─▀─█ █▄▄█ █──█ █▄▄█ █─▀█ █▀▀ 　 ──█── █▀▀ █─▀─█ █──█ 　 ▀█▀ ──█── █▀▀ █─▀─█ ▀▀█ 
        // ▀───▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ──▀── ▀▀▀ ▀───▀ █▀▀▀ 　 ▀▀▀ ──▀── ▀▀▀ ▀───▀ ▀▀▀
        function manageTempCheckOutItem(data, url, method, text, confirmButtonText, formData = null,
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
            let frm = formData != null ? new FormData(formData) : null;

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

                    tempCheckOutItemsTable.ajax.reload();

                    if (callback) {
                        callback(data);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀
        $('body').on('click', '.btn-temp-check-out-item-delete', function() {
            let id = $(this).data('id');
            console.log(id);
            let url = '{{ route('temp.check.out.items.destroy', ['id' => ':id']) }}'.replace(':id', id);

            manageTempCheckOutItem({},
                url,
                'DELETE',
                null,
                null,
                null,
                false
            );
        });


        // █▀▀█ █▀▀ █▀▄▀█ █▀▀█ ▀█─█▀ █▀▀ 　 █▀▀█ █── █── 
        // █▄▄▀ █▀▀ █─▀─█ █──█ ─█▄█─ █▀▀ 　 █▄▄█ █── █── 
        // ▀─▀▀ ▀▀▀ ▀───▀ ▀▀▀▀ ──▀── ▀▀▀ 　 ▀──▀ ▀▀▀ ▀▀▀
        $('#btn-remove-all').on('click', function(e) {
            e.preventDefault();

            manageTempCheckOutItem({},
                '{{ route('temp.check.out.items.remove.all') }}',
                'DELETE',
                null,
                null,
                null,
                false
            );
        });


        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 　 █▀▀▄ █──█ █▀▀ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █──█ █▄▄█ ──█── █▀▀ 　 █──█ █──█ █▀▀ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀▀▀─ ▀──▀ ──▀── ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ▀▀▀
        $('body').on('change', '.date-due-input', function() {
            let url = '{{ route('temp.check.out.items.change.date.due', ['id' => ':id']) }}'.replace(
                ':id', $(this).data('id'));

            manageTempCheckOutItem({
                    due_at: $(this).val()
                },
                url,
                'PUT',
                null,
                null,
                null,
                false
            );
        });
    });
</script>
