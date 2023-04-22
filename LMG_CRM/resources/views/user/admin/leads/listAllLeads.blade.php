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
                   <div class="breadcrumb-item"><a href="{{route('admin.leads.view.allLeads')}}"> Leads </a></div>
                   <div class="breadcrumb-item"><a href="{{route('admin.leads.view.allLeads')}}"> View all Leads </a></div>
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
                                    <th>Added For</th>
                                    <th class="text-center">Status</th>
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
                                            <td>{{$lead->added_for}}</td>
                                            <td class="text-center">{{$lead->getLeadStatus['status_name']}}</td>
                                            <td class="text-center"><a href="{{route('admin.leads.view.single', ['id' => base64_encode($lead->id)])}}"><i class="icon-search feather" style="font-size: 24px; font-weight:bold;"></i></a></td>
                                            <td class="text-center"><a href="{{route('admin.leads.view.followUpsDetails', ['id' => base64_encode($lead->id)])}}"><i class="icon-list feather" style="font-size: 24px; font-weight:bold;"></i></a></td>
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
                                    <th class="text-center">View</th>
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
    @endpush
@endsection