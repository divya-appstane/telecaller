@extends('user.layouts.master')

@section('main-section')
<div class="content">
    <div class="main">
        <div class="page-header">
            {{-- <h4 class="page-title">Profile</h4> --}}
            <div class="breadcrumb">
               <span class="me-1 text-gray"><i class="feather icon-home"></i></span>
               <div class="breadcrumb-item"><a href="{{route(session()->get('load_dashboard').'.dashboard')}}"> Dashboard </a></div>
               <div class="breadcrumb-item"><a href="{{route('admin.system.users')}}"> System Users </a></div>
               <div class="breadcrumb-item"><a href="{{route('admin.system.viewUserProfile', ["emp_id" => base64_encode($user->emp_id)])}}"> View Profile </a></div>
            </div>
        </div>
        <div class="card">
            <div class="container-fluid">
                <div class="row content-min-height">
                    {{-- <div class="p-0 column-panel border-end" style="max-width: 230px; min-width: 230px; left: -230px;">
                        <h4 class="mb-3 ms-3 mt-3">Profile</h4>
                        <div class="columns-panel-item-group">
                            <a class="columns-panel-item columns-panel-item-link {{Route::currentRouteNamed('viewMyProfile') ? 'active' : '' }}" href="{{route('viewMyProfile')}}">
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
                    </div> --}}
                    <div class="col">
                        <div class="card-body">
                            <div class="mb-4 d-md-flex align-items-center justify-content-between">
                                <div>
                                    <h4>Personal Information</h4>
                                    <p>Basic user related information, like name and address on this account.</p>
                                </div>
                                {{-- <a class="btn btn-primary" href="{{route('editMyProfile')}}">Edit Profile</a> --}}
                            </div>
                            <div class="row">
                                <div class="col" style="max-width: 200px;">
                                    <div class="mb-3">
                                        {{-- <img class="img-fluid w-100 rounded" src="assets/images/avatars/thumb-1.jpg" alt="upload avatar"> --}}
                                        @if (!empty($user->photo) && file_exists(base_path()."/public/".env('USER_PROFILE_PATH').$user->photo))
                                            <img class="img-fluid w-100 rounded" src="{{env('USER_PROFILE_PIC').$user->photo}}" alt="profile_pic" />
                                        @else
                                            @if ($user->salute == 1)
                                                <img class="img-fluid w-100 rounded" src="{{env('USER_PROFILE_PIC')}}male.png" alt="profile_pic" />
                                            @else
                                                <img class="img-fluid w-100 rounded" src="{{env('USER_PROFILE_PIC')}}female.png" alt="profile_pic" />
                                            @endif
                                        @endif
                                    </div>
                                    {{-- <div class="upload upload-text w-100">
                                        <div>
                                            <label for="upload" class="btn btn-secondary w-100">Upload</label>
                                        </div>
                                        <input id="upload" type="file" name="file" class="upload-input" accept="image/png, image/jpeg">
                                    </div> --}}
                                </div>
                                <div class="col-md">
                                    <table class="table">
                                        <tbody>
                                           <tr>
                                              <th class="py-4">First Name</th>
                                              <td class="py-4">{{ ucwords(strtolower($user->first_nm)) }}</td>
                                           </tr>
                                           <tr>
                                              <th class="py-4">Last Name</th>
                                              <td class="py-4">{{ ucwords(strtolower($user->last_nm)) }}</td>
                                           </tr>
                                           <tr>
                                              <th class="py-4">Email</th>
                                              <td class="py-4">{{ $user->emailid }}</td>
                                           </tr>
                                           <tr>
                                              <th class="py-4">Phone</th>
                                              <td class="py-4">{{ $user->mobnum }}</td>
                                           </tr>
                                           <tr>
                                              <th class="py-4">Gender</th>
                                              <td class="py-4">
                                                @if ($user->salute == 1)
                                                    Male
                                                @else
                                                    Female
                                                @endif
                                              </td>
                                           </tr>
                                           <tr>
                                              <th class="py-4">Birthday</th>
                                              <td class="py-4">{{ date('d-m-Y', strtotime($user->birthdate)) }}</td>
                                           </tr>
                                           <tr>
                                              <th class="py-4">Address</th>
                                              <td class="py-4">{{ $user->address }}</td>
                                           </tr>
                                           <tr>
                                              <th class="py-4">Pincode</th>
                                              <td class="py-4">{{ $user->pincode }}</td>
                                           </tr>
                                        </tbody>
                                     </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection