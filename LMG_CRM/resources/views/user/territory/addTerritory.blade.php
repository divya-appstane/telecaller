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
                    <div class="breadcrumb-item"><a href="javascript:void(0)"> Territory </a></div>
                    <div class="breadcrumb-item"><a href="{{route('territory.viewTerritory')}}"> Add New Territory </a></div>
            </div>
        </div>
        <div class="card">
            <div class="updateStatus"></div>
            <div class="card-body">
                <h4>Assign Territory</h4><br/>
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
                <form class="row" id="addTerritoryForm" name="addTerritoryForm" novalidate enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="tfile" class="form-label">Select Image<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-upload feather"></i></span>
                                <input type="file" class="form-control" id="tfile" name="tfile" accept="image/*" />
                            </div>
                            <span class="tfile error mb-2"></span>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">
                            <label>Enter Route:<span class="text-danger">&nbsp;*</span></label>
                        </div>
                    </div>
                    <div class="control-group mt-2">
                        <div class="control-label"></div>
                        <div class="controls">
                            <table id="dataTable">
                                <tr>
                                    <td>
                                        <div class="d-flex gap-3 align-items-center">
                                        <div class="d-flex gap-3 py-2 ">
                                            <div class="">
                                                <input type="textbox" name="txtfrm[]" id="txtfrm[]" placeholder="From" class="form-control">
                                            </div>
                                            <div class="">
                                                <input type="textbox" name="txtto[]" id="txtto[]" placeholder="To" class="form-control">
                                            </div>
                                        </div>
                                        
                                        </div>
                                    </td>
                                    <input name="cnt" id="cnt"  type="hidden"  readonly value="1" />
                                </tr>
                            </table>
                            <button type="button" class="btn btn-success btn-mini"  onClick="addRow('dataTable')"> Add Row</button>
                            <button type="button" class="btn btn-danger btn-mini"  onClick="deleteRow('dataTable')"> Delete Row </button>
                            
                        </div>
                        <span class="txtfrm error mb-2"></span>
                        <span class="txtto error mb-2"></span>
                    </div>
                   

                    <div class="control-group mt-2">
                        <div class="control-label">
                            <label>Enter Pincodes:<span class="text-danger">&nbsp;*</span></label>
                        </div>
                    </div>
                    <div class="control-group mt-2">
                        <div class="control-label"></div>
                        <div class="controls">
                            <table id="dataTable2">
                                <tr>
                                    <td>
                                        <div class="py-2">
                                            <input type="textbox" name="txtpin[]" placeholder="Pincode" id="txtpin[]" class="span3 form-control">
                                        </div>
                                    </td>
                                    <input name="cnt2" id="cnt2"  type="hidden"  readonly value="1" />
                                </tr>
                            </table>
                            <div class="mt-2">
                                <button type="button" class="btn btn-success btn-mini" onClick="addRow2('dataTable2')"> Add Row</button>
                                <button type="button" class="btn btn-danger btn-mini" onClick="deleteRow2('dataTable2')"> Delete Row </button>
                            </div>
                        </div>
                        <span class="txtpin error mb-2"></span>
                    </div>

                     <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="send_to" class="form-label">Send To<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-user feather"></i></span>
                                <select class="form-control" id="send_to" name="send_to">
                                    <option value="">--SELECT--</option>
                                    @foreach($emp as $e)
                                        <option value="{{$e->empusrid}}">
                                            {{$e->first_nm." ".$e->last_nm}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="send_to error mb-2"></span>
                        </div>
                    </div>

                     <div class="row">
                        <div class="col-md-6">
                            <label for="area_id" class="form-label">Area<span class="text-danger">&nbsp;*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-clock feather"></i></span>
                                <select class="form-control" id="area_id" name="area_id">
                                    <option value="">--SELECT--</option>
                                    @foreach($areas as $area)
                                        <option value="{{$area->id}}">
                                            {{$area->area_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="area_id error mb-2"></span>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-2">
                        <button type="button" class="btn btn-primary mr-3" id="update_lead">Save</button>
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

                // jQuery(function ($) {
                $('#addTerritoryForm').validate({
                    rules: {
                        tfile: {
                            required: true,
                            extension: "jpg,png,jpeg",
                            filesize: 2,
                        },
                        'txtfrm[]': {
                            required: true,
                        },
                        'txtto[]': {
                            required: true,
                        },
                        'txtpin[]': {
                            required: true,
                            digits: true,
                            minlength: 6,
                            maxlength: 6,
                        },
                        send_to: {
                            required: true,
                        },
                        area_id: {
                            required: true,
                        },
                    },
                    messages: {
                        tfile: {
                            required: "Please select the file to be uploaded",
                            extension: "Only image files supported",
                        },
                        'txtfrm[]': {
                            required: "Route source must be required",
                        },
                        'txtto[]': {
                            required: "Route destination must be required",
                        },
                        'txtpin[]': {
                            required: "Pincode must be required",
                            digits: "Your Pincode must be numbers!",
                            minlength: "Your Pincode must be 6 numbers!",
                            maxlength: "Your Pincode must be 6 numbers!",
                        },
                        send_to: {
                            required: "Please select the employee to be added",
                        },
                        area_id: {
                            required: "Please select the area to be added",
                        },
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    errorPlacement: function(error, element) {
                        if(element.attr("name") == "tfile") {
                            $('.tfile').html(error);
                        } else if(element.attr("name") == "txtfrm") {
                            $('.txtfrm').html(error);
                        }else if(element.attr("name") == "txtto") {
                            $('.txtto').html(error);
                        }else if(element.attr("name") == "txtpin") {
                            $('.txtpin').html(error);
                        } else if(element.attr("name") == "send_to") {
                            $('.send_to').html(error);
                        } else if(element.attr("name") == "area_id") {
                            $('.area_id').html(error);
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
                // });

                $('#update_lead').on('click', function() {
                    const valid = $('#addTerritoryForm').valid();

                    if(valid){
                        var fd = new FormData($('#addTerritoryForm')[0]);
                        // fd.append('upload_lead_file', $('#upload_lead_file').files);

                        $.ajax({
                            url:"{{route('territory.storeTerritory')}}",  
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

                                if(response.status == 'success'){
                                    setTimeout(() => {
                                        window.location.href = `{{route('territory.viewTerritory')}}`;
                                    }, pageReloadTimeOut);
                                }

                            },
                            error: function(errors) {
                                if( data.status === 422 ) {
                                    var errors = $.parseJSON(data.responseText);
                                    $.each(errors, function (key, value) {
                                        $('.err').removeClass("d-none");

                                        if($.isPlainObject(value)) {
                                            $.each(value, function (key, value) { 
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

        </script>

        <script type="text/javascript">
            var cnt = 1;
            function addRow(tableID)
            {
                cnt++;
                document.getElementById('cnt').value=cnt;
                
                var table = document.getElementById(tableID);
                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
                
                var colCount = table.rows[0].cells.length;
                
                for(var i=0; i<colCount; i++) 
                {
                    var newcell = row.insertCell(i);
                    newcell.innerHTML = table.rows[0].cells[i].innerHTML;
                    switch(newcell.childNodes[0].type) 
                    {
                        case "text":
                                newcell.childNodes[0].value = "";
                                break;
                        case "text":
                                newcell.childNodes[0].value = "";
                                break;
                        case "checkbox":
                                newcell.childNodes[0].checked = false;
                                break;
                        case "select-one":
                                newcell.childNodes[0].selectedIndex = 0;
                                break;
                    }
                }
            }

            function deleteRow(tableID)
            {
                try 
                {
                    var table = document.getElementById(tableID);
                    var rowCount = table.rows.length;
                    
                    var cnt=document.getElementById('cnt').value;
	
                    if(rowCount <= 1) {
                        cnt=1;
                        document.getElementById('cnt').value=cnt;
                        Swal.fire({
                            title: "Error",
                            text: "Cannot delete all rows",
                            icon: "error",
                            showConfirmButton: false,
                            showCancelButton: false,
                            showCloseButton: false,
                            timer: swalModelTimeOut
                         });
                    }
                    else
                    {
                        table.deleteRow(cnt-1);
                        rowCount--;
                        document.getElementById('cnt').value=rowCount;
                    }
		
                }catch(e) {
                    alert(e);
	            }
            }
        </script>
        <script type="text/javascript">
            var cnt2=1;
            function addRow2(tableID) 
            {
                cnt2++;
                document.getElementById('cnt2').value=cnt2;
                
                var table = document.getElementById(tableID);
                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
                
                var colCount = table.rows[0].cells.length;
                
                for(var i=0; i<colCount; i++) 
                {

                    var newcell = row.insertCell(i);
                    
                    newcell.innerHTML = table.rows[0].cells[i].innerHTML;
                    switch(newcell.childNodes[0].type) 
                    {
                        case "text":
                                newcell.childNodes[0].value = "";
                                break;
                        case "text":
                                newcell.childNodes[0].value = "";
                                break;
                        case "checkbox":
                                newcell.childNodes[0].checked = false;
                                break;
                        case "select-one":
                                newcell.childNodes[0].selectedIndex = 0;
                                break;
                }
            }
	    }

        function deleteRow2(tableID) 
        {
            try
            {
                var table = document.getElementById(tableID);
                var rowCount = table.rows.length;
                
                var cnt2=document.getElementById('cnt2').value;
            
                    if(rowCount <= 1) 
                    {
                        cnt2=1;
                        document.getElementById('cnt2').value=cnt2;
                        Swal.fire({
                            title: "Error",
                            text: "Cannot delete all rows",
                            icon: "error",
                            showConfirmButton: false,
                            showCancelButton: false,
                            showCloseButton: false,
                            timer: swalModelTimeOut
                         });
                    }
                    else
                    {
                        table.deleteRow(cnt2-1);
                        rowCount--;
                        document.getElementById('cnt2').value=rowCount;
                    }
            
            }catch(e) {
                alert(e);
            }
        }
        </script>
    @endpush
@endsection