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
                   <div class="breadcrumb-item"><a href="{{route('crm.leads.view.followUpsDetails', ['id' => base64_encode($lead_id)])}}"> View Follow-up Details </a></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4>Lead Followup Details</h4>
                    <div class="mt-4">
                        <table id="data-table" class="table data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">Added By</th>
                                    <th class="text-center">Added For</th>
                                    <th class="text-center">Before Status</th>
                                    <th class="text-center">After Status</th>
                                    <th class="text-center">In Person</th>
                                    <th class="text-center">Want to Register</th>
                                    <th class="text-center">Appointment Date-Time</th>
                                    <th class="text-center">Recall Date-Time</th>
                                    <th class="text-center">Remarks</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!is_null($leadFollowup))
                                    @foreach ($leadFollowup as $lead)
                                    {{-- <pre>
                                        {{print_r($lead)}}
                                    </pre> --}}
                                        <tr>
                                            <td class="text-center">{{$lead->followup_added_by}}</td>
                                            <td class="text-center">{{$lead->followup_added_for}}</td>
                                            <td class="text-center">{{$lead->getLeadPreviousStatus['status_name']}}</td>
                                            <td class="text-center">{{$lead->getLeadCurrentStatus['status_name']}}</td>
                                            <td class="text-center">{{$lead->is_person == 'Y' ? "YES" : "NO"}}</td>
                                            <td class="text-center">{{$lead->want_to_register == 'Y' ? "YES" : "NO"}}</td>
                                            <td class="text-center">{{date('dS M y', strtotime($lead->appointment_date))}}<br/>{{date('h:i A', strtotime($lead->appointment_time))}}</td>
                                            <td class="text-center">{{date('dS M y', strtotime($lead->recall_date))}}<br/>{{date('h:i A', strtotime($lead->recall_time))}}</td>
                                            <td class="text-center">{{html_entity_decode($lead->last_remark, ENT_QUOTES)}}</td>
                                            <td class="text-center"><a href="{{route('crm.leads.view.singleFollowUpsDetails', ['id' => base64_encode($lead->followup_id)])}}"><i class="icon-eye feather" style="font-size: 24px; font-weight:bold;"></i></a></td>
                                        </tr>                                
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center">Added By</th>
                                    <th class="text-center">Added For</th>
                                    <th class="text-center">Before Status</th>
                                    <th class="text-center">After Status</th>
                                    <th class="text-center">In Person</th>
                                    <th class="text-center">Want to Register</th>
                                    <th class="text-center">Appointment Date-Time</th>
                                    <th class="text-center">Recall Date-Time</th>
                                    <th class="text-center">Remarks</th>
                                    <th class="text-center">Action</th>
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
                ]
            });
        </script>
    @endpush
@endsection