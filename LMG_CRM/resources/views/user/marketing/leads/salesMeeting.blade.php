@extends('user.layouts.master')

@section('main-section')
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
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
               <div class="breadcrumb-item"><a href="{{route('marketing.leads.view.single', ["id" => base64_encode($single_lead->id)])}}"> Sales Meeting Updates </a></div>
            </div>
        </div>
        <div class="card">
            <div class="updateStatus"></div>
            <div class="card-body">
                <h4>Lead Information</h4>
                <form class="row g-3" id="leadInformationForm" name="leadInformationForm" novalidate enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="col-md-6">
                        <label for="sellername" class="form-label">Shop Name</label><span class="text-danger">&nbsp;*</span>
                        <input type="text" class="form-control {{$errors->has('sellername') ? 'is-invalid' : '' }}" id="sellername" name="sellername" value="{{old('sellername') ?? $single_lead->company_name}}" />
                    </div>
                    @error('sellername')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div class="col-md-6">
                        <label for="contperson" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contperson" name="contperson" value="{{old('contperson') ?? $single_lead->contact_per_name}}">
                    </div>
                    <div class="col-md-6">
                        <label for="contnum" class="form-label">Contact Number</label><span class="text-danger">&nbsp;*</span>
                        <input type="number" class="form-control" id="contnum" name="contnum" value="{{old('contnum') ?? $single_lead->contact_number}}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{old('email') ?? $single_lead->email}}">
                    </div>
                    <div class="col-md-3">
                        <label for="designation" class="form-label">Designation</label>
                        <input type="text" class="form-control" id="designation" name="designation" value="{{old('designation') ?? $single_lead->designation}}">
                    </div>
                    <div class="col-md-3">
                        <label for="address" class="form-label">Company Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{old('address') ?? $single_lead->address}}">
                    </div>
                    <div class="col-md-2">
                        <label for="city" class="form-label">City</label><span class="text-danger">&nbsp;*</span>
                        <input type="text" class="form-control" id="city" name="city" value="{{old('city') ?? $single_lead->city}}">
                    </div>
                    <div class="col-md-2">
                        <label for="state" class="form-label">State</label><span class="text-danger">&nbsp;*</span>
                        <input type="text" class="form-control" id="state" name="state" value="{{old('state') ?? $single_lead->state}}">
                    </div>
                    <div class="col-md-2">
                        <label for="pincode" class="form-label">Pincode</label><span class="text-danger">&nbsp;*</span>
                        <input type="text" class="form-control" id="pincode" name="pincode" value="{{old('pincode') ?? $single_lead->pincode}}">
                    </div>
                    <div class="col-md-12">
                        <label for="remarks" class="form-label">Remark</label>
                        <textarea class="form-control" id="remarks" name="remarks">{{old('remarks') ?? $single_lead->remarks}}</textarea>
                    </div>
                    <div class="col-md-8">
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
                    <div class="col-md-4">
                        <label for="subcategory" class="form-label">Sub-Category:</label><span class="text-danger">&nbsp;*</span>
                        <input type="text" class="form-control" id="subcategory" name="subcategory" value="{{old('subcategory')}}" placeholder="Enter seller sub-category (comma seperated values)">
                    </div>
                    <div class="col-md-6">
                        <label for="mapholder" class="form-label">Location Map</label>
                        <div id="mapholder"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control" id="latitude" name="latitude" value="" placeholder="Latitude value we display here automatically" readonly/>
                    </div>
                    <div class="col-md-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="longitude" name="longitude" value="" placeholder="Longitude value we display here automatically" readonly/>
                    </div>
                    <div class="col-md-6">
                        <label for="gst_number" class="form-label">GST Number</label>
                        <input type="text" class="form-control" id="gst_number" name="gst_number" value="{{old('gst_number') ?? $single_lead->gst_number}}" placeholder="Enter seller GST number">
                    </div>
                    <div class="col-md-6">
                        {{-- <label for="area" class="form-label">Area</label><span class="text-danger">&nbsp;*</span>
                        <select name="area" id="area" class="form-select">
                            <option value="">Please select Area</option>
                            @if (!is_null($all_areas))
                                @foreach ($all_areas as $area)
                                    <option value="{{$area->area_name}}" {{$area->area_name == $single_lead->area ? 'selected' : ''}}>{{$area->area_name}}</option>
                                @endforeach
                            @endif
                        </select> --}}
                    </div>
                    <div class="col-md-6">
                        <label for="photo" class="form-label">Shop Photo<span class="text-danger">&nbsp;*</span></label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"><i class="icon-upload feather"></i></span>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" capture="camera"/>
                        </div>
                        <div class="error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="selreg" class="form-label">Seller Register</label><span class="text-danger">&nbsp;*</span>
                        <select name="selreg" id="selreg" class="form-select">
                            <option value="">Please select status</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-none" id="notregreason">
                        <label class="form-label">Reason:</label>
                        <select name="selreason" id="selreason" class="form-select">
                            <option value="" selected>Please select reason</option>
                            <option value="newfollowup">New Followup</option>
                            <option value="notintrested">Not Interested</option>
                        </select>
                    </div>
                    
                    <div class="col-md-8 d-none" id="selappdate">
                        <label class="form-label">Appointment date & time:</label>
                        <div class="row">
                            <input type="text" name="sappoinment_date" id="sappoinment_date" value="" placeholder="Enter Appointment Date"  data-date-format="yyyy-mm-dd" class="datepicker col-md-4 form-control" autocomplete="randomFeed"/>
                            &nbsp;
                            <input type="text" name="sappoinment_time" id="sappoinment_time" value="" class="timepicker col-md-4 form-control" placeholder="Enter Appointment Time" autocomplete="randomFeed"/>
                            <button type="button" class="btn btn-danger col-md-4 mt-2" onclick="$('#sappoinment_date').val('');$('#sappoinment_time').val('');" >Clear</button>
                        </div>
                    </div>
                    
                    <div class="col-md-8 d-none" id="notregreasondata">
                        <label class="form-label">Not Interested Reason:</label>
                        <div class="controls">
                            <textarea name="reasondata" id="reasondata" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="button" class="btn btn-primary mr-3" id="update_sales_meeting">Save</button>
                        <a onclick="javascript: history.back()" class="btn btn-warning ml-2">Go Back</a>
                        <input type="hidden" name="lid" id="lid" value="{{$single_lead->id}}" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
        <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/additional-methods.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.js"></script>
        <script src="https://maps.google.com/maps/api/js?sensor=false&key=AIzaSyBvoVH5U_FMwQ5orX8H8fxm7Krkle-fqO0"></script>
        <script>
            var x = document.getElementById("updateStatus");
            var y = "dummy";
            var z = "data";
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                } else { 
                    x.innerHTML = "Geolocation is not supported by this browser.";
                }
            }

            function showPosition(position) {
                lat = position.coords.latitude;
                lon = position.coords.longitude;
                latlon = new google.maps.LatLng(lat, lon)
                mapholder = document.getElementById('mapholder')
                mapholder.style.height = '200px';
                mapholder.style.width = '350px';
                var myOptions = {
                    center:latlon,zoom:14,
                    mapTypeId:google.maps.MapTypeId.ROADMAP,
                    mapTypeControl:false,
                    navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL}
                }
                var map = new google.maps.Map(document.getElementById("mapholder"), myOptions);
                var marker = new google.maps.Marker({position:latlon,map:map,title:"You are here!"});
                y = position.coords.latitude;
                z = position.coords.longitude;
                document.getElementById('latitude').value = y;
                document.getElementById('longitude').value = z;
            }

            $(document).ready(function(){
                $("#photo").change(function(){
                    getLocation();
                });

                jQuery.validator.addMethod("phoneIND", function(contact_number, element) {
                    // console.log(contact_number);
                    contact_number = contact_number.replace(/\s+/g, "");
                    return /^[6-9][0-9]{9}$/.test(contact_number);
                }, "Please specify a valid phone number");
                $.validator.addMethod('filesize', function (value, element, limit) {
                    limit = limit * 1024 * 1024;
                    return !element.files[0] || (element.files[0].size <= limit);
                }, 'File size must be less than {0} MB');

                $('#leadInformationForm').validate({
                    ignore: ".ignore",
                    debug: false,
                    errorElement: 'span',
                    rules: {
                        sellername: {
                            required: {
                                depends:function(){
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                            },
                            // minlength: 8,
                        },
                        contnum: {
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
                        email: {
                            email: true,
                        },
                        photo: {
                            required: true,
                            filesize: 2,
                            extension: "jpg,jpeg,png",
                        },
                        selreg: {
                            required: true,
                        },
                        city: {
                            required: true,
                        },
                        state: {
                            required: true,
                        },
                        pincode: {
                            required: true,
                            digits: true,
                            minlength: 6,
                            maxlength: 6,
                        },
                        subcategory: {
                            required: true,
                        },
                        selreason: {
                            required: function (element) {
                                return $('#selreg').val() != 1
                            }
                        },
                        sappoinment_date: {
                            required: function (element) {
                                return $('#selreason').val() == 'newfollowup'
                            }
                        },
                        sappoinment_time: {
                            required: function (element) {
                                return $('#selreason').val() == 'newfollowup'
                            }
                        },
                        reasondata: {
                            required: function (element) {
                                return $('#selreason').val() == 'notintrested'
                            }
                        }
                    },
                    messages: {
                        sellername: "Company name cannot be empty.",
                        contnum: {
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
                        email: {
                            email: "Please enter a valid email address.",
                        },
                        photo: {
                            required: "Please upload the shop photo",
                            filesize: "File size must be less than {0} MB",
                            extension: "Only jpeg/png files supported",
                        },
                        selreg: "Please select seller registration status",
                        city: "City field is required.",
                        state: "State field is required.",
                        pincode: {
                            required: "Pincode must be required",
                            digits: "Your Pincode must be numbers!",
                            minlength: "Your Pincode must be 6 numbers!",
                            maxlength: "Your Pincode must be 6 numbers!",
                        },
                        subcategory: "Subcategory name cannot be empty.",
                        selreason: "Please select reason for why seller is not registering",
                        sappoinment_date: "Please select new appointment date",
                        sappoinment_time: "Please select new appointment time",
                        reasondata: "Please explain the reason for seller not registering with LMG in a brief",
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
                        } else if(element.attr("name") == "photo") {
                            $('#photo').parent().parent().find('.error').html( error );
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
                let start = new Date();
                let end = new Date(new Date().setMonth(start.getMonth()+1))

                $('#sappoinment_date').datepicker({
                    autoHide: true,
                    inline: true,
                    format: 'yyyy-mm-dd',
                    startDate: start,
                    endDate: end
                });

                $('.timepicker').clockpicker({
                    autoclose: true
                });

                $( "#selreg" ).change(function() {
                    var selreg = $("#selreg").val();
                    if(selreg == 0){
                        $("#notregreason").removeClass('d-none');
                    }else{
                        $("#notregreason").addClass('d-none');
                        $("#selappdate").addClass('d-none');
                        $("#notregreasondata").addClass('d-none');
                    } 
                });
                $( "#appoitmentstatus" ).change(function() {
                    var appoitmentstatus = $("#appoitmentstatus").val();
                    if(appoitmentstatus == "new"){
                        $("#appdate").removeClass('d-none');
                        $("#recalldate").addClass('d-none');
                    }else if(appoitmentstatus == 'cancel'){
                        $("#recalldate").removeClass('d-none');
                        $("#appdate").addClass('d-none');
                    }else if(appoitmentstatus == ''){
                        $("#recalldate").addClass('d-none');
                        $("#appdate").addClass('d-none');
                    }     
                });

                $( "#selreason" ).change(function() {
                    var selreason = $("#selreason").val();
                
                    if(selreason == "newfollowup"){
                        $("#selappdate").removeClass('d-none');
                        $("#notregreasondata").addClass('d-none');
                    
                    }else if(selreason == "notintrested"){
                    
                        $("#selappdate").addClass('d-none');
                        $("#notregreasondata").removeClass('d-none');
                    }else if(selreason == ''){
                        $("#selappdate").addClass('d-none');
                        $("#notregreasondata").addClass('d-none');
                    }    
                });

                $('#update_sales_meeting').on('click', function() {
                    // alert('clicked'); 
                    $('.error').removeClass('is-invalid');
                    $('.error').html( '' );
                    const valid = $('#leadInformationForm').valid();
                    if(valid){
                        // console.log('valid');
                        let fd = new FormData($('#leadInformationForm')[0]);
                        $.ajax({
                            url: "{{route('marketing.leads.update.salesMeeting')}}",
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
                                    if( data.status === 500 ) {
                                        var errors = $.parseJSON(data.responseText);
                                        console.log(errors);
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