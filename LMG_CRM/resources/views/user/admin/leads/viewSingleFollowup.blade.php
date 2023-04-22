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
               <div class="breadcrumb-item"><a href="{{route('admin.leads.view.allLeads')}}"> View all Leads </a></div>
               <div class="breadcrumb-item"><a href="{{route('admin.leads.view.single', ["id" => base64_encode($leadFollowup->followup_id)])}}"> View Single Follow-up </a></div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4>Lead Follow-up Information</h4>
            
                <div class="col-md-6">
                    <label for="is_person" class="form-label">Is Person</label>
                    <input type="text" class="form-control" id="is_person" readonly value="<?= isset($leadFollowup->is_person) && $leadFollowup->is_person == 'Y' ? "YES" : "NO"; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="chat_now" class="form-label">Chat Now</label>
                    <input type="text" class="form-control" id="chat_now" readonly value="<?= isset($leadFollowup->chat_now) && $leadFollowup->chat_now == 'Y' ? "YES" : "NO"; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="is_sell_online" class="form-label">Is Sell Online</label>
                    <input type="text" class="form-control" id="is_sell_online" readonly value="<?= isset($leadFollowup->is_sell_online) && $leadFollowup->is_sell_online == 'Y' ? "YES" : "NO"; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="portal_name" class="form-label">Portal Name</label>
                    <input type="text" class="form-control" id="portal_name" readonly value="<?= isset($leadFollowup->portal_name) && $leadFollowup->portal_name != '' ? $leadFollowup->portal_name : ''; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="want_to_register" class="form-label">Want To Register</label>
                    <input type="text" class="form-control" id="want_to_register" readonly value="<?= isset($leadFollowup->want_to_register) && $leadFollowup->want_to_register == 'Y' ? "YES" : "NO"; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="appoinment_date" class="form-label">Appoinment Date</label>
                    <input type="text" class="form-control" id="appoinment_date" readonly value="<?= isset($leadFollowup->appoinment_date) && $leadFollowup->appoinment_date != '' && $leadFollowup->appoinment_date != '1970-01-01' ? $leadFollowup->appoinment_date : '--'; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="appoinment_time" class="form-label">Appoinment Time</label>
                    <input type="text" class="form-control" id="appoinment_time" readonly value="<?= isset($leadFollowup->appoinment_time) && $leadFollowup->appoinment_time != '' && $leadFollowup->appoinment_time != '00:00:00' ? $leadFollowup->appoinment_time : '--'; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="want_help" class="form-label">Want Help</label>
                    <input type="text" class="form-control" id="want_help" readonly value="<?= isset($leadFollowup->want_help) && $leadFollowup->want_help == 'Y' ? 'YES' : 'NO'; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="query_remark" class="form-label">Query Remark</label>
                    <input type="text" class="form-control" id="query_remark" readonly value="<?= isset($leadFollowup->query_remark) && $leadFollowup->query_remark != '' ? $leadFollowup->query_remark : ''; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="last_remark" class="form-label">Final_remark</label>
                    <input type="text" class="form-control" id="last_remark" readonly value="<?= isset($leadFollowup->last_remark) && $leadFollowup->last_remark != '' ? $leadFollowup->last_remark : ''; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="recall_date" class="form-label">Recall date</label>
                    <input type="text" class="form-control" id="recall_date" readonly value="<?= isset($leadFollowup->recall_date) && $leadFollowup->recall_date != '1970-01-01' ? $leadFollowup->recall_date : ''; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="recall_time" class="form-label">Recall Time</label>
                    <input type="text" class="form-control" id="recall_time" readonly value="<?= isset($leadFollowup->recall_time) && $leadFollowup->recall_time != '00:00:00' ? $leadFollowup->recall_time : ''; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="call_start_time" class="form-label">call_start_time</label>
                    <input type="text" class="form-control" id="call_start_time" readonly value="<?=  isset($leadFollowup->call_start_time) && $leadFollowup->call_start_time != '' ? $leadFollowup->call_start_time : ''; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-md-6">
                    <label for="call_end_time" class="form-label">call_end_time</label>
                    <input type="text" class="form-control" id="call_end_time" readonly value="<?= isset($leadFollowup->call_end_time) && $leadFollowup->call_end_time != '' ? $leadFollowup->call_end_time : ''; ?>" style="cursor: no-drop;" />
                </div>
                <div class="col-12 mt-3">
                    <a onclick="javascript: history.back()" class="btn btn-warning ml-2">Go Back</a>
                </div>
            </div>

        </div>
    </div>


@endsection