@extends('user.layouts.master')

@section('main-section')
    @push('css')
        <link href="{{env('USER_ASSETS')}}vendors/datatables/dataTables.bootstrap.min.css" rel="stylesheet">
    @endpush
    <div class="content">
        <div class="main">
            <div class="page-header">
                <h4 class="page-title"></h4>
                <div class="breadcrumb">
                    <span class="me-1 text-gray"><i class="feather icon-home"></i></span>
                    <div class="breadcrumb-item"><a href="{{route(session()->get('load_dashboard').'.dashboard')}}"> Dashboard </a></div>
                    <div class="breadcrumb-item"><a href="{{route('admin.role-permission.view.allRole')}}"> Role </a></div>
                    <div class="breadcrumb-item"><a href="{{route('admin.role-permission.view.allRole')}}"> View all Role</a></div>
                    <div class="breadcrumb-item"><a href="{{route('admin.role-permission.view.allPermission', ['id' => base64_encode($role->designation_id)])}}"> View Permissions</a></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4>{{$role->designation_title}} Permissions</h4>
                    {{-- <p>DataTables is a plug-in for the jQuery Javascript library. It is a highly flexible tool, built upon the foundations of progressive enhancement, that adds all of these advanced features to any HTML table. Below is an example of zero configuration.</p> --}}
                    <div class="mt-4">
                        <div class="table-responsive">
                            <form name="permissionForm" id="permissionForm" novalidate autocomplete="off"> 
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Module</th>

                                            @foreach($all_actions as $action)
                                                <th scope="col">{{$action->actions}}</th>
                                            @endforeach
                                            
                                        </tr>
                                    </thead>
                                    <tbody>

                                        {{-- @foreach($all_module as $module)
                                            <tr>
                                                <td>{{$module->name}}</td>
                                                    @foreach($all_permissions as $permissions)
                                                    <td>
                                                        @foreach($permissions->permissionActions as $permaction)
                                                            @foreach($module->modulePermissions as $modpermission)
                                                                @if($modpermission->permission_id == $permaction->id  && $permissions->id == $permaction->action_id)
                                                                    <input type="checkbox">{{$permaction->name}}
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    </td>
                                                @endforeach
                                            <tr>
                                        @endforeach --}}
                                        <input type="hidden" value="{{$role->designation_id}}" name="designation_designation_id" id="designation_designation_id">

                                        @foreach($all_module as $module)
                                            <tr>
                                                <th>{{$module->name}}</th>
                                                @foreach($all_permissions as $permission)
                                                    <td>
                                                        @foreach($module->modulePermissions as $modpermission)
                                                            @if($modpermission->module_id == $module->id && $permission->id == $modpermission->action_id)
                                                            
                                                                    <input type="checkbox" value="{{$modpermission->id}}" name="permission_id[]" id="permission_id" 
                                                                        @foreach($chkpermission as $permissionss)
                                                                            @if($permissionss->designation_designation_id == $role->designation_id && $modpermission->id ==$permissionss->permission_id)
                                                                                checked
                                                                            @endif
                                                                        @endforeach
                                                                    >

                                                                    {{-- @foreach($chkpermission as $permissionss)
                                                                        @if($permissionss->designation_designation_id == $role->designation_id && $modpermission->id ==$permissionss->permission_id)
                                                                        <input type="hidden" value="{{$permissionss->id}}" name="rolepermissionid[]" id="rolepermissionid">
                                                                        @endif
                                                                    @endforeach  --}}
                                                               
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                      
                                    </tbody>
                                </table>
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary mr-3" id="update_permission">Update</button>
                                    <a onclick="javascript: history.back()" class="btn btn-warning ml-2">Go Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @push('scripts')
        <script src="{{env('USER_ASSETS')}}vendors/datatables/jquery.dataTables.min.js"></script>
        <script src="{{env('USER_ASSETS')}}vendors/datatables/dataTables.bootstrap.min.js"></script>

        <script>
             $(document).ready(function(){
                $('#update_permission').on('click', function() {
                    let ff = new FormData($('#permissionForm')[0]);
                    $.ajax({
                        url: "{{route('admin.role-permission.store.storePermission')}}",
                        type: 'POST',
                        data: ff,
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType:"json",
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

                            setTimeout(() => {
                                window.location.href = `{{route('admin.role-permission.view.allRole')}}`;
                            }, pageReloadTimeOut);
                        },
                        error: function(data){
                            if( data.status === 422 ) {
                                var errors = $.parseJSON(data.responseText);
                                $.each(errors, function (key, value) {
                                    $('.err').removeClass("d-none");

                                    if($.isPlainObject(value)) {
                                        $.each(value, function (key, value) {                       
                                            $(key).addClass('is-invalid');
                                            $('.err').show().append(value+"<br/>");
                                        });
                                    }
                                });
                            }
                        } 
                    });
                });
            });
        </script>
    @endpush
@endsection