@extends('user.layouts.master')

@section('main-section')
    @push('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

        <link href="{{env('USER_ASSETS')}}css/style.css" rel="stylesheet">
        <style>
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
    <div class="content"  @if(Session::get("timer")) style="margin-left: 0% !important" @endif>
        <div class="main">
            <div class="page-header">
                <h4 class="page-title"></h4>
                <div class="breadcrumb">
                   <span class="me-1 text-gray"><i class="feather icon-home"></i></span>
                   <div class="breadcrumb-item"><a href="{{route(session()->get('load_dashboard').'.dashboard')}}"> Dashboard </a></div>
                   <div class="breadcrumb-item"><a href="javascript:void(0)"> Leads </a></div>
                   <div class="breadcrumb-item"><a href="{{route('telecaller.leads.view.allLeads')}}"> View all Leads </a></div>
                   <div class="breadcrumb-item"><a href="{{route('telecaller.leads.view.leadCalledEngagementView', ['id' => base64_encode($single_lead->id)])}}"> Lead Call Engagement </a></div>
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
                    <h4>Call Engagement</h4>
                    <form class="row g-3" name="followup_form" id="followup_form" novalidate autocomplete="off"> 	
                        @csrf
                        <div class="control-group" >
                            <p>नमस्ते</p>
                            <p>@if(date("H") < "12"){{ "शुभ प्रभात" }} @elseif (date("H") >= "12" && date("H") < "17" ) {{ "Good Afternoon" }} @else {{ "Good Evening" }} @endif सर / मैम</p>
                            @if ($single_lead->contact_per_name != "")
                                <p>क्या मेरी बात <strong>{{ $single_lead->company_name; }}</strong> से <strong>{{ $single_lead->contact_per_name; }}</strong> जी से हो रही है?</p>
                            @else
                                <p>क्या आप <strong>{{ $single_lead->company_name; }}</strong> से बात कर रहे हैं?</p>
                            @endif
                            <p>    
                                <label class="radio-inline">
                                    <input type="radio" name="first_conf" class="first_conf" value="Y" {{$followup_lead_data->is_person == 'Y' ? 'checked' : ''}} /> हाँ
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="first_conf" class="first_conf" value="N" {{$followup_lead_data->is_person == 'N' ? 'checked' : ''}} /> नहीं
                                </label>
                            </p>
                            <div id="first_no" class="col-md-3">
                                <label for="first_no_reason" class="form-label"><em>नहीं क्लिक करने का कारण चुनें</em></label>
                                <select class="form-control" name="first_no_reason" id="first_no_reason">
                                    <option value="" {{ $followup_lead_data->first_no_reason == "" ? "selected" : "" }}>Select Reason</option>
                                    <option value="Recall" {{ $followup_lead_data->first_no_reason == "Recall" ? "selected" : "" }}>Recall</option>
                                    <option value="Wrong Number" {{ $followup_lead_data->first_no_reason == "Wrong Number" ? "selected" : "" }}>Wrong Number</option>
                                    <option value="Not Available" {{ $followup_lead_data->first_no_reason == "Not Available" ? "selected" : "" }}>Not Available</option>
                                    <option value="Contact Number Change" {{ $followup_lead_data->first_no_reason == "Contact Number Change" ? "selected" : "" }}>Contact Number Change</option>
                                    <option value="Business Closed" {{ $followup_lead_data->first_no_reason == "Business Closed" ? "selected" : "" }}>Business Closed</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 contact_number_change mt-3">
                                <label for="name_change" class="form-label">संपर्क व्यक्ति का नाम</label><span class="text-danger">&nbsp;*</span>
                                <input type="text" class="form-control" id="name_change" name="name_change" value="{{$single_lead->contact_per_name}}" placeholder="Enter Contact Person Name">
                            </div>
                            <div class="col-md-3 contact_number_change mt-3">
                                <label for="number_change" class="form-label">संपर्क मोबाइल नंबर</label><span class="text-danger">&nbsp;*</span>
                                <input type="number" class="form-control" id="number_change" name="number_change" value="{{$single_lead->contact_number}}" placeholder="Enter Contact Number">
                            </div>
                            {{-- <div id="contact_number_change" class="col-md-3 mt-3">
                                <input class="form-control" type="text" name="name_change" value="{{ $single_lead->contact_per_name; }}">&nbsp;
                                <input class="form-control" type="number" name="number_change" value="{{ $single_lead->contact_number; }}" >
                            </div> --}}
                            
                            <div id="first_info">
                                <p>में LetmeGrab से, {{ session()->get('empusrid') }} , बात कर रहा/रही हूं, हमारी कंपनी ने आपके बिजनेस को बेहतर बनाने के लिए नए कॉन्सेप्ट के साथ एक एप्लीकेशन लॉन्च की है जिसका नाम है LMG GO.</p>
                                <p>सर / मैम, LMG Go L-commerce कॉन्सेप्ट पर काम करता है, जहां हर कोई सेलर्स LMG GO पोर्टल पे रजिस्टर होकर अपना बिजनेस बढ़ा सकता है।</p>
                                <p>सर / मैम, मैंने आपके बिजनेस के बारे में बात करने के लिए आपको कॉल किया है, क्या ये सही वक्त है आपके साथ बात करने का?</p>
                                <p>    
                                    <label class="radio-inline">
                                        <input type="radio" name="chat_now" class="chat_now" value="Y" /> Yes
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="chat_now" class="chat_now" value="N" /> No
                                    </label>
                                </p>
                            </div>    
                            
                            <div id="yes_chat_now">
                                <p>Thank you so much सर / मैम, As a बिजनेस person हर कोई चाहता है की उनका बिजनेस बड़ा, जैसा कि आप भी चाहते होंगे. सर / मैम, क्या आप किसी कंपनी के माध्यम से online selling करते हो? </p>
                                <p>    
                                    <label class="radio-inline">
                                        <input type="radio" name="online_selling" class="online_selling" value="Y"> Yes
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="online_selling" class="online_selling" value="N"> No
                                    </label>
                                </p>
                                <div class="col-md-3" id="portal">
                                    <p>क्या मैं पोर्टल का नाम जान सकता / सकती हूँ ?</p>
                                    <p><input type="text" name="protal_name" id="protal_name" class="form-control" value="" placeholder="Enter Portal Name"></p> 
                                </div>
                                <div id="info_div">
                                    <p>जैसा  की  आप  e-commerce के  बारे  में  जानते  ही  होंगे! Letmegrab.com एक  online shoping portal है , जहाँ  पर  हम  seller को  एक  platfrom provide करते  है , जहाँ  seller अपनी  product LMG के  माध्यम  से  पुरे  देश में  बेच  सकते  है । </p>
                                    
                                    <p>LMG एक  इंडियन  startup company है , जिसकी  head office surat gujarat में  है। </p>
                                    
                                    <p>LMG ने  india मैं  12000 से  भी  ज्यादा  pincode cover किया  है |</p>
                                    
                                    <p>LMG RBI के   नियम  का  पालन  करती  है </p>
                                    
                                    <p>क्या आप  LMG के  साथ  register होकर अपना  बिज़नेस  पुरे  इंडिया  में  फैलाना  चाहते  है ?</p>
                                    
                                    <p>    
                                        <label class="radio-inline">
                                            <input type="radio" name="register" class="register" value="Y"> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="register" class="register" value="N"> No
                                        </label>
                                    </p>
                                    
                                    <div id="appoinment-div">
                                        <p>हमारा person आपके पास आएगा, date-time? </p>
                                        <p>
                                            <input type="text" name="appoinment_date" id="appoinment_date" value="" placeholder="Enter Date"  data-date-format="yyyy-mm-dd"  class="datepicker form-control">
                                            &nbsp;
                                            <input type="text" name="appoinment_time" id="appoinment_time" value="" class="timepicker form-control" placeholder="Time">
                                        </p>
                                        <p>
                                            <select name="followup_area" id="followup_area" class="form-control">
                                                <option value="">--Select Area--</option>
                                                @foreach ($all_areas as $area)
                                                    <option value="{{ $area['area_name']}}" {{ $area['area_name'] == $single_lead->area ? "selected" : "" }} >{{ $area['area_name']}}</option>
                                                @endforeach
                                            </select>
                                        </p>
                                        <div div="last_div">
                                            <p>अधिक  जानकारी  के  लिए  seller.letmegrab.com पर  visit कर  सकते  हो  और  registation कर  सकते  हो .</p>
                                            <p>आशा  करती/करता  हु  की  मैंने आपको जो भी जानकारी दी है आप उससे संतुस्ट है, क्या मैं आपकी कोई अन्य सहायता कर सकती/सकता हूँ?</p>
                                            <p>
                                                <label class="radio-inline">
                                                    <input type="radio" name="help" class="help" value="Y"> Yes
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="help" class="help" value="N"> No
                                                </label>
                                            </p>
                                            <div id="last_remark">
                                                <textarea cols="20" rows="10" name="query_remark" class="div-remarks form-control" placeholder="Query Remark"></textarea>
                                            </div>
                                            <div id="last_info">
                                                <p>आपका कीमती समय देने के लिए धन्यवाद्, आपकी बात LMG में {{ session()->get('empusrid') }} से हो रही थी| आपका दिन शुभ हो|</p>
                                            </div>
                                            
                                            <!--<div id="final_remark">
                                                <textarea cols="20" rows="10" name="last_remark" placeholder="Final Remark"></textarea>
                                            </div>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="no_appinment_timestamp" class="col-md-3 mt-3">
                                <p>Sir/Mam, तो  मै  आपको  किस  time पर CALL  कर  सकता / सकती  हूँ?</p>
                                <p>
                                    <input type="text" name="recall_date" id="recall_date" class="form-control" placeholder="Enter Recall Date here"  data-date-format="yyyy-mm-dd" required>
                                    &nbsp;
                                    <input type="text" name="recall_time" id="start_time" class="form-control timepicker" placeholder="Enter Recall Time here" required>
                                    &nbsp;
                                    <button type="button" class="btn btn-danger m-3" onclick="$('#recall_date').val('');$('#start_time').val('');">Clear</button>
                                </p>
                            </div>
                            
                            <div id="final_massage">
                                <p>Okay, sir/Mam आपका  कीमती  समय  देने  के  लिए  धन्यवाद , आपकी  बात  letmegrab.com में  {{ session()->get('empusrid') }} से  हो  रही  थी , आपका दिन  शुभ  हो |</p>
                            </div>
                            
                            <div id="final_remark">
                                <textarea cols="20" rows="10" name="last_remark" class="div-remarks form-control" placeholder="Final Remark"></textarea>
                            </div>
                            <input type="hidden" name="start_call_datetime" id="start_call_datetime" value="{{ date('Y-m-d H:i:s') }}"/>
                            <input type="hidden" name="end_call_datetime" id="end_call_datetime" />
                            <input type="hidden" name="lead_area" id="lead_area" value="{{ $single_lead->area }}" >
                            <input type="hidden" name="upload_lead_id" value="{{ $single_lead->id }}" >
                            
                        </div>
                        
                        <div class="form-actions" align="center">
                            <p id="followup_error_msg" style="font-weight: bold;text-align: center;"></p>
                            <input type="button" name="btn_sav" id="btn_sav" class="btn btn-success" value="Save Call Engagement Details" onclick="javascript:submitFollowup();" />
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
                            url: "{{route('telecaller.leads.update.single')}}",
                            type: 'POST',
                            data: fd,
                            processData: false,
                            contentType: false,
                            cache: false,
                            dataType:"json",
                            success: function(response) {
                                // console.log(response);
                                $('html,body').animate({
                                    scrollTop: $('.updateStatus').offset().top - 250
                                },500);
                                $('.updateStatus').addClass('alert alert-success'+status);
                                $('.updateStatus').html(response.message);

                                setTimeout(() => {
                                    $('.updateStatus').html('');
                                    $('.updateStatus').removeClass('alert alert-'+status);
                                }, 3000);
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

                // CALL ENGAGEMENT JS
                let start = new Date();
                let end = new Date(new Date().setMonth(start.getMonth()+1))
                // let end = new Date();
                $('.call_datepicker').datepicker();
                $("#appoinment_date").datepicker({
                   startDate:start,
                });
                $('#recall_date').datepicker({
                    autoHide: true,
                    inline: true,
                    format: 'yyyy-mm-dd',
                    startDate: start,
                    endDate: end
                });
                $('.timepicker').clockpicker({
                    autoclose: true
                });
                
                $("#no_chat_now").hide();
                $("#no_appinment_timestamp").hide();
                $("#final_massage").hide();
                $("#yes_chat_now").hide();
                $("#portal").hide();
                $("#info_div").hide();
                $("#appoinment-div").hide();
                $("#first_info").hide();
                $("#last_remark").hide();
                $("#last_div").hide();
                $("#last_info").hide();
                $("#final_remark").hide();
                $("#btn_sav").hide();
                
                $("#first_no").hide();
                $(".contact_number_change").hide();
                
                $(".first_conf").on("click",function() {
                    var chat_now = $(this).val();
                    
                    $("#first_info").hide();
                    $("#first_no").hide();
                    $("#no_chat_now").hide();
                    $("#no_appinment_timestamp").hide();
                    $("#final_massage").hide();
                    $("#yes_chat_now").hide();
                    $("#portal").hide();
                    $("#info_div").hide();
                    $("#appoinment-div").hide();
                    $("#last_remark").hide();
                    $("#last_div").hide();
                    $("#last_info").hide();
                    $("#final_remark").hide();
                    $("#btn_sav").hide();
                    $("#first_no_reason").val("");
                    $(".contact_number_change").hide();
                    
                    if(chat_now == 'Y'){
                        $("#first_info").show();
                    } else {
                        $("#first_no").show();
                    }
                });
                
                $("#first_no_reason").on("change",function() {
                    
                    var no_reason = $(this).val();
                    
                    $("#no_appinment_timestamp").hide();
                    $(".contact_number_change").hide();
                    $("#final_massage").hide();
                    
                    if(no_reason == "Recall"){
                        $("#no_appinment_timestamp").show();
                    }else if(no_reason == "Wrong Number"){
                        
                    }else if(no_reason == "Not Available"){
                        $("#no_appinment_timestamp").show();
                    }else if(no_reason == "Contact Number Change"){
                        $(".contact_number_change").show();
                        $("#no_appinment_timestamp").show();
                    }else if(no_reason == "Business Closed"){
                        
                    }else{
                        $("#no_appinment_timestamp").hide();
                        $(".contact_number_change").hide();
                    }
                    
                });
                
                $(".chat_now").on("click",function() {
                    var chat_now = $(this).val();
                    
                    $("#yes_chat_now").hide();
                    $("#no_appinment_timestamp").hide();
                    
                    if(chat_now == 'Y'){
                        $("#yes_chat_now").show();
                    } else {
                        $("#no_appinment_timestamp").show();
                    }
                });
                
                $(".re_chat_now").on("click",function() {
                    var chat_now = $(this).val();
                    if(chat_now == 'Y'){
                        $("#yes_chat_now").show();
                        $("#no_appinment_timestamp").hide();
                    } else {
                        $("#no_appinment_timestamp").show();
                        $("#yes_chat_now").hide();
                    }
                });
                
                $(".online_selling").on("click",function() {
                    var chat_now = $(this).val();
                    if(chat_now == 'Y'){
                        $("#portal").show();
                        $("#info_div").show();
                    } else {
                        $("#portal").hide();
                        $("#info_div").show();
                    }
                });
                
                $(".register").on("click",function() {
                    var chat_now = $(this).val();
                    if(chat_now == 'Y'){
                        $("#appoinment-div").show();
                    } else {
                        $("#appoinment-div").hide();
                    }
                });
                
                $(".help").on("click",function() {
                    var chat_now = $(this).val();
                    if(chat_now == 'Y'){
                        $("#last_remark").show();
                        $("#last_info").show();
                    } else {
                        $("#last_remark").hide();
                        $("#last_info").show();
                    }
                });
                
                $('#disconnect').on('click', function() {
                    // alert("fed");
                    var dt = new Date();
                    var DateTime = dt.getFullYear() + "-" + (dt.getMonth()+1) + "-" + dt.getDate() +" "+ dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
                    //alert(DateTime);
                    $("#end_call_datetime").val(DateTime)
                    $("#btn_sav").show();
                    $("#disconnect").hide();
                    $("#final_remark").show();
                });
                
                $("#reset-btn").on("click",function() {
                    location.reload();
                })
                
                $( "#start_time" ).change(function() {
                    var GivenDate = $("#recall_date").val();
                    var CurrentDate = new Date();
                    GivenDate = new Date(GivenDate);

                    if(GivenDate > CurrentDate){
                        $("#final_massage").show()
                    }else{
                        //alert('Given date is not greater than the today date.');
                        $("#final_massage").hide()
                    }
                });
                
                
                function GetTodayDate() {
                    var tdate = new Date();
                    var dd = tdate.getDate(); //yields day
                    var MM = tdate.getMonth(); //yields month
                    var yyyy = tdate.getFullYear(); //yields year
                    var currentDate= dd + "-" +( MM+1) + "-" + yyyy;
                    return currentDate;
                }
                
                $( "#appoinment_time" ).change(function() {
                    var GivenDate = $("#appoinment_date").val();
                    var CurrentDate = new Date();
                    GivenDate = new Date(GivenDate);
                    
                    if(GivenDate > CurrentDate){
                        $("#last_div").show();
                        $("#more-info").show();
                    }else{
                        //alert('Given date is not greater than the today date.');
                        $("#last_div").hide();
                        $("#more-info").hide();
                    }
                });
                
                $('#btn_sav').on('click', function(){
                    var form_data = $("#followup_form").serializeArray();
                    
                    // console.log(form_data);
                    
                    $.ajax({
                        
                        type: "POST",
                        url: "{{route('telecaller.leads.save.leadCallEngagementData')}}",
                        data: form_data,
                        dataType: "JSON",
                        success: function(data){
                            // console.log(data);
                            Swal.fire({
                                title: data.status,
                                text: data.message,
                                icon: data.status,
                                showConfirmButton: false,
                                showCancelButton: false,
                                showCloseButton: false,
                                timer: swalModelTimeOut
                            });

                            setTimeout(() => {
                                window.location.href = `{{route('telecaller.leads.view.allLeads')}}`;
                            }, pageReloadTimeOut);
                        }
                        
                    });
                });  
                
                const is_person = "{{$followup_lead_data->is_person}}";
                const chat_now = "{{$followup_lead_data->chat_now}}";
                $(".first_conf:checked").click();
                if(is_person == 'Y') {
                    $(".chat_now:checked").click();
                    if(chat_now == 'Y') {
                        $(".online_selling:checked").click();
                        $(".register:checked").click();
                    }
                } else if(is_person == 'N') {
                    $("#first_no_reason").change();
                }
            });

        </script>
    @endpush
@endsection