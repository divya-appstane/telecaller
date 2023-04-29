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
                   <div class="breadcrumb-item"><a href="javascript:void(0)"> Feedback </a></div>
                   <div class="breadcrumb-item"><a href="{{route('feedback.viewFeedback')}}"> View all Feedback </a></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4>All Feedback</h4>
                    {{-- <p>DataTables is a plug-in for the jQuery Javascript library. It is a highly flexible tool, built upon the foundations of progressive enhancement, that adds all of these advanced features to any HTML table. Below is an example of zero configuration.</p> --}}
                    @csrf
                    <div class="mt-4">
                        <table id="data-table" class="table data-table">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Question Type</th>
                                    <th>Question Display</th>
                                    <th>Question Number</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!is_null($all_feedback))
                                    @foreach ($all_feedback as $feedback)
                                        <tr>
                                            <td>{{$feedback->question}}</td>
                                            <td>{{$feedback->question_type}}</td>
                                            <td>{{$feedback->question_display}}</td>
                                            <td>{{$feedback->question_no}}</td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <a href="#" class="px-2" data-bs-toggle="dropdown">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if (auth('front')->user()->can('feedback-master-update'))
                                                            <li>
                                                                <a href="{{route('feedback.editFeedback', ['id' => base64_encode($feedback->feedback_id)])}}" class="dropdown-item">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="icon-edit feather"></i>
                                                                        <span class="ms-2">Edit Feedback</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (auth('front')->user()->can('feedback-master-delete'))
                                                            <li>
                                                                <a  href="#" data-action="{{route('feedback.deleteFeedback', ['id' => base64_encode($feedback->feedback_id)])}}" data-id="{{$feedback->feedback_id}}" data class="dropdown-item deletefeedback">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="icon-trash feather"></i>
                                                                        <span class="ms-2">Delete Feedback</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>                                
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <tr>
                                        <th>Question</th>
                                        <th>Question Type</th>
                                        <th>Question Display</th>
                                        <th>Question Number</th>
                                        <th class="text-center">Action</th>
                                    </tr>
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
        <script type="text/javascript">
             $(document).ready(function(){            
                $('.deletefeedback').click(function (e) {
                    var feedback_id = $(this).data('id');
                        Swal.fire({
                        icon: 'warning',
                        title: 'Are you sure you want to delete this record?',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{route('feedback.deleteFeedback')}}",
                            data: {
                                id:feedback_id
                            },
                            success: function (response, textStatus, xhr) {
                            Swal.fire({
                                icon: response.status,
                                title: response.status,
                                text: response.message,
                                showConfirmButton: false,
                                showCancelButton: false,
                                showCloseButton: false,
                                timer: swalModelTimeOut

                            }).then((result) => {
                                setTimeout(() => {
                                    window.location.href = `{{route('feedback.viewFeedback')}}`;
                                }, pageReloadTimeOut);
                            });
                            }
                        });
                        }
                    });
                }); 
            });
        </script>
    @endpush
@endsection