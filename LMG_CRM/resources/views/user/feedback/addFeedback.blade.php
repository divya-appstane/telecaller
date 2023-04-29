@extends('user.layouts.master')

@section('main-section')
@push('css')
<style>
    .error{
        color:red;
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
               <div class="breadcrumb-item"><a href="javascript:void(0)"> Feedback </a></div>
               <div class="breadcrumb-item"><a href="{{route('area.addArea')}}"> Add new feedback </a></div>
            </div>
        </div>
        <div class="card">
            <div class="updateStatus"></div>
            <div class="card-body">
                <h4>Add CRM Feedback</h4><br/>
                @if (session()->has('failures'))

                    <table class="table table-danger">
                        <tr>
                            <th>Row</th>
                            <th>Attribute</th>
                            <th>Errors</th>
                            <th>Value</th>
                        </tr>

                        @foreach (session()->get('failures') as $validation)
                            <tr>
                                <td>{{ $validation->row() }}</td>
                                <td>{{ $validation->attribute() }}</td>
                                <td>
                                    <ul>
                                        @foreach ($validation->errors() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    {{ $validation->values()[$validation->attribute()] }}
                                </td>
                            </tr>
                        @endforeach
                    </table>

                @endif
                <form  id="addFeedbackForm" name="addFeedbackForm" novalidate enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col-md-3">
                            <label for="question" class="form-label">Question<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-help-circle feather"></i></span>
                                <textarea class="form-control" id="question" name="question" placeholder="Enter question"></textarea>
                            </div>
                        </div>
                    </div>
                    <span class="question error mb-2"></span>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col-md-3">
                            <label for="question_type" class="form-label">Question Type<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-help-circle feather"></i></span>
                                <select class="form-control" id="question_type" name="question_type">
                                    <option value="">--SELECT--</option>
                                    <option value="Yes/No">Yes/No</option>
                                    <option value="Rating">Rating</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <span class="question_type error mb-2"></span>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col-md-3">
                            <label for="question_display" class="form-label">Question Display<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-eye feather"></i></span>
                                <select class="form-control" id="question_display" name="question_display" onchange="get_last_order()">
                                    <option value="">--SELECT--</option>
                                    <option value="Feedback 1">1st Call</option>
                                    <option value="Feedback 2">After 15 days 2nd Call</option>
                                    <option value="Feedback 3">After 15 days 3nd Call</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <span class="question_display error mb-2"></span>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col-md-3">
                            <label for="question_order" class="form-label">Question Order<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-help-circle feather"></i></span>
                                <input type="number" id="question_order" name="question_order" class="form-control">
                            </div>
                        </div>
                    </div>
                    <span class="question_order error mb-2"></span>
                    
                    <div class="col-12 mt-2">
                        <button type="button" class="btn btn-primary mr-3" id="add_new_feedback">Save</button>
                        <a onclick="javascript: history.back()" class="btn btn-warning ml-2">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/jquery.validate.min.js"></script>
        <script src="{{env('USER_ASSETS')}}vendors/jquery-validation/additional-methods.min.js"></script>
        <script>
            $(document).ready(function(){
                $.validator.addMethod('filesize', function (value, element, limit) {
                    limit = limit * 1024 * 1024;
                    return !element.files[0] || (element.files[0].size <= limit);
                }, 'File size must be less than {0} MB');

                $('#addFeedbackForm').validate({
                    ignore: "ignore",
                    rules: {
                        question: {
                            required: true,
                        },
                        question_type:{
                            required: true,
                        },
                        question_display: {
                            required : true,
                        },
                        question_order : {
                            required : true,
                        }
                    },
                    messages: {
                        question: {
                            required: "Question field is required."
                        },
                        question_type: {
                            required: "Please select atleast one question type.",
                        },
                        question_display: {
                            required: "Please select atleast one question status.",
                        },
                        question_order: {
                            required: "Question Order field is required.",
                        },
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    errorPlacement: function(error, element) {
                        if(element.attr("name") == "question") {
                            $('.question').html(error);
                        } else if(element.attr("name") == "question_type") {
                            $('.question_type').html(error);
                        } else if(element.attr("name") == "question_display") {
                            $('.question_display').html(error);
                        } else if(element.attr("name") == "question_order") {
                            $('.question_order').html(error);
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });

                $('#add_new_feedback').on('click', function() {
                    const valid = $('#addFeedbackForm').valid();

                    if(valid){
                        var fd = new FormData($('#addFeedbackForm')[0]);

                        $.ajax({
                            url:"{{route('feedback.storeFeedback')}}",  
                            method:"POST",  
                            data:fd,  
                            contentType:false, 
                            processData:false,  
                            cache: false,
                            success: function(response){
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
                                    window.location.href = `{{route('feedback.viewFeedback')}}`;
                                }, pageReloadTimeOut);
                            },
                            error: function(errors) {
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
                            },
                        });
                    }
                });
            });


            function get_last_order(){
				
				var question_display = $("#question_display").val();
				
				$.ajax({
					
					type: "POST",
					url: `{{route('feedback.changeOrder')}}`,
					data: {question_display:question_display},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: function(data){
						$("#question_order").val(data);
					}
					
					
				});
				
			}

        </script>
    @endpush
@endsection