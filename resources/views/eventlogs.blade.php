@extends('layouts.main')
@section('title', 'Event Logs')

@section('content')

<div id="myRMSNavigator" class="col-md-12" style="margin-top: 1em;">
    {{-- Nothing --}}
</div>
<div id="form_body">


    <div class="col-md-12">

        <div class="col-md-12">

            <button id="btnaddeventreason" name="btnaddeventreason" class="btn btn-flat btn-primary" style="margin-bottom: 10px; float: right;">New Event Reason</button>

        </div>

        <div class="col-md-12">

            <table id="tblevents" class="table table-striped table-bordered" style="width: 100%;">
                <thead>
                  <tr>
                    <th style="vertical-align: middle;">Event</th>
                    <th style="vertical-align: middle;">Remarks</th>
                    <th style="vertical-align: middle;">Created At</th>
                    <th style="vertical-align: middle;">Created By</th>
                    <th style="vertical-align: middle; text-align: center;"><button id="btnnewevent" name="btnnewevent" data-toggle="modal" data-target="#loginevent" class="btn btn-flat btn-success"><i class="fa fa-plus"></i></button></th>
                  </tr>
                </thead>
            </table>

        </div>
        
    </div>

    {{-- Modal --}}
    @include('modal.eventlogslogin')
    @include('modal.newevent')
    @include('modal.eventreason')

</div>


@endsection

@section('page-script')
<script type="text/javascript">

    //Variables
    var userid;
    var tblevents;
    var action;

    $(document).ready(function(){

        LoadEvents();

        //Timer
        countUp();

    });

    $('#btnnewevent').on('click', function(){

        ClearLogin();
        action = "New Event";

    });
    
    $('#btnlogin').on('click', function(){

        Login(action);

    });

    $('#btnsaveevent').on('click', function(){

        SaveEventInformation();

    });

    $('#btnaddeventreason').on('click', function(){

        action = "New Event Reason";
        $('#txtnreason').val('');
        $('#loginevent').modal('toggle');

    });

    $('#btnsavereason').on('click', function(){

        SaveEventReason();

    });

    function SaveEventReason(){

        var reason = $('#txtnreason').val();
        var color = $('#txtncolor').val();

        //Validation
        if(reason==""){
            toastr.error('Please input the reason.');
        }
        else{

            $.ajax({
                url: '{{ url("api/eventlogs/saveeventreason") }}',
                type: 'post',
                data: {
                    reason: reason,
                    color: color
                },
                dataType: 'json',
                success: function(response){

                    if(response.success){

                        toastr.success(response.message);
                        $('#eventreason .close').click();

                    }
                    else{

                        toastr.error(response.message);

                    }

                }
            });

        }

    }

    function LoadEvents(){

        tblevents = $('#tblevents').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
                $(nRow).find('td:eq(0)').css('background-color', aData["color"])
                $(nRow).find('td:eq(3)').attr('colspan', 2)
                // $(nRow).find('td:eq(2)').css('background-color', aData["color"])
                // $(nRow).find('td:eq(3)').css('background-color', aData["color"])
            },
            ajax: {
                type: 'get',
                url: '{{ url("api/eventlogs/loadevents") }}'
            },
            columns : [
                {data: 'event', name: 'event'},
                {data: 'remarks', name: 'remarks'},
                {data: 'createdat', name: 'createdat'},
                {data: 'createdby', name: 'createdby'},
                // {data: 'panel', name: 'panel'},
            ]
        });

    }

    function SaveEventInformation(){

        var reasonid = $('#cmbreason').val();
        var reason = $('#txtreason').val();

        if(reasonid==""){
            toastr.error("Please select a event reason.");
        }
        else if(reason==""){
            toastr.error("Please input your reason.");
        }
        else{

            $.ajax({
                url: '{{ url("api/eventlogs/saveeventinformation") }}',
                type: 'post',
                data: {
                    userid: userid,
                    reasonid: reasonid,
                    reason: reason
                },
                dataType: 'json',
                success: function(response){

                    if(response.success){
                        
                        toastr.success(response.message);
                        $('#newevent .close').click();
                        ReloadEvents();

                    }

                }
            });

        }

    }

    function LoadReasons(){

        $.ajax({
            url: '{{ url("api/eventlogs/loadreasons") }}',
            type: 'get',
            dataType: 'json',
            success: function(response){

                $('#cmbreason').find('option').remove();
                $('#cmbreason').append('<option value="">Select A Reason</option>');
                for(var i=0;i<response.data.length;i++){

                    $('#cmbreason').append('<option value="'+ response.data[i]["id"] +'">'+ response.data[i]["reason"] +'</option>');

                }

            }
        });

    }

    function ClearLogin(){

        $('#txtlusername').val('');
        $('#txtlpassword').val('');

    }

    function Login(action){

        var username = $('#txtlusername').val();
        var password = $('#txtlpassword').val();

        if(username==""){
            toastr.error("Please input your username.");
        }
        else if(password==""){
            toastr.error("Please input your password.");
        }
        else{

            $.ajax({
                url: '{{ url("api/eventlogs/eventlogin") }}' ,
                type: 'get',
                data: {
                    username: username,
                    password: password
                },
                dataType: 'json',
                success: function(response){

                    if(response.success){

                        //Fade Login
                        $('#loginevent .close').click();

                        //Clear
                        ClearEventInformation();

                        //Set
                        userid = response.userinfo.username;
                        
                        if(action=="New Event"){

                            LoadReasons();
                            $('#newevent').modal('toggle');

                        }
                        else if(action=="New Event Reason"){

                            $('#eventreason').modal('toggle');

                        }

                        
                    }
                    else{
                       
                        //Fade Login
                        $('#loginevent .close').click();

                        toastr.error(response.message);

                    }

                }
            });

        }

    }

    function ClearEventInformation(){

        userid = "";
        $('#txtreason').text(''); 

    }

    function ReloadEvents(){

        tblevents.ajax.reload();

    }

</script>
@endsection
