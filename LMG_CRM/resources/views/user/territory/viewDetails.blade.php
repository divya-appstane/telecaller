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
                <div class="breadcrumb-item"><a href="javascript:void(0)"> Territory </a></div>
                <div class="breadcrumb-item"><a href="{{route('territory.viewTerritory')}}"> View Details </a></div>
                {{-- <div class="breadcrumb-item"><a href="{{route('crm.leads.view.feedbackCallView')}}"> Feedback Call View </a></div> --}}
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4>Your Route Map</h4>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapsem show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="card">
                                    <div class="updateStatus"></div>
                                    <div class="card-body">
                                        @foreach ($routemap as $rout)
                                            <img src="{{ asset('/assets/images/' .$rout->filepath) }}" width="30%" height="30%">
                                        @endforeach
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
                <h4>Your Route by Area Name</h4>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapsem show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="card">
                                    <div class="updateStatus"></div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>From</th>
                                                    <th>To</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($route_by_area) != 0)
                                                    @foreach($route_by_area as $area)
                                                        <tr>
                                                            <td>{{$area->tfrom}}</td>
                                                            <td>{{$area->tto}}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <td>No Record Found</td>
                                                @endif
                                            </tbody>
                                        </table>

                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Area Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($route_by_area as $area)
                                                     @php
                                                        $area_name = isset($area->area_name) && $area->area_name != '' ? $area->area_name : '';
                                                        if($area_name != '')
                                                        {
                                                            echo "<td>".$area_name."</td>";
                                                        }
                                                        break;
                                                     @endphp
                                                @endforeach
                                               
                                            </tbody>
                                        </table>

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
                <h4>Your Route Pincodes</h4>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapsem show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="card">
                                    <div class="updateStatus"></div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Pincode List</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($pincode_list) != 0)
                                                    @foreach($pincode_list as $pinc)
                                                        <tr>
                                                            <td>{{$pinc->tpin}}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <td>No Record Found<td>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.js"></script>
    @endpush
    @endsection