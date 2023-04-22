@extends('user.layouts.master')

@section('main-section')
@push('css')
<style>
    .error{
        color:red;
    }
</style>
@endpush

<div class="content">
    <div class="main">
        <div class="page-header">
            <h4 class="page-title"></h4>
            <div class="breadcrumb">
               <span class="me-1 text-gray"><i class="feather icon-home"></i></span>
               <div class="breadcrumb-item"><a href="{{route(session()->get('load_dashboard').'.dashboard')}}"> Dashboard </a></div>
               <div class="breadcrumb-item"><a href="javascript:void(0)"> Area </a></div>
               <div class="breadcrumb-item"><a href="{{route('area.addArea')}}"> Add new area </a></div>
            </div>
        </div>
        <div class="card">
            <div class="updateStatus"></div>
            <div class="card-body">
                <h4>Add Area</h4><br/>
                @if (session()->has('failures'))

                    <table class="table table-danger">
                        <tr>
                            <th>Row</th>
                            <th>Attribute</th>
                            <th>Errors</th>
                            <th>Value</th>
                        </tr>

                        @foreach (session()->get('failures') as $validation)
                            <tr>
                                <td>{{ $validation->row() }}</td>
                                <td>{{ $validation->attribute() }}</td>
                                <td>
                                    <ul>
                                        @foreach ($validation->errors() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    {{ $validation->values()[$validation->attribute()] }}
                                </td>
                            </tr>
                        @endforeach
                    </table>

                @endif
                <form  id="addAreaForm" name="addAreaForm" novalidate enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col-md-3">
                            <label for="area_name" class="form-label">Area Name<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-map-pin feather"></i></span>
                                <input type="text" class="form-control" id="area_name" name="area_name" placeholder="Enter area name" />
                            </div>
                        </div>
                    </div>
                    <span class="area_name error mb-2"></span>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col-md-3">
                            <label for="surrounding_area_id" class="form-label">Surrounding Area<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                @php $cnt = 0; @endphp
                                @foreach($all_areas as $area)
                                    @php $cnt++; @endphp
                                    <input type="checkbox" name="surrounding_area_id[]" id="surrounding_area_id[]" value="{{$area->id}}"> &nbsp;{{$area->area_name}} &nbsp;&nbsp;
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="isactive" class="form-label">Active<span class="text-danger">&nbsp;*</span></label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"><i class="icon-clock feather"></i></span>
                            <select class="form-control" id="isactive" name="isactive">
                                <option value="">--SELECT--</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <span class="isactive error mb-2"></span>
                    
                    <div class="col-12 mt-2">
                        <button type="button" class="btn btn-primary mr-3" id="add_new_area">Save</button>
                        <a onclick="javascript: history.back()" class="btn btn-warning ml-2">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
        <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/additional-methods.min.js"></script>
        <script>
            $(document).ready(function(){
                $.validator.addMethod('filesize', function (value, element, limit) {
                    limit = limit * 1024 * 1024;
                    return !element.files[0] || (element.files[0].size <= limit);
                }, 'File size must be less than {0} MB');

                $('#addAreaForm').validate({
                    ignore: "ignore",
                    rules: {
                        area_name: {
                            required: true,
                        },
                        isactive:{
                            required: true,
                        },
                    },
                    messages: {
                        area_name: {
                            required: "Area name is required."
                        },
                        isactive: {
                            required: "Please select atleast one status.",
                        },
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    errorPlacement: function(error, element) {
                        if(element.attr("name") == "area_name") {
                            $('.area_name').html(error);
                        } else if(element.attr("name") == "isactive") {
                            $('.isactive').html(error);
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });

                $('#add_new_area').on('click', function() {
                    const valid = $('#addAreaForm').valid();

                    if(valid){
                        var fd = new FormData($('#addAreaForm')[0]);

                        $.ajax({
                            url:"{{route('area.storeArea')}}",  
                            method:"POST",  
                            data:fd,  
                            contentType:false, 
                            processData:false,  
                            cache: false,
                            success: function(response){
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
                                    window.location.href = `{{route('area.viewArea')}}`;
                                }, pageReloadTimeOut);
                            },
                            error: function(errors) {
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
                            },
                        });
                    }
                });
            });

        </script>
    @endpush
@endsection