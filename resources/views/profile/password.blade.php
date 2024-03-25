@extends('layouts.app')
@section('content')
    <style>
        body{
            background: #e5f6ee;
        }
        .form-control[readonly] {
            background-color: #c7c7c7 !important;
        }

        .password-show {
            position: relative;
        }

        .password-show input {
            padding-right: 2.5rem;
        }

        .password-show__toggle {
            position: absolute;
            top: 15px;
            right: 0;
            bottom: 0;
            width: 2.5rem;
        }

        .password-show_toggleshow-icon,
        .password-showtoggle_hide-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #555;
        }

        .password-show_toggle_show-icon {
            display: block;
        }

        .password-show.show .password-show_toggle_show-icon {
            display: none;
        }

        .password-show_toggle_hide-icon {
            display: none;
        }

        .password-show.show .password-show_toggle_hide-icon {
            display: block;
        }

        .password-show__toggle {
            position: absolute;
            top: 54%;
        }

        .page input[type=password] {
            border-right-color: #e5dede !important;
        }

        .app-content .side-app {
            padding: 0px 20px;
        }
    </style>
    @if ($message = Session::get('status'))
        <div class="alert alert-success">
            <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
        </div>
    @endif
    </div>
    <div class="col-lg-12">
        @if ($message = Session::get('errstatus'))
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    <li>{{ $message }}</li>
                </ul>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="myForm" onsubmit="return validateForm()" action="{{ route('profile.update_password') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="form-body" style="background: #fff;
            padding: 20px;
            border-radius: 5px;
            margin: 0 20px;">
                <div class="row" style="min-height: auto;">
                    <div class="col-md-12">
                        <div class="form-group" style="margin-bottom: 10px">
                            <label class="form-label">Old Password</label>
                            <div class="password-show">
                                <input type="password" required class="form-control" name="old_password" id="old_password"
                                    value="" placeholder="Old Password">
                                <div class="password-show__toggle"
                                    style="top: 35%;
                                width: unset;
                                right: 17px;">
                                    <i class="fa fa-eye password-show_toggle_show-icon"></i>
                                    <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">New Password*</label>
                            <div class="password-show">
                                <input type="password" id="password" class="form-control" required name="password"
                                    onkeyup="validatePassLength()" placeholder="New Password" value="">
                                <div class="password-show__toggle"
                                    style="top: 35%;
                                            width: unset;
                                            right: 17px;">
                                    <i class="fa fa-eye password-show_toggle_show-icon"></i>
                                    <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                                </div>
                                <span id="showpassmessage"></span>
                                <span id="showpassmessage2"></span>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-right: 0;padding-left:0">
                            <div class="form-group">
                                <label class="form-label">Password Confirmation*</label>
                                <div class="password-show">
                                    <input type="password" id="confirm_password" class="form-control" required
                                        name="password_confirmation" onkeyup="validatePass()" placeholder="Confirm Password"
                                        value="">
                                    <div class="password-show__toggle"
                                        style="    top: 35%;
                                                width: unset;
                                                right: 17px;">
                                        <i class="fa fa-eye password-show_toggle_show-icon"></i>
                                        <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                                    </div>
                                </div>
                                <span id="showmessage"></span>
                            </div>
                        </div>
                    </div>
                    <!-- ... -->
                    <div class="form-group" style="margin:10px auto 10px;">
                        <center>
                            <button type="submit" class="btn btn-raised btn-primary" style="padding:5px 10px">
                                <i class="fa fa-check-square-o"></i> Update Password</button>
                        </center>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $(".password-show__toggle").on("click", function(e) {
                console.log("click");
                if (
                    !$(this)
                    .parent()
                    .hasClass("show")
                ) {
                    $(this)
                        .parent()
                        .addClass("show");
                    $(this)
                        .prev()
                        .attr("type", "text");
                } else {
                    $(this)
                        .parent()
                        .removeClass("show");
                    $(this)
                        .prev()
                        .attr("type", "password");
                }
            });
        });
    </script>


    <script>
        function checkPasswordComplexity(pwd) {
            // var re = /^(?=.*\d)(?=.*[a-z])(.{8,50})$/
            var re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/

            if (pwd != '') {

                if (re.test(pwd) == false) {
                    document.getElementById('showpassmessage2').style.color = 'red';
                    //      document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
                    document.getElementById('showpassmessage2').innerHTML =
                        'Password must include at least one upper case letter, lower case letter, number, and special character';
                    $('#submit').attr('disabled', 'disabled');
                    validatePass();
                } else {
                    document.getElementById('showpassmessage2').innerHTML = '';
                    $('#submit').attr('disabled', false);
                    validatePass();
                }
            } else {
                document.getElementById('showpassmessage2').innerHTML = '';
                $('#submit').attr('disabled', false);
                validatePass();

            }
        }




        function validatePassLength() {
            var x = document.forms["myForm"]["password"].value;
            if (x != '') {
                if (x.length < 8) {
                    document.getElementById('showpassmessage').style.color = 'red';
                    document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 Characters!';
                } else {
                    document.getElementById('showpassmessage').innerHTML = '';

                }
            } else {
                document.getElementById('showpassmessage').innerHTML = '';

            }

        }
    </script>


    <script>
        function validatePass() {
            var x = document.forms["myForm"]["password"].value;
            var y = document.forms["myForm"]["confirm_password"].value;
            document.getElementById('showmessage').innerHTML = '';
            if (y != '') {
                if (x == y) {
                    document.getElementById('password').border.color = 'green';
                    document.getElementById('confirm_password').border.color = 'green';


                } else {
                    document.getElementById('showmessage').style.color = 'red';
                    document.getElementById('showmessage').innerHTML = 'Password is not matching';
                }
            }
        }
    </script>


    <script>
        function validateForm() {
            var x = document.forms["myForm"]["password"].value;
            var y = document.forms["myForm"]["confirm_password"].value;
            if (x.length >= 8) {
                if (x != y) {
                    document.getElementById('showmessage').style.color = 'red';
                    document.getElementById('showmessage').innerHTML = 'Password is not matching';
                    var elmnt = document.getElementById("passlabel");
                    elmnt.scrollIntoView();
                    return false;
                }
            } else {
                document.getElementById('showpassmessage').style.color = 'red';
                document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 digits!';
                var elmnt = document.getElementById("passlabel");
                elmnt.scrollIntoView();
                return false;
            }
        }
    </script>
@endsection
