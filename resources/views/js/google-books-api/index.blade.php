<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var collectionsTable = $('#table-books').DataTable({
            'columns': [{
                    data: 'isbn',
                },
                {
                    data: 'title',
                },
                {
                    data: 'author',
                },
                {
                    data: 'action',
                }
            ],
            'columnDefs': [{
                    targets: [0, 1],
                    orderable: false
                },
                {
                    targets: [2],
                    searchable: true
                },
                {
                    width: '150px',
                    targets: [3]
                },
            ],
            searching: false,
            scrollX: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            // serverSide: true,
            processing: true
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

        // █▀▀█ █──█ █── █▀▀ █▀▀ 
        // █▄▄▀ █──█ █── █▀▀ ▀▀█ 
        // ▀─▀▀ ─▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀
        const validationRules = {
            title: {
                presence: {
                    allowEmpty: false,
                    message: "is required."
                }
            },
            isbn: {
                format: {
                    pattern: /^(?:\d{10}|\d{13})$/,
                    message: "^ISBN must be either 10 or 13 digits."
                }
            },
            copyright_year: {
                numericality: {
                    onlyInteger: true,
                    greaterThanOrEqualTo: 1900,
                    lessThanOrEqualTo: new Date().getFullYear(),
                    message: "must be a valid year and not beyond the current year."
                },
            },
            call_main: {
                presence: {
                    allowEmpty: false,
                    message: "is required."
                }
            },
            call_cutter: {
                presence: {
                    allowEmpty: false,
                    message: "is required."
                }
            }
        };

        function keyUpValidate(idName) {
            $('#' + idName).keyup(function(e) {
                $('#' + idName + '_msg').html('');

                if (idName == 'call_main' | idName == 'call_cutter') {
                    $('#form_group_call_number').removeClass('has-error has-feedback');
                } else {
                    $('#form_group_' + idName).removeClass('has-error has-feedback');
                }

                let data = {
                    [idName]: $('#' + idName).val(),
                };

                console.log(data);

                const validationResult = validate(data, validationRules);

                if (typeof validationResult === 'undefined') {
                    return;
                }

                console.log(validationResult);
                if (validationResult[idName]) {
                    $('#' + idName + '_msg').html(validationResult[idName][0]);

                    if (idName == 'call_main' | idName == 'call_cutter') {
                        $('#form_group_call_number').addClass('has-error has-feedback');
                        return;
                    }

                    $('#form_group_' + idName).addClass('has-error has-feedback');
                }
            });
        }

        keyUpValidate('title');
        keyUpValidate('isbn');
        keyUpValidate('copyright_year');
        keyUpValidate('call_main');
        keyUpValidate('call_cutter');


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
        function swal(title, icon) {
            Swal.fire({
                position: 'center',
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 1200
            })
        }

        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('#collection-add').click(function() {
            $('#collection-modal-header').html('Add Collection');
            $('#collection-modal-button').html('Add');
            $('#collection-form-action').val('add');
            $('#collection-modal').modal('show');
            $('#image-container').html('');
        });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            var selectedRows = collectionsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#collection-delete-all, #collection-force-delete-all, #collection-restore-all').removeAttr(
                    'disabled');
                $('.collection-count').html('(' + selectedRows.length + ')');
            } else {
                $('#collection-delete-all, #collection-force-delete-all, #collection-restore-all').attr(
                    'disabled', true);
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

        // █▀▀ █▀▀ █▀▀█ █▀▀█ █▀▀ █──█ 　 █▀▀ █▀▀█ █▀▀█ 　 █▀▀▄ █▀▀█ █▀▀█ █─█ █▀▀ 
        // ▀▀█ █▀▀ █▄▄█ █▄▄▀ █── █▀▀█ 　 █▀▀ █──█ █▄▄▀ 　 █▀▀▄ █──█ █──█ █▀▄ ▀▀█ 
        // ▀▀▀ ▀▀▀ ▀──▀ ▀─▀▀ ▀▀▀ ▀──▀ 　 ▀── ▀▀▀▀ ▀─▀▀ 　 ▀▀▀─ ▀▀▀▀ ▀▀▀▀ ▀─▀ ▀▀▀
        $('#btn-search').click(function() {
            searchBooks();
        });

        $('#search_keyword').on('keydown', function(e) {
            if (e.key === 'Enter') {
                searchBooks();
            }
        });

        function searchBooks() {
            let keyword = $('#search_keyword').val();
            let searchBy = $('#search_by').val();

            if (!keyword) {
                return;
            }

            let url = "{{ route('google.books.api.search', [':keyword', ':searchBy']) }}".replace(
                ':keyword', keyword).replace(':searchBy', searchBy);

            collectionsTable.ajax.url(url).load();
        }

        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀█ █▀▀ █▀▀ █▀▀ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ──▀──
        $("#collection-modal").on("hidden.bs.modal", function() {
            $('#collection-form').trigger('reset');
        });

        // █▀▀▀ █▀▀ ▀▀█▀▀ 　 █▀▀▄ █▀▀█ █▀▀█ █─█ 　 ─▀─ █▀▀▄ █▀▀ █▀▀█ 
        // █─▀█ █▀▀ ──█── 　 █▀▀▄ █──█ █──█ █▀▄ 　 ▀█▀ █──█ █▀▀ █──█ 
        // ▀▀▀▀ ▀▀▀ ──▀── 　 ▀▀▀─ ▀▀▀▀ ▀▀▀▀ ▀─▀ 　 ▀▀▀ ▀──▀ ▀── ▀▀▀▀
        $('body').on('click', '.btn-collection-add', function() {
            let id = $(this).data('id');

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('google.books.api.index') }}" + "/" + id,
                success: function(data) {
                    console.log(data);
                    $('#title').val(data.title);
                    $('#isbn').val(data.isbn);
                    $('#physical_description').val(data.physical_description);
                    $('#publisher').val(data.publisher);

                    tagifyAuthors.addTags(data.authors);
                    tagifySubjects.addTags(data.subjects);
                    tagifySubtitles.addTags(data.subtitles);
                },
                error: function(data) {
                    console.log(data);
                }
            }).then(function() {
                $('#collection-modal').modal('show');
            });
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

            setFormDataTagifyInput('authors', tagifyAuthors.value);
            setFormDataTagifyInput('subtitles', tagifySubtitles.value);
            setFormDataTagifyInput('subjects', tagifySubjects.value);

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('collections.store') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#collection-modal').modal('hide');
                    
                    if (data.code == '400') {
                        showInputErrors(data);
                        if (data.msg['call_prefix'] || data.msg['call_main'] || data.msg[
                                'call_cutter'] || data.msg[
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
                    imageFilePond.removeFiles();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    });
</script>
