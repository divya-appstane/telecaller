<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LMG - Login Page'}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{env('USER_ASSETS')}}images/logo/favicon.ico">

    <!-- page css -->
    <style>
        .error{
            color:red;
        }
    </style>
    <!-- Core css -->
    <link href="{{env('USER_ASSETS')}}css/app.min.css" rel="stylesheet">

</head>

<body>
    <div class="auth-full-height">
        <div class="row m-0">
            <div class="col p-0 auth-full-height" style="background-image: url({{env('USER_ASSETS')}}images/others/bg-1.jpg);">
                <div class="d-flex justify-content-between flex-column h-100 px-5 py-3">
                    <div></div>
                    <div class="w-100 ">
                        <h1 class="display-4 mb-4 text-white">LetMeGrab Seller Lead Management</h1>
                        <p class="lead text-white" style="max-width: 630px;">“All power is within you; you can do anything and everything. Believe in that, do not believe that you are weak; do not believe that you are half-crazy lunatics, as most of us do nowadays. You can do anything and everything, without even the guidance of anyone. Stand up and express the divinity within you.” <br/><em> SWAMI VIVEKANANDA</em></p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-white">© <?= date('Y'); ?> Letmegrab e-Platform Pvt. Ltd.</span>
                        <div>
                            <a href="{{ env('APP_IN_STAGGING') == 1 ? env('ALPHA_HEROES_URL') : env('LIVE_HEROES_URL') }}" class="text-white text-link me-3" target="_blank">Heroes Portal</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 p-0 auth-full-height bg-white" style="max-width: 450px;">
                <div class="d-flex h-100 align-items-center p-5">
                    <div class="w-100">
                        <div class="d-flex justify-content-center mt-3">
                            <div class="text-center logo">
                                <img alt="logo" class="img-fluid" src="{{env('USER_ASSETS')}}images/logo/logo.png" style="height: 70px;">
                            </div>
                        </div>
                        <div class="mt-4">
                            {{-- @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-success">No errors</div>
                            @endif --}}
                            <div class="alert alert-danger d-none err text-center"></div>
                            <form id="loginForm" name="loginForm" method="post" novalidate>
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="empusrid" class="form-label">Username</label>
                                    <input class="form-control @error('empusrid') is-invalid @enderror" name="empusrid" id="empusrid" value="{{ old('empusrid') }}"/>
                                    @error('empusrid')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="emppass" class="form-label d-flex justify-content-between"><span>Password</span>
                                        {{-- <a href="" class="text-primary font">Forget emppass?</a> --}}
                                    </label>
                                    <div class="form-group input-affix flex-column">
                                        <input formcontrolname="emppass" class="form-control @error('emppass') is-invalid @enderror" type="password" name="emppass" id="emppass"/>
                                        <i class="suffix-icon feather cursor-pointer text-dark icon-eye toggle-password" ng-reflect-ng-class="icon-eye"></i>
                                    </div>
                                    @error('emppass')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="button" class="btn btn-primary w-100" id="submit" name="submit">Log In</button>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
    
    <!-- Core Vendors JS -->
    <script src="{{env('USER_ASSETS')}}js/vendors.min.js"></script>

    <!-- page js -->
    
    <!-- Core JS -->
    <script src="{{env('USER_ASSETS')}}js/app.min.js"></script>
    <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
    <!-- Sweetalert2 JS (CDN)-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.toggle-password').on('click', function(){
                const val = $.trim($("#emppass").val());
                if(val != ''){
                    $(this).toggleClass("icon-eye icon-eye-off");
                    var input = $("#emppass");
                    if (input.attr("type") === "password") {
                        input.attr("type", "text");
                    } else {
                        input.attr("type", "password");
                    }
                }
            });

            $("#loginForm").validate({
                ignore: ".ignore",
                debug: false,
                errorElement: 'span',
                rules: {
                    emppass: {
                        required: true,
                        // minlength: 8,
                    },
                    empusrid: {
                        required: true,
                    }
                },
                messages: {
                    emppass: "Please enter your profile password.",
                    empusrid: "Please enter your employee username.",
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
            }); 

            $('#submit').on('click', function() {
                const valid = $("#loginForm").valid();
                if(valid){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    let fd = new FormData($('#loginForm')[0]);
                    const swalModelTimeOut = 2000;
                    const pageReloadTimeOut = 3500;
                    $.ajax({
                        url: "{{route('user.checklogin')}}",
                        type: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType:"json",
                        success: function(response) {
                            if(response.status == 'success') {
                                Swal.fire({
                                    title: response.status,
                                    text: response.message,
                                    icon: response.status,
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    showCloseButton: false,
                                    timer: swalModelTimeOut
                                });

                                setTimeout(() => {
                                    window.location.href = `${response.load_dashboard}/dashboard`;
                                }, pageReloadTimeOut);
                                
                            } else {
                                Swal.fire({
                                    title: 'error',
                                    text: response.message,
                                    icon: 'error',
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    showCloseButton: false,
                                    timer: swalModelTimeOut
                                });
                                $('#empusrid').val('');
                                $('#emppass').val('');
                                $('.err').removeClass('d-none');
                                $('.err').html(response.message);

                                if(response.status == 'unauthorized') {
                                    setTimeout(() => {
                                        window.location.href = `{{route('logout')}}`;
                                    }, pageReloadTimeOut);
                                }
                            }
                        },
                        error: function(data){
                            if( data.status === 422 ) {
                                var errors = $.parseJSON(data.responseText);
                                $.each(errors, function (key, value) {
                                    // console.log(key+ " " +value);
                                    $('.err').removeClass("d-none");

                                    if($.isPlainObject(value)) {
                                        $.each(value, function (key, value) {                       
                                            // console.log(key+ " " +value);
                                            $(key).addClass('is-invalid');
                                        $('.err').show().append(value+"<br/>");
                                        });
                                    }
                                });
                            }
                        } 
                    });
                }
            });
        });
    </script>
</body>

</html>