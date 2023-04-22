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
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4>All Roles </h4>
                    {{-- <p>DataTables is a plug-in for the jQuery Javascript library. It is a highly flexible tool, built upon the foundations of progressive enhancement, that adds all of these advanced features to any HTML table. Below is an example of zero configuration.</p> --}}
                    <div class="mt-4">
                        <table id="data-table" class="table data-table">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Slug</th>
                                    <th class="text-center">View Permissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!is_null($all_designations))
                                    @foreach ($all_designations as $alldes)
                                        <tr>
                                            <td>{{$alldes->designation_title}}</td>
                                            <td>{{$alldes->slug}}</td>
                                            <td class="text-center">
                                                <a href="{{route('admin.role-permission.view.allPermission', ['id' => base64_encode($alldes->designation_id)])}}">
                                                    <button class="btn btn-success me-2">Permissions</button>
                                                </a>
                                            </td>
                                        </tr>                                
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Role</th>
                                    <th>Slug</th>
                                    <th class="text-center">Follow-up</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @push('scripts')
        <script src="{{env('USER_ASSETS')}}vendors/datatables/jquery.dataTables.min.js"></script>
        <script src="{{env('USER_ASSETS')}}vendors/datatables/dataTables.bootstrap.min.js"></script>
    @endpush
@endsection