<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var collectionsTable = $('#table-collections').DataTable({
            'columns': [
                @if (auth()->check() && auth()->user()->temp_role == 'librarian')
                    {
                        data: 'checkbox'
                    },                
                @endif
                {
                    data: 'image',
                },
                {
                    data: 'title',
                    class: "text-nowrap"
                },
                {
                    data: 'authors',
                },
                {
                    data: 'availability',
                },
                {
                    data: 'format',
                },
                {
                    data: 'action',
                }
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
                    orderable: false
                },
                {
                    targets: [2, 3, 4],
                    searchable: true
                },
                {
                    width: '200px',
                    targets: [2, 3]
                },
                @if (auth()->check() && auth()->user()->temp_role == 'librarian')
                    {
                        'targets': 0,
                        'checkboxes': {
                            'selectRow': true
                        },
                        className: 'select-checkbox'
                    }                
                @endif
            ],
            searching: true,
            // scrollX: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: {
                url: @if (Route::currentRouteName() === 'collections.archive')
                    "{{ route('collections.archive') }}"
                @else
                    "{{ route('collections.index') }}"
                @endif
            }
        });

        // ── ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ █▀▀ ── 
        // ▀▀ ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ ▀▀█ ▀▀ 
        // ── ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀ ──        
        // █▀▀█ █▀▀ █▀▄▀█ █▀▀█ ▀█─█▀ █▀▀ 　 █───█ █──█ ─▀─ ▀▀█▀▀ █▀▀ █▀▀ █▀▀█ █▀▀█ █▀▀ █▀▀ █▀▀ 
        // █▄▄▀ █▀▀ █─▀─█ █──█ ─█▄█─ █▀▀ 　 █▄█▄█ █▀▀█ ▀█▀ ──█── █▀▀ ▀▀█ █──█ █▄▄█ █── █▀▀ ▀▀█ 
        // ▀─▀▀ ▀▀▀ ▀───▀ ▀▀▀▀ ──▀── ▀▀▀ 　 ─▀─▀─ ▀──▀ ▀▀▀ ──▀── ▀▀▀ ▀▀▀ █▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀
        function removeExcessWhitespaces(id) {
            $(id).keyup(function() {
                var sanitizedValue = $(this).val().replace(/\s+/g, ' ');
                $(this).val(sanitizedValue);
            });
        }

        removeExcessWhitespaces('#title');
        removeExcessWhitespaces('#series_title');
        removeExcessWhitespaces('#publication_place');
        removeExcessWhitespaces('#publisher');

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
            let numberOfInvalids = collectionValidator.numberOfInvalids();
            
            if (numberOfInvalids == 0 && $('#title').val()) {
                $("#collection-modal-button").attr("disabled", false);
            } else {
                $("#collection-modal-button").attr("disabled", true);
            }
        }

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
        $('#collection-add').click(function() {
            $("#collection-modal-button").attr("disabled", true);
            $('#collection-modal-header').html('Add Collection');
            $('#collection-modal-button').html('Add Collection');
            $('#collection-form-action').val('add');
            $('#collection-modal').modal('show');
            imageFilePond.removeFiles();
        });

        $('body').on('click', '.btn-collection-reserve', function() {
            removeInputErrors();

            let id = $(this).data('id');
            $('#quantity').val(1);

            $('#reservation-hidden-id').val(id);
            $('#reservation-modal').modal('show');
        });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = collectionsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#collection-delete-all, #collection-force-delete-all, #collection-restore-all')
                    .removeAttr('disabled');
                $('.collection-count').html('(' + selectedRows.length + ')');
            } else {
                $('#collection-delete-all, #collection-force-delete-all, #collection-restore-all')
                    .attr('disabled', true);
                $('.collection-count').html('');
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


        // █▀▀█ ──▀ █▀▀█ █─█ 　 █▀▀ █──█ █▀▀ █▀▀ █▀▀ █▀▀ █▀▀ 
        // █▄▄█ ──█ █▄▄█ ▄▀▄ 　 ▀▀█ █──█ █── █── █▀▀ ▀▀█ ▀▀█ 
        // ▀──▀ █▄█ ▀──▀ ▀─▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀
        function ajaxSuccess(data) {
            console.log(data);

            if (data.code == '400') {
                showInputErrors(data);
                if (data.msg['call_prefix'] || data.msg['call_main'] || data.msg['call_cutter'] || data.msg[
                        'call_suffix']) {
                    $('#form_group_call_number').addClass('has-error has-feedback');
                }
                return;
            }

            if (data.error) {
                swal(data.error, 'error');
            }
            if (data.success) {
                swal(data.success, 'success');
            }

            collectionsTable.column(0).checkboxes.deselectAll();
            toggleButtons();
            collectionsTable.ajax.reload();
            let action = $('#collection-form-action').val();
            $('#collection-form').trigger('reset');
            $('#collection-form-action').val(action);
            imageFilePond.removeFiles();
        }

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

            setFormDataTagifyInput('authors', tagifyAuthors.value);
            setFormDataTagifyInput('subtitles', tagifySubtitles.value);
            setFormDataTagifyInput('subjects', tagifySubjects.value);

            if ($('#collection-form-action').val() == 'add') {
                // ─█▀▀█ ░█▀▀▄ ░█▀▀▄ 
                // ░█▄▄█ ░█─░█ ░█─░█ 
                // ░█─░█ ░█▄▄▀ ░█▄▄▀
                $.ajax({
                    data: frm,
                    type: 'POST',
                    url: '{{ route('collections.store') }}',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        ajaxSuccess(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

            } else if ($('#collection-form-action').val() == 'edit') {
                // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
                // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
                // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
                let id = $('#collection-hidden-id').val();
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
                        ajaxSuccess(data);
                        $('#collection-modal').modal('hide');
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        })


        // █▀▀ █▀▀ █▀▀▄ █▀▀▄ 　 █▀▀█ █▀▀ █▀▀ █▀▀ █▀▀█ ▀█─█▀ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ▀▀█ █▀▀ █──█ █──█ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ █▄▄▀ ─█▄█─ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ▀▀▀ ▀▀▀ ▀──▀ ▀▀▀─ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀─▀▀ ──▀── ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $('#reservation-form').submit(function(e) {
            e.preventDefault();

            removeInputErrors();

            let id = $('#reservation-hidden-id').val();
            let quantity = $('#quantity').val();
            let url = "{{ route('reservations.store') }}";

            manageCollection({
                    directRequest: true,
                    id: id,
                    type: 'App\\Models\\collection',
                    quantity: quantity
                },
                url,
                'POST',
                null,
                null,
                null,
                false
            );
        })


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀█ █▀▀ █▀▀ █▀▀ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ──▀──
        $("#collection-modal").on("hidden.bs.modal", function() {
            $('#collection-form').trigger('reset');
            removeInputErrors();
        });

        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ ▀█▀ ──█── 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀──
        $('body').on('click', '.btn-collection-edit', function() {
            $("#collection-modal-button").attr("disabled", false);
            removeInputErrors();
            imageFilePond.removeFiles();

            let id = $(this).data('id');

            $('#collection-form-action').val('edit');
            $('#collection-modal-header').html('Update Collection');
            $('#collection-modal-button').html('Update Collection');
            $('#collection-modal').modal('show');
            $('#collection-form-action').val('edit');
            $('#collection-hidden-id').val(id);

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('collections.index') }}" + "/" + id + "/edit",
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

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 ─▀─ █▀▀▄ 　 █▀▀█ █▀▀█ █▀▀█ █▀▀█ █──█ 
        // █─▀█ █▀▀ ──█── 　 ▀█▀ █──█ 　 █▄▄█ █▄▄▀ █▄▄▀ █▄▄█ █▄▄█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀ ▀▀▀─ 　 ▀──▀ ▀─▀▀ ▀─▀▀ ▀──▀ ▄▄▄█
        function getIdArray() {
            let selectedRows = collectionsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }

        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-collection-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('collections.destroy') }}';

            swalConfirmation(
                'Delete this collection and all of its related information?',
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
        $('#collection-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('collections.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' collection(s) and all of its related information?',
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
        $('body').on('click', '.btn-collection-restore', function() {
            let id = $(this).data('id');
            let url = '{{ route('collections.restore') }}';

            swalConfirmation(
                'Restore this collection and all of its related information?',
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
        $('#collection-restore-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('collections.restore') }}';

            swalConfirmation(
                'Restore ' + id.length + ' collection(s) and all of its related information?',
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
        $('body').on('click', '.btn-collection-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('collections.force.delete') }}';

            swalConfirmation(
                'Permanently delete this collection and all of its related information?',
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
        $('#collection-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('collections.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' collection(s) and all of its related information?',
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

        $('#collection-google-books-api').click(function() {
            window.location.href = '{{ route('google.books.api.index') }}';
        });
    });
</script>
