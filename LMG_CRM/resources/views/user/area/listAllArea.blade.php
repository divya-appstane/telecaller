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
                   {{-- <div class="breadcrumb-item"><a href="{{route('dashboard')}}"> Dashboard </a></div> --}}
                   <div class="breadcrumb-item"><a href="javascript:void(0)"> Area </a></div>
                   <div class="breadcrumb-item"><a href="{{route('area.viewArea')}}"> View all Area </a></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4>All Area</h4>
                    {{-- <p>DataTables is a plug-in for the jQuery Javascript library. It is a highly flexible tool, built upon the foundations of progressive enhancement, that adds all of these advanced features to any HTML table. Below is an example of zero configuration.</p> --}}
                    @csrf
                    <div class="mt-4">
                        <table id="data-table" class="table data-table">
                            <thead>
                                <tr>
                                    <th>Area Name</th>
                                    {{-- <th>Surrounding Area</th> --}}
                                    <th>Is Active?</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!is_null($all_area))
                                    @foreach ($all_area as $area)
                                        <tr>
                                            <td>{{$area->area_name}}</td>
                                            <td>
                                               
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" 
                                                        @if (auth('front')->user()->can('area-master-update')) onclick="javascript:changeStatus({{$area->id}})" 
                                                        @else disabled 
                                                        @endif 
                                                        @if ($area->isactive == 1) checked
                                                        @endif
                                                    />
                                                    <label class="form-check-label ms-2" for="flexSwitchCheckChecked"></label>
                                                </div>
                                                
                                                {{-- <div class="form-check form-switch">
                                                    @if ($area->isactive == 1)
                                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
                                                    @else
                                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked">
                                                    @endif
                                                    <label class="form-check-label ms-2" for="flexSwitchCheckChecked"></label>
                                                </div> --}}
                                               
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <a href="#" class="px-2" data-bs-toggle="dropdown">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if (auth('front')->user()->can('area-master-update'))
                                                            <li>
                                                                <a href="{{route('area.editArea', ['id' => base64_encode($area->id)])}}" class="dropdown-item">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="icon-edit feather"></i>
                                                                        <span class="ms-2">Edit Area</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (auth('front')->user()->can('area-master-delete'))
                                                            <li>
                                                                <a  href="#" data-action="{{route('area.deleteArea', ['id' => base64_encode($area->id)])}}" data-id="{{$area->id}}" data class="dropdown-item deletearea">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="icon-trash feather"></i>
                                                                        <span class="ms-2">Delete Area</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @endif
                                                         <li>
                                                            {{-- <a href="{{route('area.areawiseBDF', ['id' => base64_encode($area->id)])}}" class="dropdown-item" > --}}
                                                            <a href="#" data-id="{{$area->id}}" data-bs-toggle="modal" data-bs-target="#exampleModal" class="areawisebdf">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="icon-eye feather"></i>
                                                                    <span class="ms-2">Areawise BDFs</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>                                
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <tr>
                                        <th>Area Name</th>
                                         {{-- <th>Surrounding Area</th> --}}
                                        <th>Is Active?</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Area Wise BDF's</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               {{-- @foreach ($areadata as $ad)
                   <b>{{$ad->agentcode}}</b>
               @endforeach --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            </div>
        </div>
        </div>

    @push('scripts')
        <script src="{{env('USER_ASSETS')}}vendors/datatables/jquery.dataTables.min.js"></script>
        <script src="{{env('USER_ASSETS')}}vendors/datatables/dataTables.bootstrap.min.js"></script>
        <script>
            $('.data-table').DataTable({
                // 'columnDefs': [
                //     { 'orderable': false, 'targets': 0 }
                // ],
                // order: [[0, 'asc']],
            });
            function changeStatus(id) {
                $.ajax({
                    url: "{{route('area.changeStatus')}}",
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
                            // setTimeout(() => {
                            //     window.location.href = `{{route('area.viewArea')}}`;
                            // }, pageReloadTimeOut);
                        }
                    },
                });
                return false;
            }

            $(document).ready(function(){            
                $('.areawisebdf').click(function () {
                    var area_id = $(this).data('id');
                    $.ajax({
                        type: "POST",
                        dataType: "html",
                        url: "{{route('area.areawiseBDF')}}",
                        data: {'area_id': area_id},
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function (response) {
                            // console.log(response);
                            $(".modal-body").html(response);
                        }
                    });
                });
            });

            $(document).ready(function(){            
                $('.deletearea').click(function (e) {
                    var area_id = $(this).data('id');
                        Swal.fire({
                        icon: 'warning',
                        title: 'Are you sure you want to delete this record?',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{route('area.deleteArea')}}",
                            data: {
                                id:area_id
                            },
                            success: function (response, textStatus, xhr) {
                            Swal.fire({
                                icon: response.status,
                                title: response.status,
                                text: response.message,
                                showConfirmButton: false,
                                showCancelButton: false,
                                showCloseButton: false,
                                timer: swalModelTimeOut

                            }).then((result) => {
                                setTimeout(() => {
                                    window.location.href = `{{route('area.viewArea')}}`;
                                }, pageReloadTimeOut);
                            });
                            }
                        });
                        }
                    });
                }); 
            });
        </script>
    @endpush
@endsection