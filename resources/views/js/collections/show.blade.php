<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var copiesTable = $('#table-copies').DataTable({
            'columns': [
                @if (auth()->check() && auth()->user()->temp_role == 'librarian')
                    {
                        data: 'checkbox',
                    },
                @endif {
                    data: 'barcode',
                },
                {
                    data: 'call_prefix',
                },
                {
                    data: 'fund',
                },
                {
                    data: 'vendor',
                },
                {
                    data: 'price',
                },
                {
                    data: 'date_acquired',
                    class: "text-nowrap",
                },
                {
                    data: 'librarian',
                    class: "text-nowrap",
                },
                {
                    data: 'availability',
                },
                @if (auth()->check())
                    {
                        data: 'action',
                    }
                @endif
            ],
            'initComplete': function(settings) {
                var api = this.api();

                api.cells(
                    api.rows(function(idx, data, node) {
                        return data.disabled;
                    }).indexes(),
                    0
                ).checkboxes.disable();
            },
            'columnDefs': [{
                    targets: [0, 1, 4, 5],
                    orderable: false,
                },
                {
                    targets: [2, 3, 4],
                    searchable: true,
                },
                @if (auth()->check() && auth()->user()->temp_role == 'librarian')
                    {
                        'targets': 0,
                        'checkboxes': {
                            'selectRow': true,
                        },
                        className: 'select-checkbox',
                    }
                @endif
            ],
            searching: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: {
                url: @if ($collection->deleted_at)
                    "{{ route('copies.archive', $collection->id) }}"
                @else
                    "{{ route('copies.index', $collection->id) }}"
                @endif
            },
        });

        // ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $.validator.addMethod("validYear", function(value, element) {
            if (/^\d+$/.test(value)) {
                var currentYear = new Date().getFullYear();
                var inputYear = parseInt(value, 10);
                return inputYear <= currentYear;
            }
            return false;
        }, "Please enter a valid year not greater than the current year");

        var collectionValidator = $("#collection-form").validate({
            focusInvalid: false,
            rules: {
                title: {
                    required: true,
                },
                copyright_year: {
                    required: false,
                    validYear: true
                },
                // call_main: {
                //     required: true,
                // },
                // call_cutter: {
                //     required: true,
                // }
            },
            onkeyup: function(element, event) {
                this.element(element);
                toggleCollectionSubmitBtn();
            },
            onfocusout: function(element, event) {
                this.element(element);
                toggleCollectionSubmitBtn();
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
        function toggleCollectionSubmitBtn() {
            let numberOfInvalids = collectionValidator.numberOfInvalids();

            if (numberOfInvalids == 0 && $('#title').val()) {
                $("#collection-modal-button").attr("disabled", false);
            } else {
                $("#collection-modal-button").attr("disabled", true);
            }
        }

        // ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $.validator.addMethod("notAfterToday", function(value, element) {
            var selectedDate = new Date(value);

            var today = new Date();
            today.setHours(0, 0, 0, 0);
            return selectedDate <= today;
        }, "Date cannot be after today");

        $.validator.addMethod("priceRequiredIfPurchased", function(value, element) {
            var fundValue = $("#fund").val();

            if (fundValue === "purchased") {
                return parseFloat(value) > 0;
            } else {
                return true;
            }
        }, "Price is required and must be greater than 0 if Fund is 'purchased'");

        var copyValidator = $("#copy-form").validate({
            rules: {
                barcode: {
                    required: true
                },
                acquired_at: {
                    required: true,
                    dateISO: true,
                    notAfterToday: "Date cannot be after today"
                },
                price: {
                    priceRequiredIfPurchased: true
                }
            },
            messages: {
                barcode: {
                    remote: "This barcode is already taken."
                }
            },
            onkeyup: function(element, event) {
                this.element(element);
            },
            onfocusout: function(element, event) {
                this.element(element);
            },
            onclick: function(element, event) {
                if (element.name === 'fund') {
                    this.element($('#price'));
                    toggleSubmitBtn();
                }
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
            let numberOfInvalids = copyValidator.numberOfInvalids();

            if (numberOfInvalids == 0 && $('#barcode').val()) {
                $("#copy-modal-button").attr("disabled", false);
            } else {
                $("#copy-modal-button").attr("disabled", true);
            }
        }

        $('#copy-form').on('blur focus keyup', () => {
            toggleSubmitBtn();
        })

        // ▀▀█▀▀ █▀▀█ █▀▀▀ ─▀─ █▀▀ █──█ 
        // ──█── █▄▄█ █─▀█ ▀█▀ █▀▀ █▄▄█ 
        // ──▀── ▀──▀ ▀▀▀▀ ▀▀▀ ▀── ▄▄▄█
        const tagifySettings = {
            delimiters: [';'],
            maxTags: 5
        }
        const tagifyAuthors = new Tagify($('#authors')[0], tagifySettings);
        const tagifySubjects = new Tagify($('#subjects')[0], tagifySettings);
        const tagifySubtitles = new Tagify($('#subtitles')[0], tagifySettings);

        // █▀▀ ─▀─ █── █▀▀ █▀▀█ █▀▀█ █▀▀▄ █▀▀▄ 
        // █▀▀ ▀█▀ █── █▀▀ █──█ █──█ █──█ █──█ 
        // ▀── ▀▀▀ ▀▀▀ ▀▀▀ █▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀─
        $.fn.filepond.registerPlugin(FilePondPluginImagePreview);
        $.fn.filepond.registerPlugin(
            FilePondPluginFileValidateType);

        $('#image').filepond();

        var imageInput = document.querySelector('#image');
        var imageFilePond = FilePond.create(imageInput, {
            imagePreviewHeight: 175,
            instantUpload: false,
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/webp'],
        });

        // █▀▀ █── █▀▀ █▀▀█ ▀█─█▀ █▀▀ 
        // █── █── █▀▀ █▄▄█ ─█▄█─ █▀▀ 
        // ▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ ──▀── ▀▀▀
        var priceCleave = new Cleave('#price', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalScale: 2,
            numeralDecimalMark: '.'
        });

        $('#price').change(function() {
            if ($(this).val() === '') {
                $(this).val('0.00');
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

        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀█ █▀▀ █▀▀ █▀▀ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ──▀──
        $("#collection-modal,#copy-modal").on("hidden.bs.modal", function() {
            $('#collection-form,#copy-modal').trigger('reset');
            removeInputErrors();
        });

        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('#copy-add').click(function() {
            $("#copy-modal-button").attr("disabled", true);
            $('#copy-form').trigger('reset');
            $('#copy-modal-header').html('Add Copy');
            $('#copy-modal-button').html('Add Copy');
            $('#copy-form-action').val('add');
            $('#copy-modal').modal('show');
            $('#image-container').html('');
        });


        // █▀▀█ █▀▀ █▀▀ █▀▀ █▀▀█ ▀█─█▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █──█ 
        // █▄▄▀ █▀▀ ▀▀█ █▀▀ █▄▄▀ ─█▄█─ █▀▀ 　 █── █──█ █──█ █▄▄█ 
        // ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀─▀▀ ──▀── ▀▀▀ 　 ▀▀▀ ▀▀▀▀ █▀▀▀ ▄▄▄█
        $('body').on('click', '.btn-copy-reserve', function() {
            removeInputErrors();

            let id = $(this).data('id');
            let url = '{{ route('reservations.store') }}';

            swalConfirmation(
                'Reserve this copy?',
                'Yes, reserve it!',
                function() {
                    $.ajax({
                        data: {
                            copyId: id
                        },
                        type: 'POST',
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

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = copiesTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#copy-delete-all, #copy-force-delete-all, #copy-restore-all')
                    .removeAttr('disabled');
                $('.copy-count').html('(' + selectedRows.length + ')');
            } else {
                $('#copy-delete-all, #copy-force-delete-all, #copy-restore-all')
                    .attr('disabled', true);
                $('.copy-count').html('');
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

            copiesTable.column(0).checkboxes.deselectAll();
            toggleButtons();
            copiesTable.ajax.reload();
            let action = $('#copy-form-action').val();
            $('#copy-form').trigger('reset');
            $('#copy-form-action').val(action);
        }

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀ ─▀─ █▀▀▄ █▀▀█ █──█ ▀▀█▀▀ 　 █▀▀ ▀▀█▀▀ █▀▀█ 
        // █─▀█ █▀▀ ──█── 　 ──█── █▄▄█ █─▀█ ▀▀█ ▀█▀ █──█ █──█ █──█ ──█── 　 ▀▀█ ──█── █▄▄▀ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ──▀── ▀──▀ ▀▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ █▀▀▀ ─▀▀▀ ──▀── 　 ▀▀▀ ──▀── ▀─▀▀
        function getTagsInputString(name) {
            let arrayInput = $("#" + name).tagsinput('items');

            if (arrayInput.length > 0) {
                let items = arrayInput.join(';');
                return items;
            }

            return '';
        }


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#copy-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();

            let frm = new FormData(this);

            if ($('#copy-form-action').val() == 'add') {
                // ─█▀▀█ ░█▀▀▄ ░█▀▀▄ 
                // ░█▄▄█ ░█─░█ ░█─░█ 
                // ░█─░█ ░█▄▄▀ ░█▄▄▀
                $.ajax({
                    data: frm,
                    type: 'POST',
                    url: '{{ route('copies.store', ['id' => $collection->id]) }}',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        ajaxSuccess(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

            } else if ($('#copy-form-action').val() == 'edit') {
                // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
                // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
                // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
                let id = $('#copy-hidden-id').val();
                let url = '{{ route('copies.update', ['id' => ':id']) }}'.replace(':id', id);

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
                        $('#copy-modal').modal('hide');
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
        $('body').on('click', '.btn-copy-edit', function() {
            removeInputErrors();
            imageFilePond.removeFiles();

            let id = $(this).data('id');

            $('#copy-form-action').val('edit');
            $('#copy-modal-header').html('Update Copy');
            $('#copy-form').trigger('reset');
            $('#copy-modal-button').html('Update Copy');
            $('#copy-modal').modal('show');
            $('#copy-form-action').val('edit');
            $('#copy-hidden-id').val(id);

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('copies.edit', ['id' => ':id']) }}".replace(':id', id),
                success: function(data) {
                    $('#barcode').val(data.barcode);
                    $('#fund').val(data.fund);
                    $('#vendor').val(data.vendor);
                    $('#price').val(data.price);
                    $('#date_acquired').val(data.date_acquired);
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#copy-modal').modal('show');
            });
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = copiesTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }

        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-copy-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('copies.destroy') }}';

            swalConfirmation(
                'Delete this copy and all of its related information?',
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
        $('#copy-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('copies.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' copies and all of its related information?',
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



        // █▀▀█ █▀▀▄ █▀▀▄ 　 ▀▀█▀▀ █▀▀█ 　 █▀▀ █──█ █▀▀ █── █▀▀ 
        // █▄▄█ █──█ █──█ 　 ──█── █──█ 　 ▀▀█ █▀▀█ █▀▀ █── █▀▀ 
        // ▀──▀ ▀▀▀─ ▀▀▀─ 　 ──▀── ▀▀▀▀ 　 ▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀──   
        $('body').on('click', '.btn-copy-shelf', function() {
            let id = $(this).data('id');

            manageCopy({
                    id: id,
                },
                "{{ route('shelf.items.store') }}",
                'POST',
                null,
                null,
                null,
                false
            );
        });

        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#collection-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();

            let frm = new FormData(this);

            const file = imageFilePond.getFile();

            if (file) {
                const fileObject = new File([file.file], file.file.name);
                frm.append('image', fileObject);
                console.log(fileObject);
            }

            function setFormDataTagifyInput(name, tagifyInput) {
                if (tagifyInput.length != 0) {
                    const arr = [];

                    for (let i = 0; i < tagifyInput.length; i++) {
                        const firstElement = tagifyInput[i]['value'];
                        arr.push(firstElement);
                    }

                    frm.set(name, JSON.stringify(arr));
                }
            }

            setFormDataTagifyInput('subtitles', tagifySubtitles.value);
            setFormDataTagifyInput('authors', tagifyAuthors.value);
            setFormDataTagifyInput('subjects', tagifySubjects.value);

            // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
            // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
            // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
            let id = '{{ $collection->id }}';
            let url = '{{ route('collections.update', ['id' => ':id']) }}'.replace(':id', id);

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
                    if (data.error) {
                        swal(data.error, 'error');
                    }
                    if (data.success) {
                        swal(data.success, 'success');
                    }

                    $('#collection-modal').modal('hide');
                    Livewire.emit('updateCollection');

                    setTimeout(function() {
                        copiesTable.ajax.reload();
                    }, 1000);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        })

        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 　 █▀▀ █▀▀█ █── █── █▀▀ █▀▀ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // █▀▀ █──█ ▀█▀ ──█── 　 █── █──█ █── █── █▀▀ █── ──█── ▀█▀ █──█ █──█ 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $('#btn-collection-edit').click(function() {
            removeInputErrors();
            imageFilePond.removeFiles();
            $('#collection-form').trigger('reset');

            var collectionId = '{{ $collection->id }}';

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('collections.index') }}" + "/" + collectionId + "/edit",
                success: function(data) {
                    $('#barcode').val(data.collection.barcode);
                    $('#format').val(data.collection.format);
                    $('#title').val(data.collection.title);
                    $('#edition').val(data.collection.edition);
                    $('#series_title').val(data.collection.series_title);
                    $('#isbn').val(data.collection.isbn);
                    $('#publication_place').val(data.collection.publication_place);
                    $('#publisher').val(data.collection.publisher);
                    $('#copyright_year').val(data.collection.copyright_year);
                    $('#physical_description').val(data.collection.physical_description);
                    $('#note').val(data.collection.note);
                    $('#copy').val(data.collection.copy);
                    $('#call_prefix').val(data.collection.call_prefix);
                    $('#call_main').val(data.collection.call_main);
                    $('#call_cutter').val(data.collection.call_cutter);
                    $('#call_suffix').val(data.collection.call_suffix);

                    tagifyAuthors.addTags(data.authors);
                    tagifySubjects.addTags(data.subjects)
                    tagifySubtitles.addTags(data.subtitles)

                    if (data.image) {
                        imageFilePond.addFile(data.image);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#collection-modal').modal('show');
            });
        });

        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#btn-collection-delete').click(function() {
            let url = '{{ route('collections.destroy') }}';

            swalConfirmation(
                'Delete this collection and all of its related information?',
                'Yes, delete it!',
                function() {
                    $.ajax({
                        data: {
                            id: '{{ $collection->id }}'
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
                                    '{{ route('collections.index') }}';
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
        $('#btn-collection-restore').click(function() {
            let url = '{{ route('collections.restore') }}';

            swalConfirmation(
                'Restore this collection and all of its related information?',
                'Yes, restore it!',
                function() {
                    $.ajax({
                        data: {
                            id: '{{ $collection->id }}'
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
                                    '{{ route('collections.archive') }}';
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
        $('#btn-collection-force-delete').click(function() {
            let url = '{{ route('collections.force.delete') }}';

            swalConfirmation(
                'Delete collection permanently and all of its related information?',
                'Yes, Delete it!',
                function() {
                    $.ajax({
                        data: {
                            id: '{{ $collection->id }}'
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
                                    '{{ route('collections.archive') }}';
                            }, 2000); // 2000 milliseconds = 2 seconds
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
