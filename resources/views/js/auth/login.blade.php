<script type="text/javascript">
    $(document).ready(function() {

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

        var registrationValidator = $("#registration-form").validate({
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
                    //             return $('.id2').eq(0).val();
                    //         }
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
                    //             return $('.email').eq(0).val();
                    //         }
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
                password: {
                    required: true,
                    passwordValidator: true

                },
                confirm_password: {
                    required: true,
                    equalTo: '.password'
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
                toggleRegistrationSubmitBtn();
            },
            onfocusout: function(element, event) {
                this.element(element);
                toggleRegistrationSubmitBtn();
            },
            errorPlacement: function(error, element) {
                let elementName = $(element).attr("name");
                let errorMessage = error.text();

                $('.' + elementName + '_msg').html(errorMessage);
                $('.form_group_' + elementName).addClass('has-error has-feedback');
            },
            success: function(label, element) {
                let elementName = $(element).attr("name");

                $('.' + elementName + '_msg').html('');
                $('.form_group_' + elementName).removeClass('has-error has-feedback');
            }
        });

        var forgotPasswordValidator = $("#forgot-password-form").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                }
            },
            onkeyup: function(element, event) {
                this.element(element);
                toggleForgotPasswordSubmitBtn();
            },
            onfocusout: function(element, event) {
                this.element(element);
                toggleForgotPasswordSubmitBtn();
            },
            errorPlacement: function(error, element) {
                let elementName = $(element).attr("name");
                let errorMessage = error.text();

                $('.' + elementName + '_msg').html(errorMessage);
                $('.form_group_' + elementName).addClass('has-error has-feedback');
            },
            success: function(label, element) {
                let elementName = $(element).attr("name");

                $('.' + elementName + '_msg').html('');
                $('.form_group_' + elementName).removeClass('has-error has-feedback');
            }
        });

        function toggleRegistrationSubmitBtn() {
            let numberOfInvalids = registrationValidator.numberOfInvalids();
            let form = $("#registration-form");

            if (numberOfInvalids == 0 && form.find('.id2:first').val() && form.find('.first_name:first')
            .val() && form.find('.last_name:first').val() && form.find('.email:first').val() && form.find(
                    '.password:first').val() && form.find('.confirm_password:first')
                .val()) {
                $("#btn-registration-submit").attr("disabled", false);
            } else {
                $("#btn-registration-submit").attr("disabled", true);
            }
        }

        function toggleForgotPasswordSubmitBtn() {
            let numberOfInvalids = forgotPasswordValidator.numberOfInvalids();
            let form = $("#forgot-password-form");

            if (numberOfInvalids == 0 && form.find('.email:first').val()) {
                $("#btn-forgot-password-submit").attr("disabled", false);
            } else {
                $("#btn-forgot-password-submit").attr("disabled", true);
            }
        }

        $('#btn-create-an-account').click(function() {
            $("#btn-registration-submit").attr("disabled", true);
        });

        $('#btn-forgot-password').click(function() {
            $("#btn-forgot-password-submit").attr("disabled", true);
        });



        // ░█▀▀▀█ ░█──░█ ─█▀▀█ ░█─── █▀█ 
        // ─▀▀▀▄▄ ░█░█░█ ░█▄▄█ ░█─── ─▄▀ 
        // ░█▄▄▄█ ░█▄▀▄█ ░█─░█ ░█▄▄█ █▄▄
        function swal(title, icon) {
            Swal.fire({
                position: 'center',
                icon: icon,
                title: title,
                // showConfirmButton: false,
                timer: 5000,
                confirmButtonColor: '#1572E8',
            })
        }

        const showLoading = () => {
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we process your request.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false
            });
        };

        const hideLoading = () => {
            Swal.close();
        };

        // █▀▀ █░░█ █▀▀█ █░░░█ 　 ░▀░ █▀▀▄ █▀▀█ █░░█ ▀▀█▀▀ 　 █▀▀ █▀▀█ █▀▀█ 
        // ▀▀█ █▀▀█ █░░█ █▄█▄█ 　 ▀█▀ █░░█ █░░█ █░░█ ░░█░░ 　 █▀▀ █▄▄▀ █▄▄▀ 
        // ▀▀▀ ▀░░▀ ▀▀▀▀ ░▀░▀░ 　 ▀▀▀ ▀░░▀ █▀▀▀ ░▀▀▀ ░░▀░░ 　 ▀▀▀ ▀░▀▀ ▀░▀▀
        function showInputErrors(data) {
            for (let key in data.msg) {
                $('.' + String(key) + '_msg').html(String(data.msg[key][0]));
                $('.form_group_' + String(key)).addClass(
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
            if (data.code == 400) {
                showInputErrors(data);
                return;
            }
        }


        // █▀▀█ █▀▀ █▀▀ █▀▀ ▀▀█▀▀ 　 █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 
        // █▄▄▀ █▀▀ ▀▀█ █▀▀ ──█── 　 █▀▀ █──█ █▄▄▀ █─▀─█ 
        // ▀─▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ──▀── 　 ▀── ▀▀▀▀ ▀─▀▀ ▀───▀
        $("#registration-modal,#forgot-password-modal").on("hidden.bs.modal", function() {
            $('#registration-form,#forgot-password-form').trigger('reset');
            $('.input_msg').html('');
            $('.form-group').removeClass('has-error has-feedback');
        });


        // █▀▀ █▀▀█ █▀▀█ █▀▄▀█ 　 █▀▀ █──█ █▀▀▄ █▀▄▀█ ─▀─ ▀▀█▀▀ 
        // █▀▀ █──█ █▄▄▀ █─▀─█ 　 ▀▀█ █──█ █▀▀▄ █─▀─█ ▀█▀ ──█── 
        // ▀── ▀▀▀▀ ▀─▀▀ ▀───▀ 　 ▀▀▀ ─▀▀▀ ▀▀▀─ ▀───▀ ▀▀▀ ──▀──
        $('#registration-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();
            
            let frm = new FormData(this);

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('registrations.store') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.code == 400) {
                        showInputErrors(data);
                        hideLoading();
                        return;
                    }

                    window.location.href = data.redirect;
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });


        $('#forgot-password-form').submit(function(e) {
            e.preventDefault();
            removeInputErrors();
            showLoading();

            let frm = new FormData(this);

            $.ajax({
                data: frm,
                type: 'POST',
                url: '{{ route('forgot.password.store') }}',
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.code == 400) {
                        showInputErrors(data);
                        hideLoading();
                        return;
                    }

                    window.location.href = data.redirect;
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    });
</script>
