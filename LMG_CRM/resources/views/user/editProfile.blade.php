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
                                    <h4>Personal Information</h4>
                                    <p>Basic info, like your name and address on this account.</p>
                                </div>
                                {{-- <button class="btn btn-primary">Edit Profile</button> --}}
                            </div>
                                <form id="edit-profile" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <div class="row">
                                        <div class="col" style="max-width: 200px;">
                                            <div class="mb-9 mb-2">
                                                {{-- <img class="img-fluid w-100 rounded" src="assets/images/avatars/thumb-1.jpg" alt="upload avatar"> --}}
                                                @if (!empty(auth('front')->user()->toArray()['photo']) && file_exists(base_path()."/public/".env('USER_PROFILE_PATH').auth('front')->user()->toArray()['photo']))
                                                    <img class="img-fluid w-100 rounded" src="{{env('USER_PROFILE_PIC').auth('front')->user()->toArray()['photo']}}" alt="profile_pic" />
                                                @else
                                                    @if (auth('front')->user()->toArray()['salute'] == 1)
                                                        <img class="img-fluid w-100 rounded" src="{{env('USER_PROFILE_PIC')}}male.png" alt="profile_pic" />
                                                    @else
                                                        <img class="img-fluid w-100 rounded" src="{{env('USER_PROFILE_PIC')}}female.png" alt="profile_pic" />
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="upload upload-text w-100">
                                                <div>
                                                    <label for="photo" class="btn btn-secondary w-100">Upload</label>
                                                </div>
                                                <input id="photo" name="photo" type="file" class="upload-input" accept="image/png, image/jpeg">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="row mb-9 mb-2">
                                                <label for="salute" class="col-sm-3 col-form-label control-label">Title</label>
                                                <div class="col-md-6">
                                                    <select class="form-select" name="salute" id="salute">
                                                        <option value="">Choose Title...</option>
                                                        <option value="1" {{$user->salute == 1 ? "selected" : "" }}>Mr.</option>
                                                        <option value="2" {{$user->salute == 2 ? "selected" : "" }}>Mrs.</option>
                                                        <option value="3" {{$user->salute == 3 ? "selected" : "" }}>Ms.</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="first_nm" class="col-sm-3 col-form-label control-label">First Name</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="first_nm" id="first_nm" placeholder="Enter your first name" value="{{old('first_nm') ?? ucwords(strtolower($user->first_nm)) }}"/>
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="last_nm" class="col-sm-3 col-form-label control-label">Last Name</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="last_nm" id="last_nm" placeholder="Enter your last name" value="{{old('last_nm') ?? ucwords(strtolower($user->last_nm)) }}"/>
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="emailid" class="col-sm-3 col-form-label control-label">Email</label>
                                                <div class="col-md-6">
                                                    <input type="email" class="form-control" name="emailid" id="emailid" placeholder="Enter your email" value="{{old('emailid') ?? $user->emailid }}" disabled="" />
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="mobnum" class="col-sm-3 col-form-label control-label">Mobile Number</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="mobnum" id="mobnum" placeholder="Enter your mobile number" value="{{old('mobnum') ?? $user->mobnum }}" disabled="" />
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="birthdate" class="col-sm-3 col-form-label control-label">Birth date</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="birthdate" id="birthdate" placeholder="Enter your date of birth" value="{{old('birthdate') ?? $user->birthdate }}" disabled="" />
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="address" class="col-sm-3 col-form-label control-label">Address</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="address" id="address" placeholder="Enter your address" value="{{old('address') ?? $user->address }}" disabled="" />
                                                </div>
                                            </div>
                                            <div class="row mb-9 mb-2">
                                                <label for="pincode" class="col-sm-3 col-form-label control-label">Pincode</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="pincode" id="pincode" placeholder="Enter your pincode" value="{{old('pincode') ?? $user->pincode }}" disabled="" />
                                                </div>
                                            </div>
                                            
                                            <div class="form-group text-start">
                                                <button class="btn btn-primary" name="update_profile" id="update_profile" disabled="disabled">Update Profile</button>
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
            $('#edit-profile').validate({
                ignore: ':hidden:not(:checkbox)',
                errorElement: 'label',
                errorClass: 'is-invalid error',
                validClass: 'is-valid',
                rules: {
                    salute: {
                        required: true,
                    },
                    first_nm: {
                        required: true,
                    },
                    last_nm: {
                        required: true,
                    }
                },
                messages: {
                    salute : "Please select title",
                    first_nm : "Please enter the first name",
                    last_nm : "Please enter the last name",
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });

            $('input[name="first_nm"], input[name="last_nm"], input[name="photo"]').change(function(){
                if ($(this).val())
                {
                    $("#update_profile").removeAttr('disabled');
                }
            });

            $('#salute').on('change', function(){
                $("#update_profile").removeAttr('disabled');
            })

            $('#update_profile').on('click', function(e){
                e.preventDefault();
                const valid = $('#edit-profile').valid();
                if(valid) {
                    var fd = new FormData($('#edit-profile')[0]);
                    $.ajax({
                        url:"{{route('updateMyProfile')}}",  
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