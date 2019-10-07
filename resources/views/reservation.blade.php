@extends('layouts.mainreservation')
@section('title', 'RMS Reservation')

@section('content')

<div id="myRMSNavigator" class="col-md-12" style="margin-top: 1em;">
    {{-- Nothing --}}
</div>
<div id="form_body">

    <div class="col-md-6 ">
        <h1 style="text-align: left;">Room {{ $room_no }}</h1>
    </div>
    <div class="col-md-6 ">
        <button type="button" class="btn btn-flat btn-success" data-toggle="modal" data-target="#logincalendar" id="reserveRoom" name="reserveRoom" style="float: right; margin-top: 10px;">Make Reservation</button>
    </div>
	<div class="col-md-12">
        <br>
        <div id="calendar">

        </div>
    </div>
    {{-- Modal --}}
    @include('modal.newreservation')
    @include('modal.reservationlogin')
</div>


@endsection

@section('page-script')
<script type="text/javascript">

    //Variables
    var id = "{{ $id }}";
    var room_no = "{{ $room_no }}";
    var calendar;
    var userid;
    var login = "New Reservation";

    //Update
    var id;
    var title;
    var start;
    var end;

    $(document).ready(function(){

        //Timer
        countUp();

        //Calendar
        LoadCalendar();

        //Set
        $('#checkinDateFrom').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('#checkinDateTo').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('#timeInReservation').datetimepicker({
        	format: 'LT'
        });

    });

    $('#reserveRoom').on('click', function(){

        login = "New Reservation";
        ClearLogin();

    });

    function LoadCalendar(){

         calendar = $('#calendar').fullCalendar({
             editable: true,
                header:{
                     left: 'prev, next today',
                     center: 'title',
                     //right: 'month, agendaWeek, agendaDay'
                     right: 'month'
                },
                events: {
                     url: '{{ url("api/reservation/loadreservation") }}',
                     data: {
                         id: id,
                         room_no: room_no
                     },
                },
                eventRender: function(event, element) {
                    
                    element.html('<p style="text-align: center; color: white;">'+event.title+'</p>');

                },
                eventDrop: function(event){ //Grab And Drop Update Event
                    
                    //Set
                    id = event.id;
                    title = event.title;
                    start = moment(event.start).format('YYYY-MM-DD');
                    end = moment(event.end).format('YYYY-MM-DD'); 
                    
                    //Clear
                    ClearLogin();

                    //Set Login Show Login
                    login = "Update Reservation";
                    $('#logincalendar').modal('toggle');
                    
                 }
         });

    }

    $('#reservationSave').on('click', function(){

		var roomtypesReservation = $('#roomtypesReservation').val();
		var checkinDateFrom = $('#checkinDateFrom').val();
        var checkinDateTo = $('#checkinDateTo').val();
        var reservationRate = $('#rateReservation').val();
		var reserveCategories = $('#reserveCategories').val();
		var timeInReservation = $('#timeInReservation').val();
		var fnameReservation = $('#fnameReservation').val();
		var pnumberReservation = $('#pnumberReservation').val();
		var lnameReservation = $('#lnameReservation').val();
		var emailReservation = $('#emailReservation').val();
		var deposit = $('#deposit').val();
		var amount = $('#amount').val();
		var notesForReservation = $('#notesForReservation').val();

		$.ajax({
			url: '{{ url("api/reservation/addreservation") }}',
			method: 'post',
			dataType: 'json',
			data:{
				roomNo: room_no,
				roomtypesReservation: roomtypesReservation,
				checkinDateFrom: checkinDateFrom,
                checkinDateTo: checkinDateTo,
                reservationRate: reservationRate,
				reserveCategories: reserveCategories,
				timeInReservation: timeInReservation,
				fnameReservation: fnameReservation,
				pnumberReservation: pnumberReservation,
				lnameReservation: lnameReservation,
				emailReservation: emailReservation,
				deposit: deposit,
				amount: amount,
				notesForReservation: notesForReservation,
				userInfo: userid
			},
			success:function(response){

                if(response.success){
                    
                    toastr.success(response.message);
                    ReloadReservation();

                    //Fade Reservation
                    $('#reserveCalendar .close').click();

                }

			},
			error:function(response){
				console.log(response);
			}
		});
        
	});

    $('#txtlpassword').on('keyup', function(event){

        if(event.keyCode==13){

            Login();

        }

    });

    $('#btnlogin').on('click', function(){

        Login();

    });

    function Login(){

        if(login=="New Reservation"){

            var username = $('#txtlusername').val();
            var password = $('#txtlpassword').val();
            
            $.ajax({
                url: '{{ url("api/reservation/reslogin") }}',
                type: 'post',
                data: {
                    username: username,
                    password: password
                },
                dataType: 'json',
                success: function(response){

                    if(response.success){

                        //Fade Login
                        $('#logincalendar .close').click();

                        //Load
                        LoadReservationType();
                        LoadReservationCategory();
                        LoadRates();

                        //Set
                        userid = response.username;
                        setTimeout(OpenReserveModal, 1000);
                    
                    }
                    else{

                        //Fade Login
                        $('#logincalendar .close').click();

                        toastr.error(response.message);

                    }

                }
            });

        }
        else if(login=="Update Reservation"){

            var username = $('#txtlusername').val();
            var password = $('#txtlpassword').val();
            
            $.ajax({
                url: '{{ url("api/reservation/reslogin") }}',
                type: 'post',
                data: {
                    username: username,
                    password: password
                },
                dataType: 'json',
                success: function(response){

                    if(response.success){

                        $.ajax({
                            url: '{{ url("api/reservation/updatereservationinformation") }}',
                            type: 'post',
                            data: {
                                id: id,
                                title: title,
                                from: start,
                                to: end
                            },
                            dataType: 'json',
                            success: function(response){

                                if(response.success){

                                    //Fade Login
                                    $('#logincalendar .close').click();

                                    toastr.success("Reservation information has been update.");

                                }

                            }
                        });
                    
                    }
                    else{

                        //Fade Login
                        $('#logincalendar .close').click();
                        ReloadReservation();
                        toastr.error(response.message);

                    }

                }
            });

        }

    }

    $('#btncloselogin').on('click', function(){

        ReloadReservation();

    });

    function OpenReserveModal(){

        $('#reserveCalendar').modal('toggle');

    }

    function LoadReservationType(){

        $.ajax({
            url: '{{ url("api/reservation/loadreservationtype") }}',
            type: 'get',
            dataType: 'json',
            success: function(response){

                $('#roomtypesReservation').find('option').remove();
                $('#roomtypesReservation').append('<option value="">Select Reservation Type</option>');
                for(var i=0;i<response.data.length;i++){

                    $('#roomtypesReservation').append('<option value="'+response.data[i]["id"]+'">'+response.data[i]["title"]+'</option>');

                }

            }
        });

    }

    function LoadReservationCategory(){

        $.ajax({
            url: '{{ url("api/reservation/loadreservationcategory") }}',
            type: 'get',
            dataType: 'json',
            success: function(response){

                $('#reserveCategories').find('option').remove();
                $('#reserveCategories').append('<option value="">Select Category Type</option>');
                for(var i=0;i<response.data.length;i++){

                    $('#reserveCategories').append('<option value="'+response.data[i]["id"]+'">'+response.data[i]["title"]+'</option>');

                }

            }
        });

    }

    function LoadRates(){

        $.ajax({
            url: '{{ url("api/reservation/loadreservationrates") }}',
            type: 'get',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(response){

                $('#rateReservation').find('option').remove();
                $('#rateReservation').append('<option value="">Select Rate Type</option>');
                for(var i=0;i<response.data.length;i++){

                    $('#rateReservation').append('<option value="'+response.data[i]["ID"]+'">'+response.data[i]["RateDesc"]+'</option>');

                }

            }
        });

    }

    function ClearLogin(){

        $('#txtlusername').val('');
        $('#txtlpassword').val('');

    }

    function ReloadReservation(){

        calendar.fullCalendar('refetchEvents');

    }

</script>
@endsection
