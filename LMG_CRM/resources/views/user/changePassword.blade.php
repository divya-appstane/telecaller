@extends('user.layouts.master')

@section('main-section')
@push('css')
    <style>
        .error {
            color: red;
        }
    </style>
@endpush
<div class="content">
    <div class="main">
        <div class="page-header">
            {{-- <h4 class="page-title">Profile</h4> --}}
            <div class="breadcrumb">
               <span class="me-1 text-gray"><i class="feather icon-home"></i></span>
               <div class="breadcrumb-item"><a href="{{route(session()->get('load_dashboard').'.dashboard')}}"> Dashboard </a></div>
               <div class="breadcrumb-item"><a href="{{route('editMyProfile')}}"> Edit Profile </a></div>
            </div>
        </div>
        <div class="card">
            <div class="container-fluid">
                <div class="row content-min-height">
                    <div class="p-0 column-panel border-end" style="max-width: 230px; min-width: 230px; left: -230px;">
                        <h4 class="mb-9 mb-2 ms-3 mt-3">Profile</h4>
                        <div class="columns-panel-item-group">
                            <a class="columns-panel-item columns-panel-item-link {{Route::currentRouteNamed('editMyProfile') ? 'active' : '' }}" href="{{route('viewMyProfile')}}">
                                <div class="d-flex align-items-center">
                                    <i class="feather font-size-lg icon-user"></i>
                                    <span class="ms-3">Personal</span>
                                </div>
                            </a>
                            <a class="columns-panel-item columns-panel-item-link {{Route::currentRouteNamed('changeProfilePassword') ? 'active' : '' }}" href="{{route('changeProfilePassword')}}">
                                <div class="d-flex align-items-center">
                                    <i class="feather font-size-lg icon-bell"></i>
                                    <span class="ms-3">Change Password</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-body">
                            <div class="mb-4 d-md-flex align-items-center justify-content-between">
                                <div>
                                    <h4>Change your Password</h4>
                                    <p></p>
                                </div>
                                {{-- <button class="btn btn-primary">Edit Profile</button> --}}
                            </div>
                                <form id="change-password-form" novalidate>
                                    @csrf
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="row mb-9 mb-2">
                                                <label for="old" class="col-sm-3 col-form-label control-label">Old Password</label>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" name="old" id="old" placeholder="Enter your old password" value=""/>
                                                        <span class="input-group-text"><i class="suffix-icon feather cursor-pointer text-dark icon-eye toggle-password" ng-reflect-ng-class="icon-eye"></i></span>
                                                    </div>
                                                    <span class="error old-error oldError"></span>
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="password" class="col-sm-3 col-form-label control-label">Password</label>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter your new password" value=""/>
                                                        <span class="input-group-text"><i class="suffix-icon feather cursor-pointer text-dark icon-eye toggle-password" ng-reflect-ng-class="icon-eye"></i></span>
                                                    </div>
                                                    <span class="error password-error"></span>
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="confirm-password" class="col-sm-3 col-form-label control-label">Confirm Password</label>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Re-enter your new password" value=""/>
                                                        <span class="input-group-text"><i class="suffix-icon feather cursor-pointer text-dark icon-eye toggle-password" ng-reflect-ng-class="icon-eye"></i></span>
                                                    </div>
                                                    <span class="error password_confirmation-error"></span>
                                                    {{-- <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Re-enter your new password" value=""/> --}}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group text-start">
                                                <button class="btn btn-primary" name="change_password" id="change_password">Update Profile</button>
                                            </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.toggle-password').on('click', function(){
                $(this).toggleClass("icon-eye icon-eye-off");
                var input = $("#password");
                var input2 = $("#old");
                if (input.attr("type") === "password" && input2.attr("type") === "password") {
                    input.attr("type", "text");
                    input2.attr("type", "text");
                } else {
                    input.attr("type", "password");
                    input2.attr("type", "password");
                }
            });

            $('#change-password-form').validate({
                ignore: ':hidden:not(:checkbox)',
                errorElement: 'label',
                errorClass: 'is-invalid error',
                validClass: 'is-valid',
                rules: {
                    old: {
                        required: true,
                    },
                    password: {
                        required: true,
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    old : "Please enter the old password",
                    password : "Please enter the new password",
                    password_confirmation : {
                        require: "Please enter the confirm password",
                        equalTo: "Both password didnot match",
                    },
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    $(`.${$(error).attr('id')}`).html(error)
                    console.log(error);
                    // error.insertAfter(element);
                }
            });

            $('#old').on('blur', function(e) {
                e.preventDefault();
                $('.oldError').html('');
                $('#change_password').prop('disabled', false);
                const old_password = $.trim($(this).val());
                if(old_password != ""){
                    $.ajax({
                        url:"{{route('verifyOldPassword')}}",  
                        method:"POST",  
                        data:{old_password:old_password},  
                        success: function(response){
                            // console.log(response);
                            if(response.status == 'error') {
                                $('.oldError').html(response.message);
                                $('#change_password').prop('disabled', true);
                            }
                            else 
                            {
                                $('.oldError').html('');
                                $('#change_password').prop('disabled', false);
                            }
                        },
                    });
                }
            });

            $('#change_password').on('click', function(e){
                e.preventDefault();
                const valid = $('#change-password-form').valid();
                if(valid) {
                    var fd = new FormData($('#change-password-form')[0]);
                    $.ajax({
                        url:"{{route('updatePassword')}}",  
                        method:"POST",  
                        data:fd,  
                        contentType:false, 
                        processData:false,  
                        cache: false,
                        success: function(response){
                            // console.log(response);

                            Swal.fire({
                                title: response.status,
                                text: response.message,
                                icon: response.status,
                                showConfirmButton: false,
                                showCancelButton: false,
                                showCloseButton: false,
                                timer: swalModelTimeOut
                            });

                            if(response.status == 'success'){
                                setTimeout(() => {
                                    window.location.href = `{{route('viewMyProfile')}}`;
                                }, pageReloadTimeOut);
                            }

                        },
                        error: function(errors) {
                            if( errors.status === 422 ) {
                                Swal.fire({
                                    title: "warning",
                                    text: "Something went wrong",
                                    icon: "warning",
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    showCloseButton: false,
                                    timer: swalModelTimeOut
                                });
                                var errors = errors.responseJSON.errors;

                                $.each(errors, function (key, value) {
                                    $('.error').removeClass("d-none");
                                    $('.form-control').addClass('is-invalid');
                                    $('.error').show().append(value+"<br/>");
                                });
                            }
                        },
                    });
                }
            });
        });
    </script>
@endpush    
@endsection