@extends('user.layouts.master')

@section('main-section')
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<style>
    .error{
        color:red;
    }
    textarea#last_remark {
    width: 30%;
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
<div class="content" @if(Session::get("timer")) style="margin-left: 0% !important" @endif>
    <div class="main">
        <div class="page-header">
            <h4 class="page-title"></h4>
            <div class="breadcrumb">
                <span class="me-1 text-gray"><i class="feather icon-home"></i></span>
                <div class="breadcrumb-item"><a href="{{route(session()->get('load_dashboard').'.dashboard')}}"> Dashboard </a></div>
                <div class="breadcrumb-item"><a href="javascript:void(0)"> Leads </a></div>
                <div class="breadcrumb-item"><a href="{{route('crm.leads.view.allLeads')}}"> View all Leads </a></div>
                {{-- <div class="breadcrumb-item"><a href="{{route('crm.leads.view.feedbackCallView')}}"> Feedback Call View </a></div> --}}
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4>Seller Info</h4>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                View Contact Details
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="card">
                                    <div class="updateStatus"></div>
                                    <div class="card-body">
                                        <h4>Lead Information</h4>
                                        <form class="row g-3" id="leadInformationForm" name="leadInformationForm" novalidate autocomplete="off">
                                            @csrf
                                            <input type="hidden" id="tele_id" name="tele_id" value="{{$single_lead->id}}">
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
                                            <div>
                                                <p id="error_msg" style="color:red;float:left;"></p></br>
                                                <p id="succ_msg" style="color:green;float:left;"></p></br>
                                            </div>
                                            <div class="col-12">
                                                <button type="button" class="btn btn-primary mr-3" id="update_lead">Update</button>
                                                {{-- <a onclick="javascript: history.back()" class="btn btn-warning ml-2">Go Back</a> --}}
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

        <div class="card">
            <div class="card-body">
                <h4>Call</h4>
                <form class="row g-3" name="followup_form" id="followup_form" novalidate autocomplete="off"> 	
                    @csrf
                    <div class="control-group" >
                        <p>ReCall ?</p>
                        <p>    
                            <label class="radio-inline">
                                <input type="radio" name="is_recall" class="is_recall" id="is_recall" value="Y" /> Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_recall" class="is_recall" id="is_recall" value="N" /> No
                            </label>
                        </p>
                    </div>
                    <div class="col-md-3" id="feedback_form">
                        <p>
                            <strong><u>Feedback Form :</u></strong>
                        </p>
                        @php $i = 0;  @endphp
                        @foreach($feedback_data_notin as $fd)
                        @php ++$i; @endphp
                        <p>@php echo $i."."; @endphp &nbsp;&nbsp; {{ $fd->question }}</p>
                        <p>
                            @if($fd->question_type == "Yes/No")
                                <label class="radio-inline">
                                    <input type="radio" name="question_{{$fd->feedback_id}}" value="Y" /> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="question_{{$fd->feedback_id}}" value="N" /> No
                                </label>
                            @elseif($fd->question_type == "Rating")
                                <label class="radio-inline">
                                    <input type="radio" name="question_{{$fd->feedback_id}}" value="1"> 1
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="question_{{$fd->feedback_id}}" value="2"> 2
                                </label>
                                <label class="radio-inline">   
                                    <input type="radio" name="question_{{$fd->feedback_id}}" value="3"> 3
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="question_{{$fd->feedback_id}}" value="4"> 4
                                </label>
                                <label class="radio-inline">   
                                    <input type="radio" name="question_{{$fd->feedback_id}}" value="5"> 5
                                </label>
                            @endif
                            <textarea class="form-control" name="question_remark_{{$fd->feedback_id}}" rows="1" placeholder="Feedback query @php echo $i; @endphp"></textarea>
                        </p>
                        @endforeach


                        <div class="form-group">
                            <p>Seller Interested ?</p>
                            <p>    
                                <select name="selintrested" id="selintrested" class="form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                </select>
                            </p>
                        </div>	
                        <div class="form-group" id="new_recall_datetime">
                            <p>Appointment Date & Time:</p>
                            <p>
                                <input type="text" name="appointment_date" id="appointment" class="form-control" placeholder="Enter Date"  data-date-format="yyyy-mm-dd" required>
                                &nbsp;
                                <input type="text" name="appointment_time" id="appointment" class="form-control timepicker" placeholder="Time" required>
                                &nbsp;
                                <button type="button" class="btn btn-danger form-control" onclick="$('#appointment_date').val('');$('#appointment_time').val('');">Clear</button>
                            </p>
                        </div>

                    </div>

                    <div class="col-md-3 mt-3" id="recall_datetime">
                        <p>Recall Date & Time:</p>
                        <p>
                            <input type="text" name="recall_date" id="recall_date" class="form-control" placeholder="Enter Recall Date here"  data-date-format="yyyy-mm-dd" required>
                            &nbsp;
                            <input type="text" name="recall_time" id="start_time" class="form-control timepicker" placeholder="Enter Recall Time here" required>
                            &nbsp;
                            <button type="button" class="btn btn-danger m-3" onclick="$('#recall_date').val('');$('#start_time').val('');">Clear</button>
                        </p>
                    </div>

                    <div id="final_remark">
                        <textarea class="form-control" cols="20" rows="10" name="last_remark" id="last_remark" placeholder="Final Remark"></textarea>
                    </div>


                    @php $call_start_time = date('Y-m-d H:i:s'); @endphp

                    <input type="hidden" name="start_call_datetime" id="start_call_datetime" value="@php echo $call_start_time;  @endphp"/>
                    <input type="hidden" name="end_call_datetime" id="end_call_datetime" />
                    <input type="hidden" name="lead_area" id="lead_area" value="<?= $single_lead->area ?>" >
                    <input type="hidden" name="upload_lead_id" value="<?= $single_lead->id ?>" >

                    <div class="form-actions" align="center">
                        <p id="followup_error_msg" style="font-weight: bold;text-align: center;"></p>
                        <input type="button" name="btn_sav" id="btn_sav" class="btn btn-success" value="Save Feedback Details"/>
                        <input type="button" name="disconnect" id="disconnect" class="btn btn-success" value="Disconnect" />
                        <button type="reset" class="btn btn-info" id="reset-btn">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.js"></script>
    <script>
        $('.call_datepicker').datepicker();
        $("#appointment").datepicker();
        $('#appoinment_date').datepicker();
        $('#recall_date').datepicker();

        $('.timepicker').clockpicker({
            autoclose: true
        });

        $("#feedback_form").hide();
        $("#recall_datetime").hide();
        $("#final_remark").hide();
        $("#btn_sav").hide();


        $(document).on("click",".is_recall",function() {
            var is_recall = $(this).val();

            $("#feedback_form").hide();
            $("#recall_datetime").hide();
            $("#final_remark").hide();
            $("#btn_sav").hide();

            if(is_recall == 'Y'){
                $("#recall_datetime").show();
            } else {
                $("#feedback_form").show();
            }
        });

        $(document).on("click","#disconnect",function() {

            var dt = new Date();
            var DateTime = dt.getFullYear() + "-" + (dt.getMonth()+1) + "-" + dt.getDate() +" "+ dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
            $("#end_call_datetime").val(DateTime)
            $("#btn_sav").show();
            $("#disconnect").hide();
            $("#final_remark").show(); 
        });
        $(document).on("click","#btn_sav",function() {
            var form_data = $("#followup_form").serializeArray();
        });

        $(document).on("click","#reset-btn",function() {
            location.reload();
        });


        $(document).ready(function(){
            jQuery.validator.addMethod("phoneIND", function(contact_number, element) {
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
                        element.parent().append( error );
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
            $('#update_lead').on('click', function() {
                const valid = $('#leadInformationForm').valid();
                if(valid){

                    let fd = new FormData($('#leadInformationForm')[0]);
                    $.ajax({
                        url: "{{route('crm.leads.update.single')}}",
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
                                window.location.href = `{{route('crm.leads.view.allLeads')}}`;
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
                }
            });
        });


        $(document).ready(function(){
            $('#btn_sav').on('click', function() {
                let ff = new FormData($('#followup_form')[0]);
                $.ajax({
                    url: "{{route('crm.leads.storenotin.feedbacknotin')}}",
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
                            window.location.href = `{{route('crm.leads.view.allLeads')}}`;
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

        $("#selintrested").change(function(){
            var selintreset = $("#selintrested").val();
            if(selintreset == "1"){
                $("#new_recall_datetime").show();
            }else{
                $("#new_recall_datetime").hide();
            }
        });

    </script>
    @endpush
    @endsection