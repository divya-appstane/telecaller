@extends('user.layouts.master')

@section('main-section')
@push('css')
    <style>
        .error{
            color:red;
        }
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
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
               <div class="breadcrumb-item"><a href="{{route('marketing.leads.view.allLeads')}}"> View all Leads </a></div>
               <div class="breadcrumb-item"><a href="{{route('marketing.leads.view.single', ["id" => base64_encode($single_lead->id)])}}"> View Single Leads </a></div>
            </div>
        </div>
        <div class="card">
            <div class="updateStatus"></div>
            <div class="card-body">
                <h4>Lead Information</h4>
                <form class="row g-3" id="leadInformationForm" name="leadInformationForm" autocomplete="off" novalidate>
                    @csrf
                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Shop Name</label><span class="text-danger">&nbsp;*</span>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="{{$single_lead->company_name}}">
                    </div>
                    <div class="col-md-6">
                        <label for="contact_per_name" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contact_per_name" name="contact_per_name" value="{{$single_lead->contact_per_name}}">
                    </div>
                    <div class="col-md-6">
                        <label for="contact_number" class="form-label">Contact Number</label><span class="text-danger">&nbsp;*</span>
                        <input type="number" class="form-control" id="contact_number" name="contact_number" value="{{$single_lead->contact_number}}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{$single_lead->email}}">
                    </div>
                    <div class="col-md-3">
                        <label for="designation" class="form-label">Designation</label>
                        <input type="text" class="form-control" id="designation" name="designation" value="{{$single_lead->designation}}">
                    </div>
                    <div class="col-md-3">
                        <label for="address" class="form-label">Company Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{$single_lead->address}}">
                    </div>
                    <div class="col-md-2">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{$single_lead->city}}">
                    </div>
                    <div class="col-md-2">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state" value="{{$single_lead->state}}">
                    </div>
                    <div class="col-md-2">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" value="{{$single_lead->pincode}}">
                    </div>
                    <div class="col-12">
                        <label for="remarks" class="form-label">Remark</label>
                        <textarea class="form-control" id="remarks" name="remarks">{{$single_lead->remarks}}</textarea>
                    </div>
                    <div class="col-12">
                        <label for="offeredcategory" class="form-label">Offered Category</label> <span class="text-danger">&nbsp;*</span>
                        @if (!is_null($all_categories))
                            @foreach ($all_categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{$category->cid}}" id="cat_id[]" name="cat_id[]" {{ in_array($category->cid,array_column($single_lead->getLeadCategory->toArray(), 'cat_id')) ? 'checked' : ''}}>
                                    <label class="form-check-label ms-2" for="cat_id">
                                        {{$category->catname}}
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="gst_number" class="form-label">GST Number</label>
                        <input type="text" class="form-control" id="gst_number" name="gst_number" value="{{$single_lead->gst_number}}" placeholder="Enter your GST number">
                    </div>
                    <input type="hidden" name="lid" id="lid" value="{{$single_lead->id}}" />
                    <div class="col-md-6">
                        <label for="area" class="form-label">Area</label><span class="text-danger">&nbsp;*</span>
                        <select name="area" id="area" class="form-select">
                            <option value="">Please select Area</option>
                            @if (!is_null($all_areas))
                                @foreach ($all_areas as $area)
                                    <option value="{{$area->area_name}}" {{$area->area_name == $single_lead->area ? 'selected' : ''}}>{{$area->area_name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-primary mr-3" id="update_lead">Save</button>
                        <a onclick="javascript: history.back()" class="btn btn-warning ml-2">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- <script src="{{env('USER_ASSETS')}}vendors/datatables/jquery.dataTables.min.js"></script> --}}
        {{-- <script src="{{env('USER_ASSETS')}}vendors/datatables/dataTables.bootstrap.min.js"></script> --}}
        <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
        <script>
            $(document).ready(function(){
                jQuery.validator.addMethod("phoneIND", function(contact_number, element) {
                    // console.log(contact_number);
                    contact_number = contact_number.replace(/\s+/g, "");
                    return /^[6-9][0-9]{9}$/.test(contact_number);
                }, "Please specify a valid phone number");
                $('#leadInformationForm').validate({
                    ignore: ".ignore",
                    debug: false,
                    errorElement: 'span',
                    rules: {
                        company_name: {
                            required: {
                                depends:function(){
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                            },
                            // minlength: 8,
                        },
                        contact_number: {
                            required: {
                                depends:function(){
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                            },
                            phoneIND: true,
                            minlength: 10,
                            maxlength: 10,
                        },
                        'cat_id[]': {
                            required: true,
                        },
                        area: {
                            required: true,
                        },
                        pincode : {
                            digits: true,
                            minlength: 6,
                            maxlength: 6,
                        },
                        email: {
                            email: true,
                        }
                    },
                    messages: {
                        company_name: "Company name cannot be empty.",
                        contact_number: {
                            required: "Contact number field is required.",
                            phoneIND: "Please enter valid mobile number.",
                            minlength: "Contact numbers can only be {0} digit long.",
                            maxlength: "Contact numbers can only be {0} digit long.",
                        },
                        'cat_id[]': {
                            required: "You must select at least 1 category.",
                        },
                        area: {
                            required: "You must select an area.",
                        },
                        pincode : {
                            digits: "Your Pincode must be numbers!",
                            minlength: "Your Pincode must be 6 numbers!",
                            maxlength: "Your Pincode must be 6 numbers!",
                        },
                        email: {
                            email: "Please enter a valid email address.",
                        }
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    errorPlacement: function(error, element) {
                        if(element.attr("name") == "cat_id[]") {
                            // console.log(element);
                            element.parent().append( error );
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
                $('#update_lead').on('click', function() {
                    // alert('clicked')
                    const valid = $('#leadInformationForm').valid();
                    if(valid){
                        // console.log('valid');
                        let fd = new FormData($('#leadInformationForm')[0]);
                        $.ajax({
                            url: "{{route('marketing.leads.update.single')}}",
                            type: 'POST',
                            data: fd,
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
                                    window.location.href = `{{route('marketing.leads.view.allLeads')}}`;
                                }, pageReloadTimeOut);
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
    @endpush
@endsection