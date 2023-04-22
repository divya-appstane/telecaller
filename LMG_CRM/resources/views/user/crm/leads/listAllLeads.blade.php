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
                   <div class="breadcrumb-item"><a href="javascript:void(0)"> Leads </a></div>
                   <div class="breadcrumb-item"><a href="{{route('crm.leads.view.allLeads')}}"> View all Leads </a></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4>All Leads {{"(".ucwords(session()->get('load_dashboard')).")"}}</h4>
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
                                    <th class="text-center">Call</th>
                                    <th class="text-center">Follow-up</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!is_null($all_leads))
                                    @foreach ($all_leads as $lead)
                                        <tr>
                                            <td>{{$lead->company_name}}</td>
                                            <td>{{$lead->contact_per_name}}</td>
                                            <td>
                                                @if ($lead->lead_status == 1)
                                                    <a href="tel:+91{{$lead->contact_number}}">{{$lead->contact_number}}</a>
                                                @else
                                                    {{$lead->contact_number}}
                                                @endif
                                            </td>
                                            <td>{{$lead->address}}</td>
                                            <td>{{$lead->added_by}}</td>
                                            <td class="text-center">{{$lead->getLeadStatus['status_name']}}</td>
                                            @if ($lead->lead_status == "10" || $lead->lead_status == "12")
                                                <td class="text-center">
                                                    <a href="{{route('crm.leads.view.feedbackCallView', ['id' => base64_encode($lead->id)])}}">
                                                    {{-- <a class="timerclass" data-url="{{route('crm.leads.view.feedbackCallView', ['id' => base64_encode($lead->id)])}}"> --}}
                                                        <i class="icon-phone-call feather"  style="font-size: 24px; font-weight:bold;"></i>
                                                    </a>
                                                </td>
                                                
                                            @elseif($lead->lead_status == "13" || $lead->lead_status == "14")
                                                <td class="text-center">
                                                    @if($lead->call_date >= date('Y-m-d') && $lead->call_time >= date('H:i:s'))
                                                        <a href="{{route('crm.leads.view.feedbackCallViewStepTwo', ['id' => base64_encode($lead->id)])}}">
                                                            <i class="icon-phone-call feather" style="font-size: 24px; font-weight:bold;"></i>
                                                        </a>
                                                   @else 
                                                        <p class="text-center">-</p>
                                                   @endif
                                                </td>
                                               
                                            @elseif($lead->lead_status == "15" || $lead->lead_status == "16" || $lead->lead_status == "17")
                                                <td class="text-center">
                                                    @if($lead->call_date >= date('Y-m-d') && $lead->call_time >= date('H:i:s'))
                                                        <a href="{{route('crm.leads.view.feedbackCallViewStepThree', ['id' => base64_encode($lead->id)])}}">
                                                            <i class="icon-phone-call feather" style="font-size: 24px; font-weight:bold;"></i>
                                                        </a>
                                                    @else
                                                        <p class="text-center">-</p>
                                                    @endif
                                                </td>
                                               
                                            @elseif($lead->lead_status == "11")
                                                <td class="text-center">
                                                    <a href="{{route('crm.leads.view.feedbackCallViewNotIntersted', ['id' => base64_encode($lead->id)])}}">
                                                        <i class="icon-phone-call feather" style="font-size: 24px; font-weight:bold;"></i>
                                                    </a>
                                                </td>
                                                
                                            @else
                                                <td class="text-center"> - </td>
                                            @endif
                                            <td class="text-center">
                                                <a href="{{route('crm.leads.view.followUpsDetails', ['id' => base64_encode($lead->id)])}}"><i class="icon-list feather" style="font-size: 24px; font-weight:bold;"></i>
                                                </a>
                                            </td>
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
                                    <th class="text-center">Call</th>
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
        <script>
            $('.data-table').DataTable({
                'columnDefs': [
                    { 'orderable': false, 'targets': 0 }
                ],
                order: [[5, 'desc']],
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function(){
                $(".timerclass").click(function(){
                    var hours =0;
                    var mins =0;
                    var seconds =0;
                    startTimer();

                function startTimer()
                {
                    timex = setTimeout(function(){
                        seconds++;
                        if(seconds >59)
                        {
                            seconds=0;
                            mins++;
                            if(mins>59) 
                            {
                                mins=0;
                                hours++;
                                if(hours <10) 
                                {
                                    $("#hours").text('0'+hours+':');
                                } 
                                else 
                                {
                                    $("#hours").text(hours+':');
                                }
                            }
                                
                            if(mins<10)
                            {                     
                                $("#mins").text('0'+mins+':');
                            }       
                            else 
                            {
                                $("#mins").text(mins+':');
                            }
                        }    
                        if(seconds <10) 
                        {
                            $("#seconds").text('0'+seconds);
                        } 
                        else 
                        {
                            $("#seconds").text(seconds);
                        }
                    startTimer();
                        },1000);
                    }
                    var url = $(this).data("url");
                    $.ajax({
                        type:'GET',
                        url: "{{route('crm.leads.store.createSession')}}",
                        contentType: false,
                        processData: false,
                        success:function(data){
                            window.location.href= url;
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection