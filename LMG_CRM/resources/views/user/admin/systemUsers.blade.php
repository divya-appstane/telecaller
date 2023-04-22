@extends('user.layouts.master')

@section('main-section')
<div class="content">
    <div class="main">
        <div class="page-header">
            <h4 class="page-title"></h4>
            <div class="breadcrumb">
               <span class="me-1 text-gray"><i class="feather icon-home"></i></span>
               <div class="breadcrumb-item"><a href="{{route(session()->get('load_dashboard').'.dashboard')}}"> Dashboard </a></div>
               <div class="breadcrumb-item"><a href="{{route('admin.system.users')}}"> System Users </a></div>
            </div>
        </div>
        <div class="card">
            <div class="updateStatus"></div>
            <div class="card-body">
                <div>
                    <table class="table table-hover user-list-table">
                        <thead>
                            <tr>
                                {{-- <th>
                                    <div class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input">
                                    </div>
                                </th> --}}
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Online</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($allUsers) > 0)
                                @foreach ($allUsers as $user)
                                        
                                    @foreach ($user->getDesignationWiseEmp as $empDetails)
                                        {{-- <pre>
                                        {{print_r($empDetails)}}
                                        </pre> --}}
                                        <tr>
                                            {{-- <td>
                                                <div class="form-check mb-0">
                                                    <input type="checkbox" class="form-check-input">
                                                </div>
                                            </td> --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-circle avatar-image" style="width: 38px; height: 38px;">
                                                    {{-- <img src="assets/images/avatars/thumb-1.jpg" alt=""> --}}
                                                    @if (!empty($empDetails->photo) && file_exists(base_path()."/public/".env('USER_PROFILE_PATH').$empDetails->photo))
                                                        <img src="{{env('USER_PROFILE_PIC').$empDetails->photo}}" alt="" />
                                                    @else
                                                        @if ($empDetails->salute == 1)
                                                            <img src="{{env('USER_PROFILE_PIC')}}male.png" alt="" />
                                                        @else
                                                            <img src="{{env('USER_PROFILE_PIC')}}female.png" alt="" />
                                                        @endif
                                                    @endif
                                                    </div>
                                                    <div class="ms-2">
                                                        <div class="text-dark fw-bold">{{ ucwords($empDetails->first_nm. " " . $empDetails->last_nm) }}</div>
                                                        <div class="text-muted">
                                                            @if (!empty($empDetails->company_email) && $empDetails->offinfo == '2')
                                                                {{ $empDetails->company_email }}
                                                            @elseif (!empty($empDetails->emailid))
                                                                {{ $empDetails->emailid }}
                                                            @else
                                                                {{ $empDetails->alternate_emailid }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span><a href="tel:+91{{ $empDetails->mobnum }}">+91-{{ $empDetails->mobnum }}</a></span>
                                            </td>
                                            <td>
                                                <span>{{ $user->designation_title }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center {{ $empDetails->offinfo == '2' ? 'text-success' : 'text-danger' }}">
                                                    {{-- <span class="badge-dot me-2 {{ $empDetails->offinfo == '2' ? 'bg-success' : 'bg-danger' }}"></span> --}}
                                                    <span class="text-capitalize">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" 
                                                                @if (auth('front')->user()->can('area-master-update')) onclick="javascript:changeStatus({{$empDetails->emp_id}})" 
                                                                @else disabled 
                                                                @endif 
                                                                @if ($empDetails->offinfo == 2) checked
                                                                @endif
                                                            />
                                                            <label class="form-check-label ms-2" for="flexSwitchCheckChecked"></label>
                                                        </div>
                                                        {{-- {{ $empDetails->offinfo == '2' ? "active" : "block" }} --}}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span>{{ date('dS M Y h:i:sA',strtotime($empDetails->loghistoryd . " " . $empDetails->loghistoryt)) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <a href="#" class="px-2" data-bs-toggle="dropdown">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a href="{{route('admin.system.viewUserProfile', ["emp_id" => base64_encode($empDetails->emp_id)])}}" class="dropdown-item">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="icon-eye feather"></i>
                                                                    <span class="ms-2">View Profile</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        {{-- <li>
                                                            <a href="javascript:void(0)" class="dropdown-item">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="icon-edit feather"></i>
                                                                    <span class="ms-2">Edit Profile</span>
                                                                </div>
                                                            </a>
                                                        </li> --}}
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function changeStatus(id) {
                if(id != '' && $.isNumeric(id)){
                    $.ajax({
                        url: "{{route('admin.system.users.toggleStatus')}}",
                        type: "POST",
                        dataType: "JSON",
                        data: {id: id},
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(response) {
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
                                    window.location.href = `{{route('admin.system.users')}}`;
                                }, pageReloadTimeOut);
                            }
                        },
                    });
                }
            }
        </script>
    @endpush
@endsection