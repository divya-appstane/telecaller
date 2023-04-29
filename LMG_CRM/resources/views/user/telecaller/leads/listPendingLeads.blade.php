@extends('user.layouts.master')

@section('main-section')
    @push('css')
        <link href="{{ env('USER_ASSETS') }}vendors/datatables/dataTables.bootstrap.min.css" rel="stylesheet">
    @endpush
    <div class="content">
        <div class="main">
            <div class="page-header">
                <h4 class="page-title"></h4>
                <div class="breadcrumb">
                    <span class="me-1 text-gray"><i class="feather icon-home"></i></span>
                    <div class="breadcrumb-item"><a href="{{ route(session()->get('load_dashboard') . '.dashboard') }}">
                            Dashboard </a></div>
                    <div class="breadcrumb-item"><a href="javascript:void(0)"> Leads </a></div>
                    <div class="breadcrumb-item"><a href="{{ route('telecaller.leads.pendingLeads') }}"> View pending Leads
                        </a></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4>Pending Leads</h4>
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
                                    <th class="text-center">Call</th>
                                    <th class="text-center">View</th>
                                    <th class="text-center">Follow-up</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!is_null($pending_leads))
                                    @foreach ($pending_leads as $lead)
                                        {{-- <pre>
                                {{print_r($lead)}}
                            </pre> --}}
                                        <tr>
                                            <td>{{ $lead->company_name }}</td>
                                            <td>{{ $lead->contact_per_name }}</td>
                                            <td>
                                                @if ($lead->lead_status == 1)
                                                    <a
                                                        href="tel:+91{{ $lead->contact_number }}" />{{ $lead->contact_number }}</a>
                                                @else
                                                    {{ $lead->contact_number }}
                                                @endif
                                            </td>
                                            <td>{{ $lead->address }}</td>
                                            <td>{{ $lead->added_by }}</td>
                                            <td class="text-center">{{ $lead->getLeadStatus['status_name'] }}</td>
                                            @if ($lead->lead_status == 1)
                                                <td class="text-center">
                                                    {{-- @if ($lead->call_date <= date('Y-m-d')) --}}
                                                        <a href="#" class="countdowntimer"
                                                            data-id="@php echo time(); @endphp"
                                                            data-action="{{ route('telecaller.leads.view.leadCalledEngagementView', ['id' => base64_encode($lead->id)]) }}">
                                                            <i class="icon-phone-call feather"
                                                                style="font-size: 24px; font-weight:bold;"></i>
                                                        </a>
                                                    {{-- @else
                                                        <p class="text-center">-</p>
                                                    @endif --}}
                                                </td>
                                            @elseif($lead->lead_status == '2' || $lead->lead_status == '4' || $lead->lead_status == '5')
                                                <td class="text-center">
                                                    {{-- @if ($lead->call_date <= date('Y-m-d')) --}}
                                                        <a href="#" class="recalltimer"
                                                            data-id="@php echo time(); @endphp"
                                                            data-action="{{ route('telecaller.leads.view.leadCalledReEngagementView', ['id' => base64_encode($lead->id)]) }}">
                                                            <i class="icon-phone-call feather"
                                                                style="font-size: 24px; font-weight:bold;"></i>
                                                        </a>
                                                    {{-- @else
                                                        <p class="text-center">-</p>
                                                    @endif --}}
                                                </td>
                                            @else
                                                <td class="text-center"> - </td>
                                            @endif
                                            <td class="text-center"><a
                                                    href="{{ route('telecaller.leads.view.single', ['id' => base64_encode($lead->id)]) }}"><i
                                                        class="icon-search feather"
                                                        style="font-size: 24px; font-weight:bold;"></i></a></td>
                                            <td class="text-center"><a
                                                    href="{{ route('telecaller.leads.view.followUpsDetails', ['id' => base64_encode($lead->id)]) }}""><i
                                                        class="icon-list feather"
                                                        style="font-size: 24px; font-weight:bold;"></i></a></td>
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
            <script src="{{ env('USER_ASSETS') }}vendors/datatables/jquery.dataTables.min.js"></script>
            <script src="{{ env('USER_ASSETS') }}vendors/datatables/dataTables.bootstrap.min.js"></script>
            <script>
                $('.data-table').DataTable({
                    'columnDefs': [{
                        'orderable': false,
                        'targets': 0
                    }],
                    order: [
                        [5, 'desc']
                    ],
                });
            </script>
            <script type="text/javascript">
                $(document).ready(function() {
                    $(".countdowntimer").click(function() {
                        var starttime = $(this).data('id');
                        var sucurl = $(this).data('action');
                        var url = "{{ route('telecaller.leads.store.createSession') }}";
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {
                                "sucurl": sucurl
                            },
                            success: function(response) {
                                elem = document.getElementById('timer').innerHTML;
                                window.location.href = sucurl;

                                setInterval(cntDown, 1000);
                            }
                        });
                    });


                    $(".recalltimer").click(function() {
                        var starttime = $(this).data('id');
                        var sucurl = $(this).data('action');
                        var url = "{{ route('telecaller.leads.store.createSession') }}";
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {
                                "sucurl": sucurl
                            },
                            success: function(response) {
                                elem = document.getElementById('timer').innerHTML;
                                window.location.href = sucurl;

                                setInterval(cntDown, 1000);
                            }
                        });
                    });
                });
            </script>
        @endpush
    @endsection
