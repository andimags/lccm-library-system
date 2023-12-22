<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var patronsTable = $('#table-patrons').DataTable({
            columns: [{
                    data: 'checkbox',
                    className: 'disable-checkbox'
                },
                {
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'image',
                },
                {
                    data: 'id2',
                },
                {
                    data: 'name',
                    class: "text-nowrap"
                },
                {
                    data: 'roles',
                },
                {
                    data: 'groups',
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
            columnDefs: [{
                targets: 0,
                checkboxes: {
                    'selectRow': true
                },
                className: 'select-checkbox'
            }],
            searching: true,
            scrollCollapse: true,
            fixedColumns: true,
            select: true,
            idSrc: 'id',
            serverSide: true,
            processing: true,
            ajax: "{{ \Illuminate\Support\Facades\Route::currentRouteName() == 'patrons.index' ? route('patrons.index') : route('patrons.archive') }}",
        });


        // ▀▀█▀▀ █▀▀█ █▀▀▀ ─▀─ █▀▀ █──█ 
        // ──█── █▄▄█ █─▀█ ▀█▀ █▀▀ █▄▄█ 
        // ──▀── ▀──▀ ▀▀▀▀ ▀▀▀ ▀── ▄▄▄█
        const tagifyGroups = new Tagify($('#groups')[0], {
            dropdown: {
                classname: "color-blue",
                enabled: 0, // disable suggestions
                maxItems: 5,
                position: "input", // place the dropdown near the typed text
                closeOnSelect: false, // keep the dropdown open after selecting a suggestion
                highlightFirst: true
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(';'),
            delimiters: [';'],
        });

        const tagifyRoles = new Tagify($('#roles')[0], {
            whitelist: ['Student', 'Employee', 'Faculty', 'Librarian'],
            maxTags: 2,
            dropdown: {
                classname: "color-blue",
                position: "input", // place the dropdown near the typed text
                closeOnSelect: false, // keep the dropdown open after selecting a suggestion
                highlightFirst: true,
                enabled: 0, // <- show suggestions on focus
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(';'),
            delimiters: [';'],
            enforceWhitelist: true
        });


        // █▀▀▀ █▀▀█ █▀▀█ █──█ █▀▀█ 　 █▀▀ █──█ █▀▀▀ █▀▀▀ █▀▀ █▀▀ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ █▀▀ 
        // █─▀█ █▄▄▀ █──█ █──█ █──█ 　 ▀▀█ █──█ █─▀█ █─▀█ █▀▀ ▀▀█ ──█── ▀█▀ █──█ █──█ ▀▀█ 
        // ▀▀▀▀ ▀─▀▀ ▀▀▀▀ ─▀▀▀ █▀▀▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀
        $.ajax({
            type: 'GET',
            url: "{{ route('settings.get.holding.options') }}",
            data: {
                fields: ['group']
            },
            success: function(data) {
                tagifyGroups.whitelist = data.group;
            },
            error: function(data) {
                console.log(data);
            }
        })

        // ── ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ █▀▀ ── 
        // ▀▀ ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ ▀▀█ ▀▀ 
        // ── ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀ ──        
        // █▀▀█ █▀▀ █▀▄▀█ █▀▀█ ▀█─█▀ █▀▀ 　 █───█ █──█ ─▀─ ▀▀█▀▀ █▀▀ █▀▀ █▀▀█ █▀▀█ █▀▀ █▀▀ █▀▀ 
        // █▄▄▀ █▀▀ █─▀─█ █──█ ─█▄█─ █▀▀ 　 █▄█▄█ █▀▀█ ▀█▀ ──█── █▀▀ ▀▀█ █──█ █▄▄█ █── █▀▀ ▀▀█ 
        // ▀─▀▀ ▀▀▀ ▀───▀ ▀▀▀▀ ──▀── ▀▀▀ 　 ─▀─▀─ ▀──▀ ▀▀▀ ──▀── ▀▀▀ ▀▀▀ █▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀
        $('#first_name').keyup(function() {
            var sanitizedValue = $(this).val().replace(/\s+/g, ' ');
            $(this).val(sanitizedValue);
        });

        // ▀█─█▀ █▀▀█ █── ─▀─ █▀▀▄ █▀▀█ ▀▀█▀▀ ─▀─ █▀▀█ █▀▀▄ 
        // ─█▄█─ █▄▄█ █── ▀█▀ █──█ █▄▄█ ──█── ▀█▀ █──█ █──█ 
        // ──▀── ▀──▀ ▀▀▀ ▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀ ▀▀▀▀ ▀──▀
        $.validator.addMethod("alphabetic", function(value, element) {
                return this.optional(element) || /^(?!.*--)[A-Za-z][A-Za-z\s'-]*[A-Za-z\s]$/.test(value);
            },
            "This field can only contain letters, spaces, and dashes (dash should be followed by a letter)");

        var patronValidator = $("#patron-form").validate({
            rules: {
                id2: {
                    required: true,
                    digits: true,
                    maxlength: 11,
                    minlength: 2,
                    // remote: {
                    //     url: '{{ route('patrons.check.uniqueness') }}',
                    //     type: 'GET',
                    //     data: {
                    //         field: 'id2',
                    //         value: function() {
                    //             return $('#id2').val();
                    //         },
                    //         action: function() {
                    //             return $('#patron-form-action').val();
                    //         },
                    //         patron_id: function() {
                    //             return $('#patron-hidden-id').val();
                    //         },
                    //     },
                    //     dataFilter: function(data) {
                    //         var data = JSON.parse(data);

                    //         if (data == false) {
                    //             return 'false';
                    //         } else {
                    //             return 'true';
                    //         }
                    //     }
                    // },
                },
                first_name: {
                    required: true,
                    minlength: 2,
                    alphabetic: true
                },
                last_name: {
                    required: true,
                    minlength: 2,
                    alphabetic: true
                },
                email: {
                    required: true,
                    email: true,
                    // remote: {
                    //     url: '{{ route('patrons.check.uniqueness') }}',
                    //     type: 'GET',
                    //     data: {
                    //         field: 'email',
                    //         value: function() {
                    //             return $('#email').val();
                    //         },
                    //         action: function() {
                    //             return $('#patron-form-action').val();
                    //         },
                    //         patron_id: function() {
                    //             return $('#patron-hidden-id').val();
                    //         },
                    //     },
                    //     dataFilter: function(data) {
                    //         var data = JSON.parse(data);
                    //         console.log(data);

                    //         if (data == false) {
                    //             return 'false';
                    //         } else {
                    //             return 'true';
                    //         }
                    //     }
                    // },
                }
            },
            // messages: {
            //     id2: {
            //         remote: "This ID is already taken."
            //     },
            //     email: {
            //         remote: "This email is already taken."
            //     },
            // },
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
            let numberOfInvalids = patronValidator.numberOfInvalids();

            if (numberOfInvalids == 0 && $('#id2').val() && $('#first_name').val() && $('#last_name').val() &&
                $('#email').val() && tagifyRoles.value.length > 0) {
                $("#patron-modal-button").attr("disabled", false);
            } else {
                $("#patron-modal-button").attr("disabled", true);
            }
        }

        tagifyRoles.on('add', () => {
            toggleSubmitBtn()
        }).on('remove', () => {
            toggleSubmitBtn()
        });

        // █▀▀ █── █▀▀ █▀▀█ ▀█─█▀ █▀▀ 
        // █── █── █▀▀ █▄▄█ ─█▄█─ █▀▀ 
        // ▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ ──▀── ▀▀▀
        var amountCleave = new Cleave('#id2', {
            numericOnly: true,
            blocks: [11]
        });

        // █▀▀ ─▀─ █── █▀▀ █▀▀█ █▀▀█ █▀▀▄ █▀▀▄ 
        // █▀▀ ▀█▀ █── █▀▀ █──█ █──█ █──█ █──█ 
        // ▀── ▀▀▀ ▀▀▀ ▀▀▀ █▀▀▀ ▀▀▀▀ ▀──▀ ▀▀▀─
        $.fn.filepond.registerPlugin(FilePondPluginImagePreview);
        $.fn.filepond.registerPlugin(FilePondPluginFileValidateType);

        var imageFilePond = FilePond.create($('#image')[0], {
            imagePreviewHeight: 100,
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
        $('#patron-add').click(function() {
            $("#patron-modal-button").attr("disabled", true);
            $('#patron-modal-header').html('Add patron');
            $('#patron-modal-button').html('Add');
            $('#patron-form-action').val('add');
            $('#patron-modal').modal('show');
            imageFilePond.removeFiles();
        });


        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀▄ █──█ ▀▀█▀▀ ▀▀█▀▀ █▀▀█ █▀▀▄ █▀▀ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 █▀▀▄ █──█ ──█── ──█── █──█ █──█ ▀▀█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀─ ─▀▀▀ ──▀── ──▀── ▀▀▀▀ ▀──▀ ▀▀▀
        function toggleButtons() {
            let selectedRows = patronsTable.column(0).checkboxes.selected();

            if (selectedRows.length > 0) {
                $('#patron-delete-all, #patron-force-delete-all, #patron-restore-all').removeAttr('disabled');
                $('.patron-count').html('(' + selectedRows.length + ')');
            } else {
                $('#patron-delete-all, #patron-force-delete-all, #patron-restore-all').attr('disabled', true);
                $('.patron-count').html('');
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

            patronsTable.column(0).checkboxes.deselectAll();
            toggleButtons();
            patronsTable.ajax.reload();
            let action = $('#patron-form-action').val();
            $('#patron-form').trigger('reset');
            $('#patron-form-action').val(action);
            imageFilePond.removeFiles();
        }


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#patron-form').submit(function(e) {
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

            setFormDataTagifyInput('groups', tagifyGroups.value);
            setFormDataTagifyInput('roles', tagifyRoles.value);

            if ($('#patron-form-action').val() == 'add') {
                // ─█▀▀█ ░█▀▀▄ ░█▀▀▄ 
                // ░█▄▄█ ░█─░█ ░█─░█ 
                // ░█─░█ ░█▄▄▀ ░█▄▄▀
                $.ajax({
                    data: frm,
                    type: 'POST',
                    url: '{{ route('patrons.store') }}',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        ajaxSuccess(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

            } else if ($('#patron-form-action').val() == 'edit') {
                // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
                // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
                // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
                let id = $('#patron-hidden-id').val();
                let url = '{{ route('patrons.update', ['id' => ':id']) }}'.replace(':id', id);

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
                        $('#patron-modal').modal('hide');
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        })

        $("#patron-modal").on("hidden.bs.modal", function() {
            $('#patron-form').trigger('reset');
            imageFilePond.removeFiles();
            removeInputErrors();
        });

        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ ▀█▀ ──█── 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀──
        $('body').on('click', '.btn-patron-edit', function() {
            var user_id = $(this).data('id');

            $('#patron-modal-header').html('Update Patron');
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
                    tagifyGroups.addTags(data.groups);
                    tagifyRoles.addTags(data.roles);

                    if (data.image) {
                        imageFilePond.addFile(data.image);
                    }
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
            let selectedRows = patronsTable.column(0).checkboxes.selected();

            var id = [];
            $.each(selectedRows, function(key, element) {
                id.push($(element).val());
            });

            return id;
        }

        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('body').on('click', '.btn-patron-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('patrons.destroy') }}';

            swalConfirmation(
                'Delete this patron and all of its related information?',
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
        $('#patron-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('patrons.destroy') }}';

            swalConfirmation(
                'Delete ' + id.length + ' patron(s) and all of its related information?',
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
        $('body').on('click', '.btn-patron-restore', function() {
            let id = $(this).data('id');
            let url = '{{ route('patrons.restore') }}';

            swalConfirmation(
                'Restore this patron and all of its related information?',
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
        $('#patron-restore-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('patrons.restore') }}';

            swalConfirmation(
                'Restore ' + id.length + ' patron(s) and all of its related information?',
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
        $('body').on('click', '.btn-patron-force-delete', function() {
            let id = $(this).data('id');
            let url = '{{ route('patrons.force.delete') }}';

            swalConfirmation(
                'Permanently delete this patron and all of its related information?',
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
        $('#patron-force-delete-all').click(function() {
            let id = getIdArray();
            let url = '{{ route('patrons.force.delete') }}';

            swalConfirmation(
                'Permanently delete ' + id.length +
                ' patron(s) and all of its related information?',
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

        $('#patron-registrations').click(function() {
            window.location.href = '{{ route('registrations.index') }}';
        });

        $('#patron-attendance').click(function() {
            window.location.href = '{{ route('attendance.index') }}';
        });
    });
</script>
