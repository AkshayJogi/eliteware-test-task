@extends('layouts.app')
@section('title', 'Employee')
@section('content')

<!-- navigation -->
<nav class="navbar navbar-inverse">
   <div class="container-fluid">
      <div class="collapse navbar-collapse float-right" id="myNavbar">
         <ul class="nav navbar-nav navbar-right">
            <li class="active">
               <a href="javascript:void(0)" id="btnAddEmployee">Add Employee</a>
            </li>
         </ul>
      </div>
   </div>
</nav>

<!-- table -->
<div class="container-fluid">
   <div class="row content">
      <div class="col-sm-12 text-left mt-2">
         <h4>All Employees</h4>
         Filter By Date  : <input type="text" name="datetimes" id="dateRangePicker" />
         <div class="table-responsive">
            <table id="table" class="table table-bordered dt-responsive  nowrap w-100">
            </table>
         </div>
      </div>
   </div>
</div>

<!-- Modal -->
<div class="modal fade" id="popup-modal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Employee</h4>
         </div>
         <form method="post" id="form" action="javascript:void(0)" enctype="multipart/form-data">
            <div class="modal-body">
               @csrf
               <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div>
                        <div class="mb-3">
                           <label for="example-text-input" class="form-label">First Name</label>
                           <input class="form-control" name="first_name" type="text" id="first_name">
                           <span id="first_name-error" class="error text-danger"></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="mb-3">
                        <label for="example-text-input" class="form-label">Last Name</label>
                        <input class="form-control" name="last_name" type="text"  id="last_name">
                        <span id="last_name-error" class="error text-danger"></span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div>
                        <div class="mb-3">
                           <label for="example-text-input" class="form-label">Logo</label>
                           <input class="form-control" name="joining_date" type="date"  id="joining_date">
                           <span id="joining_date-error" class="error text-danger"></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="mb-3">
                        <label for="example-text-input" class="form-label">Profile Image</label>
                        <input class="form-control" name="image" type="file"  id="image" onclick="this.value=null;">
                        <span id="image-error" class="error text-danger"></span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" id="btnSubmit" class="btn btn-success w-md">Submit</button>
            </div>
         </form>
      </div>
   </div>
</div>

<input type="hidden" id="startDate">
<input type="hidden" id="endDate">

<script>
    var table;

    $(function() {
        getTable();
        $('#dateRangePicker').val('');
        $('#startDate').val('');
        $('#endDate').val('');

        $("input[name='datetimes']").daterangepicker(
          {},
          function (start, end, label) {
            let startDate = start.format("YYYY-MM-DD").toString();
            let endDate = end.format("YYYY-MM-DD").toString();
            $('#startDate').val(startDate);
            $('#endDate').val(endDate);
            table.draw();
          }
        );
    });

    $('#btnAddEmployee').click(function(){
        $('#form')[0].reset();
        $('#popup-modal').modal('show');
    })

    function validateImage(imgElm){
        /* current this object refer to input element */
        var $input = imgElm;
        /* collect list of files choosen */
        var files = $input[0].files;
        var fileSize = files[0].size;
        /* 1024 = 1MB */
        var size = Math.round((fileSize / 1024));
        /* checking for less than or equals to 2MB file size */

        if (size > 2*1024) {
            message('error', 'Only 2MB image file size is allowed.');
            $input.value=null;

            return false;
        }else{
            return true;
        }
    }

    /* this function will call when onchange event fired */
    $("#image").on("change",function(){
        validateImage($(this));
    });

    $('#form').on('submit', function(e) {
        e.preventDefault()

        if($("#image")[0].files.length){
            if(!validateImage($("#image"))){
                return false;
            }            
        }

        showButtonLoader('btnSubmit', 'Submit','disable');
        let formValue = new FormData(this);
        $.ajax({
            type: "post",
            url: "{{ url('save-employee') }}",
            data: formValue,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log('response',response);
                if (response.success) {
                    message('success', response.message);
                    $('#popup-modal').modal('hide');
                    table.draw();
                } else {
                    message('error', response.message);
                }
                showButtonLoader('btnSubmit', 'Submit','enable');
            },
            error: function(response) {
                let error = response.responseJSON;
                if (!error) {
                    error = JSON.parse(response.responseText);
                }

                $.each(error.errors, function( key, value) {
                    $("#"+key+"-error").text(value);
                });

                showButtonLoader('btnSubmit', 'Submit', 'enable');             
            },
        });
    });

    function getTable(startDate = null, endDate = null) {
        table = $('#table').DataTable({
            searching: true,
            processing: true,
            serverSide: true,
            lengthChange: false,
            ajax: {
                url: '{{ url("employee") }}',
                type: "GET",
                data: function(d) {
                    d.start_date = $('#startDate').val(),
                    d.end_date = $('#endDate').val()
                }
            },
            columns: [{
                    data: 'employee_code',
                    name: 'employee_code',
                    title: 'Employee Code.'
                },
                {
                    data: 'first_name',
                    name: 'first_name',
                    title: 'First Name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'last_name',
                    name: 'last_name',
                    title: 'Last Name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'joining_date',
                    name: 'joining_date',
                    title: 'Joining Date',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'image',
                    name: 'image',
                    title: 'Image',
                    orderable: true,
                    searchable: true
                },                
            ],
            error: function(xhr, error, code) {
            },
        });
    }

</script>
@endsection