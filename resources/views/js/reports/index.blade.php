<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var reportsTable = $('#table-reports').DataTable({
            columns: [{
                    data: 'checkbox'
                },
                {
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'report_type'
                },
                {
                    data: 'file_type'
                },
                {
                    data: 'librarian'
                },
                {
                    data: 'created_at'
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
            scrollX: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: "{{ \Illuminate\Support\Facades\Route::currentRouteName() == 'reports.index' ? route('reports.index') : route('reports.archive') }}",
        });

        // ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $.validator.addMethod("greaterThanOrEqual", function(value, element, param) {
            let elementName = $(element).attr('name');
            let form = $(element).closest('form');
            var startAt = Date.parse(form.find($("." + elementName.replace('end', 'start'))).val());
            var endAt = Date.parse(form.find($("." + elementName.replace('start', 'end'))).val());

            if (isNaN(startAt) && isNaN(endAt)) {
                return true;
            }

            return endAt >= startAt;
        }, "End date must be greater than or equal to start date");

        var reportsValidationSettings = {
            rules: {
                'fields[]': {
                    required: true,
                    minlength: 1
                },
                acquired_at_start: {
                    dateISO: true,
                    required: {
                        depends: function(element) {
                            let form = $(element).closest('form');
                            let endInput = form.find('.acquired_at_end:first');
                            return endInput.val().trim() !== "";
                        }
                    },
                },
                acquired_at_end: {
                    required: {
                        depends: function(element) {
                            let form = $(element).closest('form');
                            let startInput = form.find('.acquired_at_start:first');
                            return startInput.val().trim() !== "";
                        }
                    },
                    dateISO: true,
                    greaterThanOrEqual: true,
                },
                created_at_start: {
                    dateISO: true,
                    required: {
                        depends: function(element) {
                            let form = $(element).closest('form');
                            let endInput = form.find('.created_at_end:first');
                            return endInput.val().trim() !== "";
                        }
                    },
                },
                created_at_end: {
                    dateISO: true,
                    required: {
                        depends: function(element) {
                            let form = $(element).closest('form');
                            let startInput = form.find('.created_at_start:first');
                            return startInput.val().trim() !== "";
                        }
                    },
                    greaterThanOrEqual: true,
                },
            },
            messages: {
                'fields[]': {
                    required: "You must select at least 1 field"
                },
                acquired_at_start: {
                    required: "Start date is required when End date is present"
                },
                acquired_at_end: {
                    required: "End date is required when Start date is present"
                },
                created_at_start: {
                    required: "Start date is required when End date is present"
                },
                created_at_end: {
                    required: "End date is required when Start date is present"
                }
            },
            onkeyup: function(element, event) {
                this.element(element);
                toggleSubmitButton($(element).closest('form'), this);
            },
            onfocusout: function(element, event) {
                this.element(element);
                toggleSubmitButton($(element).closest('form'), this)
            },
            onclick: function(element, event) {
                this.element(element);
                toggleSubmitButton($(element).closest('form'), this)

            },
            success: function(label, element) {
                let form = $(element).closest('form');
                let elementName = $(element).attr('name').replace(/\[|\]/g, '');
                form.find('.' + elementName + '_msg:first').html('');
                form.find('.form_group_' + elementName + ':first').removeClass(
                    'has-error has-feedback');
            },
            errorPlacement: function(error, element) {
                let form = $(element).closest('form');
                let elementName = $(element).attr('name').replace(/\[|\]/g, '');
                form.find('.' + elementName + '_msg:first').html(error.text());
                console.log('.form_group_' + elementName + ':first');
                form.find('.form_group_' + elementName + ':first').addClass('has-error has-feedback');
            },
        };

        var patronsListValidator = $("#patrons-list-form").validate(reportsValidationSettings);
        var collectionsListValidator = $("#collections-list-form").validate(reportsValidationSettings);
        var copiesListValidator = $("#copies-list-form").validate(reportsValidationSettings);
        var offSiteCirculationsListValidator = $("#off-site-circulations-list-form").validate(
            reportsValidationSettings);
        var inHouseCirculationsListValidator = $("#in-house-circulations-list-form").validate(
            reportsValidationSettings);

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 　 █▀▀▄ ──█── █──█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀── 　 ▀▀▀─ ──▀── ▀──▀
        function toggleSubmitButton(form, validator) {
            let numberOfInvalids = validator.numberOfInvalids();

            if (numberOfInvalids === 0) {
                form.find('.submit-button:first').attr('disabled', false);
            } else {
                form.find('.submit-button:first').attr('disabled', true);
            }
        }

        function attachChangeHandlers(form, validator) {
            form.find('.created_at_start, .created_at_end').on('change', function() {
                $(this).valid();
                const form = $(this).closest('form');
                form.find('.' + $(this).attr('name').replace('start', 'end')).valid();
                toggleSubmitButton(form, validator);
            });
        }

        attachChangeHandlers($('#patrons-list-form'), patronsListValidator);
        attachChangeHandlers($('#collections-list-form'), collectionsListValidator);
        attachChangeHandlers($('#copies-list-form'), copiesListValidator);
        attachChangeHandlers($('#off-site-circulations-list-form'), offSiteCirculationsListValidator);
        attachChangeHandlers($('#in-house-circulations-list-form'), inHouseCirculationsListValidator);

        $('#copies-list-form').find('.acquired_at_start, .acquired_at_end').on('change', function() {
            $(this).valid();
            const form = $(this).closest('form');
            form.find('.' + $(this).attr('name').replace('start', 'end')).valid();
            toggleSubmitButton(form, copiesListValidator);
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


        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = reportsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#report-delete-all, #report-force-delete-all, #report-restore-all')
                    .removeAttr('disabled');
                $('.report-count').html('(' + selectedRows.length + ')');
            } else {
                $('#report-delete-all, #report-force-delete-all, #report-restore-all').attr(
                    'disabled', true);
                $('.report-count').html('');
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
        function showInputErrors(data, form) {
            for (let key in data.msg) {
                console.log('.' + String(key) + '_msg' + ':first');
                console.log('.form_group_' + String(key) + ':first');
                form.find('.' + String(key) + '_msg' + ':first').html(String(data.msg[key]));
                form.find('.form_group_' + String(key) + ':first').addClass(
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
        function ajaxSuccess(data, form) {
            console.log(data);
            hideLoading();

            if (data.code == '400') {
                showInputErrors(data, form);
                return;
            }

            $('#report-modal').modal('hide');

            if (data.error) {
                swal(data.error, 'error');
            }
            if (data.success) {
                swal(data.success, 'success');
            }

            reportsTable.column(0).checkboxes.deselectAll();
            reportsTable.ajax.reload();
            toggleButtons();
        }

        $("#report-modal").on("hidden.bs.modal", function() {
            removeInputErrors();
            $('#patrons-list-form,#collections-list-form,#copies-list-form,#off-site-circulations-list-form,#in-house-circulations-list-form')
                .trigger('reset');
        });

        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#patrons-list-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();
            let frm = new FormData(this);

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('reports.patrons.list') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data, $('#patrons-list-form'));
                },
                error: function(data) {
                    console.log(data);
                    hideLoading();
                }
            });
        })

        $('#collections-list-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();
            let frm = new FormData(this);

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('reports.collections.list') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data, $(this));
                },
                error: function(data) {
                    console.log(data);
                    hideLoading();
                }
            });
        })

        $('#copies-list-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();
            let frm = new FormData(this);

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('reports.copies.list') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data, $(this));
                },
                error: function(data) {
                    console.log(data);
                    hideLoading();
                }
            });
        })

        $('#off-site-circulations-list-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();
            let frm = new FormData(this);

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('reports.off.site.circulations.list') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data, $(this));
                },
                error: function(data) {
                    console.log(data);
                    hideLoading();
                }
            });
        })

        $('#in-house-circulations-list-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();
            let frm = new FormData(this);

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('reports.in.house.circulations.list') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    ajaxSuccess(data, $(this));
                },
                error: function(data) {
                    console.log(data);
                    hideLoading();
                }
            });
        })

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = reportsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }


        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-report-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('reports.destroy') }}';

            swalConfirmation(
                'Delete this report and all of its related information?',
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
        $('#report-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('reports.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' report(s) and all of its related information?',
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
        $('body').on('click', '.btn-report-restore', function() {
            let id = $(this).data('id');
            let url = '{{ route('reports.restore') }}';

            swalConfirmation(
                'Restore this report and all of its related information?',
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
        $('#report-restore-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('reports.restore') }}';

            swalConfirmation(
                'Restore ' + id.length + ' report(s) and all of its related information?',
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
        $('body').on('click', '.btn-report-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('reports.force.delete') }}';

            swalConfirmation(
                'Permanently delete this report and all of its related information?',
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
        $('#report-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('reports.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' report(s) and all of its related information?',
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


        $('body').on('click', '.btn-report-download', function() {
            let id = $(this).data('id');
            let url = '{{ route('reports.download.link', ['id' => ':id']) }}'.replace(':id', id);

            $.ajax({
                type: 'get', // method shown on route:list
                url: url,
                success: function(data) {
                    // window.location = data;
                    window.open(data, '_blank');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

    });
</script>
