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
                    <div class="breadcrumb-item"><a href="javascript:void(0)"> Leads </a></div>
                    <div class="breadcrumb-item"><a href="javascript:void(0)"> Add Bulk Leads </a></div>
                </div>
            <div class="form-group ms-auto"><a href="{{route('telecaller.leads.add.sigleLeadForm')}}" class="btn btn-success">Add Single Leads</a></div>
        </div>
        <div class="card">
            <div class="updateStatus"></div>
            <div class="card-body">
                <h4>Upload Bulk Leads (CSV)</h4><br/>
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
                <form class="row" id="addBulkLeadForm" name="addBulkLeadForm" novalidate enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col-md-3">
                            <label for="upload_lead_file" class="form-label">Upload CSV file<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-upload feather"></i></span>
                                <input type="file" class="form-control" id="upload_lead_file" name="upload_lead_file" placeholder="Please select csv file for lead upload" />
                            </div>
                        </div>
                        <div>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-download feather"></i></span>
                                <a class="btn btn-danger" download="Leads_data" href="{{ env('USER_ASSETS') }}/samples/Leads_data.csv" title="Leads_data"></i>Download Sample</a>
                            </div>
                        </div>
                    </div>
                    <div class="error mb-2"></div>
                    
                    <div class="col-12">
                        <button type="button" class="btn btn-primary mr-3" id="update_lead">Save</button>
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

                // jQuery(function ($) {
                $('#addBulkLeadForm').validate({
                    rules: {
                        upload_lead_file: {
                            required: true,
                            extension: "csv",
                            filesize: 2,
                        },
                    },
                    messages: {
                        upload_lead_file: {
                            required: "Please select the file to be uploaded",
                            extension: "Only csv files supported",
                        },
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    errorPlacement: function(error, element) {
                        if(element.attr("name") == "upload_lead_file") {
                            $('.error').html(error);
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
                // });

                $('#update_lead').on('click', function() {
                    const valid = $('#addBulkLeadForm').valid();

                    if(valid){
                        var fd = new FormData($('#addBulkLeadForm')[0]);
                        // fd.append('upload_lead_file', $('#upload_lead_file').files);

                        $.ajax({
                            url:"{{route('telecaller.leads.store.bulkLead')}}",  
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
                                        window.location.href = `{{route('telecaller.leads.view.allLeads')}}`;
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