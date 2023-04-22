@extends('user.layouts.master')

@section('main-section')
    @push('css')
        <link href="{{env('USER_ASSETS')}}vendors/datatables/dataTables.bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
        <style>
            .datepicker-dropdown{
                z-index: 99999999999999999 !important;
            }

            .error{
                color: red;
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
                   <div class="breadcrumb-item"><a href="{{route('telecaller.leads.view.allLeads')}}"> View all Leads </a></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4>All Leads {{"(".ucwords(session()->get('load_dashboard')).")"}}</h4>
                    {{-- <p>DataTables is a plug-in for the jQuery Javascript library. It is a highly flexible tool, built upon the foundations of progressive enhancement, that adds all of these advanced features to any HTML table. Below is an example of zero configuration.</p> --}}
                    <div class="mt-4">
                        <table id="data-table" class="table data-table">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Contact Person Name</th>
                                    <th>Contact Number</th>
                                    <th>Address</th>
                                    <th>Added By</th>
                                    <th class="text-center">Status</th>
                                    <th>Call Datetime</th>
                                    <th>Appointment Datetime</th>
                                    <th class="text-center">Call</th>
                                    <th class="text-center">View</th>
                                    <th class="text-center">Follow-up</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!is_null($all_leads))
                                    @foreach ($all_leads as $lead)
                                    {{-- <pre>
                                        {{print_r($lead)}}
                                    </pre> --}}
                                        <tr>
                                            <td>{{$lead->company_name}}</td>
                                            <td>{{$lead->contact_per_name}}</td>
                                            <td>
                                                @if ($lead->lead_status == 1)
                                                    <a href="tel:+91{{$lead->contact_number}}"/>{{$lead->contact_number}}</a>
                                                @else
                                                    {{$lead->contact_number}}
                                                @endif
                                            </td>
                                            <td>{{$lead->address}}</td>
                                            <td>{{$lead->added_by}}</td>
                                            <td class="text-center">{{$lead->getLeadStatus['status_name']}}</td>
                                            <td class="text-center">{{date('d-m-Y H:i:s', strtotime($lead->call_date." ".$lead->call_time))}}</td>
                                            <td class="text-center">{{date('d-m-Y H:i:s', strtotime($lead->appointment_date." ".$lead->appointment_time))}}</td>
                                            @if ($lead->lead_status == "6" || $lead->lead_status == "8")
                                                <td>
                                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#assignModal"  onclick="$('#assignModal_lead_id').val({{ $lead->id }})">
                                                        <i class="icon-phone-call feather" style="font-size: 1.2rem;"></i>
                                                    </button>
                                                </td>
                                            @elseif ($lead->lead_status == "7")
                                                <td class="text-center">
                                                    <a class="btn btn-outline-primary" href="{{route('marketing.leads.show.salesMeetingg', ['id' => base64_encode($lead->id)])}}">
                                                        <i class="icon-edit feather" style="font-size: 1.2rem;"></i>
                                                    </a>
                                                </td>
                                            @else
                                                <td class="text-center"> - </td>
                                            @endif
                                            <td class="text-center"><a href="{{route('marketing.leads.view.single', ['id' => base64_encode($lead->id)])}}"><i class="icon-search feather" style="font-size: 20px; font-weight:bold;"></i></a></td>
                                            <td class="text-center"><a href="{{route('marketing.leads.view.followUpsDetails', ['id' => base64_encode($lead->id)])}}""><i class="icon-list feather" style="font-size: 20px; font-weight:bold;"></i></a></td>
                                        </tr>                                
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Contact Person Name</th>
                                    <th>Contact Number</th>
                                    <th>Address</th>
                                    <th>Added By</th>
                                    <th class="text-center">Status</th>
                                    <th>Call Datetime</th>
                                    <th>Appointment Datetime</th>
                                    <th class="text-center">Call</th>
                                    <th class="text-center">View</th>
                                    <th class="text-center">Follow-up</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODEL START -->
        <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ucwords(session()->get('load_dashboard'))}} Call</h5>
                    </div>
                    <div class="modal-body">
                        <form id="assignModal_form" name="assignModal_form" class="row g-2" novalidate autocomplete="off">
                            @csrf
                            <div class="col-md-6">
                                <label for="assignStatus" class="form-label"><strong>Meeting confirm Status:</strong></label>
                                <select id="assignStatus" name="assignStatus" class="form-select">
                                    <option value="" selected>Choose Status...</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-none" id="appstatus">
                                <label for="appoitmentstatus" class="form-label"><strong>Reason (Why No?): </strong></label>
                                <select name="appoitmentstatus" id="appoitmentstatus" class="form-select">
                                    <option value="" selected>Choose Reason...</option>
                                    <option value="new">New Appointment</option>
                                    <option value="cancel">Cancel</option>
                                </select>
                            </div>  
                            <div class="col-md-12 d-none" id="appdate">
                                <label for="recipient-name" class="form-label"><strong>Appointment date & time: </strong></label>
                                <input type="text" name="appoinment_date" id="appoinment_date" value="" placeholder="Enter Date"  data-date-format="yyyy-mm-dd"  class="form-control datepicker">
                                &nbsp;
                                <input type="text" name="appoinment_time" id="appoinment_time" value="" class="form-control timepicker" placeholder="Time">
                                <button type="button" class="form-control btn btn-danger mt-2" onclick="$('#appoinment_date').val('');$('#appoinment_time').val('');"  style="margin-bottom:10px;">Clear</button>
                            </div>  
                
                            <div class="col-md-12 d-none" id="recalldate">
                                <label for="recipient-name" class="form-label"><strong>Recall date & time: </strong></label>
                                <input type="text" name="recall_date" id="recall_date" value="" placeholder="Enter Date"  data-date-format="yyyy-mm-dd"  class="form-control datepicker">
                                &nbsp;
                                <input type="text" name="recall_time" id="recall_time" value="" class="form-control timepicker" placeholder="Time">
                                <button type="button" class="form-control btn btn-danger mt-2" onclick="$('#recall_date').val('');$('#recall_time').val('');"  style="margin-bottom:10px;">Clear</button>
                            </div>
                            
                            <input type="hidden" id="assignModal_lead_id" name="assignModal_lead_id">
                            
                            <p id="followup_error_msg1" style="font-weight:bold;"></p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" name="btn-submit" id="btn-submit">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODEL END -->

    @push('scripts')
        <script src="{{env('USER_ASSETS')}}vendors/datatables/jquery.dataTables.min.js"></script>
        <script src="{{env('USER_ASSETS')}}vendors/datatables/dataTables.bootstrap.min.js"></script>
        <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.js"></script>
        <script>
            $('.data-table').DataTable({
                'columnDefs': [
                    { 'orderable': false, 'targets': 0 }
                ],
                order: [[5, 'desc']],
            });

            let start = new Date();
            let end = new Date(new Date().setMonth(start.getMonth()+1))

            $('#appoinment_date').datepicker({
                autoHide: true,
                inline: true,
                format: 'yyyy-mm-dd',
                startDate: start,
                endDate: end
            });
            $('#recall_date').datepicker({
                autoHide: true,
                inline: true,
                format: 'yyyy-mm-dd',
                startDate: start,
                endDate: end
            });
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

            $( "#assignStatus" ).change(function() {
                var metting = $("#assignStatus").val();
                //alert(metting);
                if(metting == 0){
                    $("#appstatus").removeClass('d-none');
                }else{
                    $("#appstatus").addClass('d-none'); 
                    $("#appdate").addClass('d-none');
                    $("#recalldate").addClass('d-none');
                }    
            });
            
            $( "#selreg" ).change(function() {
                var selreg = $("#selreg").val();
                if(selreg == 0){
                    $("#notregreason").show();
                }else{
                    $("#notregreason").hide();
                    $("#selappdate").hide();
                    $("#notregreasondata").hide();
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
            
            $('#btn-submit').on('click', function(e) {
                e.preventDefault();
                const valid = $('#assignModal_form').valid();
                if(valid) {
                    const form_data = $("#assignModal_form").serializeArray();
                    $.ajax({
                        type: "POST",
                        url: "{{route('marketing.leads.saveCallStatus')}}",
                        data: form_data,
                        dataType: "JSON",
                        success: function(response){
                            $('#modal').modal('toggle');
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
                        }
                    });
                } else {
                    console.log('invalid');
                }
            });

            $('#assignModal_form').validate({
                ignore: ".ignore",
                debug: false,
                errorElement: 'span',
                rules:{
                    assignStatus: {
                        required: true,
                    },
                    appoitmentstatus: {
                        required: function (element) {
                            return $('#assignStatus').val() != 1
                        }
                    },
                    appoinment_date: {
                        required: function (element) {
                            return $('#appoitmentstatus').val() == 'new'
                        }
                    },
                    appoinment_time: {
                        required: function (element) {
                            return $('#appoitmentstatus').val() == 'new'
                        }
                    },
                    recall_date: {
                        required: function (element) {
                            return $('#appoitmentstatus').val() == 'cancel'
                        }                        
                    },
                    recall_time: {
                        required: function (element) {
                            return $('#appoitmentstatus').val() == 'cancel'
                        }
                    },
                },
                messages: {
                    assignStatus: "You must select call status",
                    appoitmentstatus: "You must select appointment status",
                    appoinment_date: "You must select new appointment date",
                    appoinment_time: "You must select new appointment time",
                    recall_date: "You must select recall date",
                    recall_time: "You must select recall time",
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    // if(element.attr("name") == "cat_id[]") {
                    //     // console.log(element);
                    //     element.parent().append( error );
                    // } else {
                    //     error.insertAfter(element);
                    // }
                    error.insertAfter(element);
                },
            });
            
            function update_meeting_status(){
                
                var form_data = $("#meetconfirm_form").serializeArray();
                        
                console.log(form_data);
                
                $.ajax({
                    
                    type: "POST",
                    url: "save_sales_followup_step2.php",
                    data: form_data,
                    dataType: "JSON",
                    success: function(data){
                        console.log(data);
                        if(data.status == "ok"){
                            $("#followup_error_msg2").css("color","green");
                            $("#followup_error_msg2").html(data.message);
                            setTimeout(function(){ location.reload(); }, 2000);
                            
                        }else{
                            $("#followup_error_msg2").css("color","red");
                            $("#followup_error_msg2").html(data.message);
                        }
                    }
                    
                });

            }
        </script>
    @endpush
@endsection