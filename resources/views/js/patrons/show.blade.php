<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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
                @if (auth()->user()->temp_role == 'librarian')
                    id2: {
                        required: true,
                        digits: true,
                        maxlength: 11,
                        minlength: 2,
                    },
                @endif
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

        $.validator.addMethod("passwordValidator", function(value) {
                // Check for at least one uppercase letter
                if (!/[A-Z]/.test(value)) {
                    return false;
                }

                // Check for at least one digit
                if (!/\d/.test(value)) {
                    return false;
                }

                if (value.length < 8) {
                    return false;
                }

                return true;
            },
            "The password must be 8 characters or more and contain at least one uppercase letter and one digit."
        );

        var changePasswordValidator = $("#change-password-form").validate({
            rules: {
                old_password: {
                    required: true
                },
                new_password: {
                    required: true,
                    passwordValidator: true

                },
                confirm_new_password: {
                    required: true,
                    equalTo: '#new_password'
                }
            },
            onkeyup: function(element, event) {
                this.element(element);
                toggleChangePasswordBtn();
            },
            onfocusout: function(element, event) {
                this.element(element);
                toggleChangePasswordBtn();
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

        function toggleChangePasswordBtn() {
            let numberOfInvalids = changePasswordValidator.numberOfInvalids();

            if (numberOfInvalids == 0 && $('#old_password').val() && $('#new_password').val() && $(
                    '#confirm_new_password').val()) {
                $("#btn-change-password-submit").attr("disabled", false);
            } else {
                $("#btn-change-password-submit").attr("disabled", true);
            }
        }

        $('#btn-change-password').click(function() {
            $("#btn-change-password-submit").attr("disabled", true);
        });

        $("#change-password-modal,#update-patron").on("hidden.bs.modal", function() {
            $('#patron-form,#change-password').trigger('reset');
            imageFilePond.removeFiles();
            removeInputErrors();
        });

        // █▀▀ █── █▀▀ █▀▀█ ▀█─█▀ █▀▀ 
        // █── █── █▀▀ █▄▄█ ─█▄█─ █▀▀ 
        // ▀▀▀ ▀▀▀ ▀▀▀ ▀──▀ ──▀── ▀▀▀
        new Cleave('#id2', {
            numeral: true,
            numeralPositiveOnly: true,
            delimiter: ''
        });

        // ▀▀█▀▀ █▀▀█ █▀▀▀ █▀▀▀ █── █▀▀ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 
        // ──█── █──█ █─▀█ █─▀█ █── █▀▀ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 　 █▀▀▄ ──█── █──█ 
        // ──▀── ▀▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ ▀▀▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀── 　 ▀▀▀─ ──▀── ▀──▀
        function toggleSubmitBtn() {
            let numberOfInvalids = patronValidator.numberOfInvalids();

            if (numberOfInvalids == 0 @if(auth()->user()->temp_role == 'librarian') && $('#id2').val() @endif && $('#first_name').val() && $('#last_name').val() &&
                $('#email').val() && tagifyRoles.value.length > 0) {
                $("#patron-modal-button").attr("disabled", false);
            } else {
                $("#patron-modal-button").attr("disabled", true);
            }
        }

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

            // █──█ █▀▀█ █▀▀▄ █▀▀█ ▀▀█▀▀ █▀▀ 
            // █──█ █──█ █──█ █▄▄█ ──█── █▀▀ 
            // ─▀▀▀ █▀▀▀ ▀▀▀─ ▀──▀ ──▀── ▀▀▀
            let id = '{{ $patron->id }}';
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

                    $('#patron-modal').modal('hide');
                    Livewire.emit('updatePatron');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        })

        // █▀▀ █▀▀▄ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ ▀█▀ ──█── 
        // ▀▀▀ ▀▀▀─ ▀▀▀ ──▀──
        $('#btn-patron-edit').click(function() {
            imageFilePond.removeFiles();
            removeInputErrors();
            $('#patron-form').trigger('reset');

            var patronId = '{{ $patron->id }}';

            $.ajax({
                type: 'get', // method shown on route:list
                url: "{{ route('patrons.index') }}" + "/" + patronId + "/edit",
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

        // █▀▀▄ █▀▀ █── █▀▀ ▀▀█▀▀ █▀▀ 　 █▀▀▄ ▀▀█▀▀ █▀▀▄ 　 █──█ █▀▀█ █▀▀▄ █▀▀▄ █── █▀▀ 
        // █──█ █▀▀ █── █▀▀ ──█── █▀▀ 　 █▀▀▄ ──█── █──█ 　 █▀▀█ █▄▄█ █──█ █──█ █── █▀▀ 
        // ▀▀▀─ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── ▀▀▀ 　 ▀▀▀─ ──▀── ▀──▀ 　 ▀──▀ ▀──▀ ▀──▀ ▀▀▀─ ▀▀▀ ▀▀▀
        $('#btn-patron-delete').click(function() {
            let url = '{{ route('patrons.destroy') }}';

            swalConfirmation(
                'Delete this patron and all of its related information?',
                'Yes, delete it!',
                function() {
                    $.ajax({
                        data: {
                            id: '{{ $patron->id }}'
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
                                    '{{ route('patrons.index') }}';
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
        $('#btn-patron-restore').click(function() {
            let url = '{{ route('patrons.restore') }}';

            swalConfirmation(
                'Restore this patron and all of its related information?',
                'Yes, restore it!',
                function() {
                    $.ajax({
                        data: {
                            id: '{{ $patron->id }}'
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
                                    '{{ route('patrons.archive') }}';
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
        $('#btn-patron-force-delete').click(function() {
            let url = '{{ route('patrons.force.delete') }}';

            swalConfirmation(
                'Delete patron permanently and all of its related information?',
                'Yes, Delete it!',
                function() {
                    $.ajax({
                        data: {
                            id: '{{ $patron->id }}'
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
                                    '{{ route('patrons.archive') }}';
                            }, 2000); // 2000 milliseconds = 2 seconds
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            )
        });


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀█ █▀▀ █▀▀ █▀▀ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 █▄▄▀ █▀▀ ▀▀█ █▀▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ──▀──
        $("#change-password-modal").on("hidden.bs.modal", function() {
            $('#change-password-form').trigger('reset');
        });


        // █▀▀ █──█ █▀▀█ █▀▀▄ █▀▀▀ █▀▀ 　 █▀▀█ █▀▀█ █▀▀ █▀▀ █───█ █▀▀█ █▀▀█ █▀▀▄ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 
        // █── █▀▀█ █▄▄█ █──█ █─▀█ █▀▀ 　 █──█ █▄▄█ ▀▀█ ▀▀█ █▄█▄█ █──█ █▄▄▀ █──█ 　 █▀▀ █──█ █▄▄▀ █─▀─█ 
        // ▀▀▀ ▀──▀ ▀──▀ ▀──▀ ▀▀▀▀ ▀▀▀ 　 █▀▀▀ ▀──▀ ▀▀▀ ▀▀▀ ─▀─▀─ ▀▀▀▀ ▀─▀▀ ▀▀▀─ 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀
        $('#change-password-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();

            let frm = new FormData(this);

            $.ajax({
                data: frm,
                type: 'POST',
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },
                url: '{{ route('change.password') }}',
                processData: false,
                contentType: false,
                success: function(data) {
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

                    $('#change-password-modal').modal('hide');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        })
    });
</script>
