@extends('layouts.main')
@section('title', 'Room Management System')

@section('content')
<style>
#myRMSFloater{
	display: none;
}

</style>
<!-- <div id="myRMSNavigator" class="mx-auto col-md-12" style="text-align: left; margin-top: 1em;"> -->
	<div id="myRMSNavigator" class="mx-auto col-md-12" style="margin-top: 1em;">
	<div class="row">
		<div class="form-group">
			<div class="col-md-4" style="margin-bottom: 1em;">
				
				{{-- <i style="cursor:pointer; float: left; margin-right: 5px; margin-top: 5px; font-size: 25px;"  class="fa fa-tasks" aria-hidden="true"></i> --}}
				<input id="search" class="form-control" type="text" placeholder="Search Room # or Status" name="">
					
			</div>
			<div class="col-md-2" style="margin-bottom: 1em;">
				<select id="area" class="form-control" name="area">
				</select>
			</div>
			<div class="col-md-6" style="margin-bottom: 1em;">
				<button onClick="fullscreen();"class="btn btn-primary" style="font-size: 12px;"><i class="fa fa-arrows-alt"></i> Fullscreen</button>
				<button class="btn btn-warning" style="font-size: 12px;"><i class="fa fa-th"></i> Change View</button>
				<button class="btn btn-success" id="dione" style="font-size: 12px;"><i class="fa fa-refresh"></i> Apply OSK</button>
				<button id="btnroombalancer" class="btn btn-info" onclick="openNav()" style="font-size: 12px;"><i class="fa fa-th"></i> Room Balancer <span id="balancerbadge1" class="badge" style="font-size: 11px;">0</span></button>
				<button id="btnbelo1" class="btn btn-danger" style="font-size: 12px;"><i class="fa fa-th"></i> BELO <span id="belobadge1" class="badge" style="font-size: 11px;">0</span></button>
			</div>
		</div>
	</div>
</div>
<div id="form_body">
	<div class="row">
		<div class="col-lg-12" id="roomFetch" style="background: #fff !important;">

		</div>
	</div>
</div>
<div id="myRMSFloater" class="col-md-2" style="margin-bottom: 1em; position: fixed; right: 0; bottom: 0;">
	<button style="width: 100%;" onClick="unFullscreen();"class="btn btn-primary"><i class="fa fa-arrows-alt"></i> Exit Fullscreen</button>
	<br>
	<br>
	<button style="width: 100%;" class="btn btn-warning"><i class="fa fa-th"></i> Change View</button>
	{{-- <br> --}}
	{{-- <br> --}}
	{{-- <button id="btnbelo2" style="width: 100%;" class="btn btn-danger"><i class="fa fa-th"></i> BELO <span id="belobadge2" class="badge">0</span></button> --}}
</div>

{{-- Modals --}}
@include('modal.roomselect')
@include('modal.forbelo')

{{-- Extenstions --}}
@include('extensions.menu')
@include('extensions.sidebar')

@endsection

@section('page-script')
<script type="text/javascript">
	// console.log(":D");
	var roomId;
	var roomType;
	var roomNo;
	var roomName;
	var rateDesc;
	var rateAmount;
	var rmsLogId;
	
	var ipaddress = "{{ $_SERVER['SERVER_ADDR'] }}";
	var socket = io.connect('http://'+ipaddress+':6969');

	//Logs Variables
	var logsid;

	//Variables Change Status
	var sroomno;

	//Variables Table
	var tblbelo;
	var tblreserve;

	$(document).ready(function(){

		countUp();

		fetchLocal();
		fetchRooms();
        roomInformation();
		LoadBalancerBadge();
		LoadBeloBadge();
		LoadRoomGRP();

		//Set
		tblbelo = $('#tblbelo').DataTable();
		tblreserve = $('#reservedTable').DataTable();

	});

	$(document).on('click', 'button[name="roomid[]"]', function(){

		roomId = $(this).val();
		roomName = $('#roomname').val();

		//Validation For Recovery
		$.ajax({
			url: '{{ url("api/operation/validateispest") }}',
			type: 'get',
			data: {
				roomId: roomId
			},
			dataType: 'json',
			success: function(response){

				if(response.success){

					LoadLoginModal(roomId, roomName);

				}
				else{

					toastr.error(response.message);

				}

			}
		});

	});

	$(document).on('click', '#dione', function(){
		var objWindow = window.open(location.href, "_self");
		objWindow.close();
	});

	$('#search').on('keyup', function(){

		fetchRooms();
		
	});

	function LoadLoginModal(roomId, roomName){

		$.ajax({
			url: '{{ url("api/getcurrentroomstatus") }}',
			type: 'get',
			data:{
				'id': roomId
			},
			dataType: 'json',
			success: function(response){

				sroomno = response.roomInstance.room_no;

				$('#iinfo').text('');
				$('#iinfo').append(' ROOM # ' + response.roomInstance.room_no + ' [<strong>'+ response.roomInstance.current_status +'</strong>] FROM ' + '[' + response.roomInstance.previous_status + ']');

			}
		});

		$.ajax({
			url: '{{ url("api/getcurrentroomstatus") }}',
			type: 'get',
			data:{
				'id': roomId
			},
			dataType: 'json',
			success: function(r){
				
			}
		})

		$.ajax({
			url: '{{ url("api/getPrevRemarks") }}',
			method: 'get',
			dataType: 'json',
			data:{
				'roomId': roomId,
			},
			success:function(r){
				console.log(r);
				$('#remarks').html('');
				$.each(r.lastRemarksOfRoomWeb, function(i, items){
					$('#remarks').append(
							'<p> Notes: ' + items.notes + '<br/>' + ' Area: ' + items.areaName + '<br/>' + ' Component: ' + items.componentName + '<br/>' + ' Remarks: ' + items.remarksName + '<br/>' + ' By: ' + items.name + '<br/>' +' Created At: ' + items.created_at + '</p>' + '<div style="border:1px solid black;"></div>'
						);
				});

			},
			error:function(r){
				console.log(r);
			}
		});

		$('#myRoom').modal('toggle');

	}

	$('#btnbelo1').on('click', function(){

		$('#forbelo').modal('toggle');
		LoadBelo();

	});

	function LoadBelo(){

		tblbelo.destroy();
		tblbelo = $('#tblbelo').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: 'get',
                url: '{{ url("api/loadforbelopercent") }}'
            },
            columns : [
                {data: 'room', name: 'room'},
                {data: 'percent', name: 'percent'},
            ]
        });

	}

	function LoadRoomGRP(){

		$.ajax({
			url: '{{ url("api/loadroomgrp") }}',
			type: 'get',
			dataType: 'json',
			success: function(response){

				$('#sidebarcontent').append(response.content);

			}
		});

	}

	function fetchRooms(){

		var search = $('#search').val();
		var area_id = $('#area').val();

		$.ajax({
			url: '{{ url("api/rooms") }}',
			method: 'GET',
			data: {
				search: search,
				area_id: area_id
			},
			beforeSend: function(){
				$('#roomFetch').hide();
			},
			success:function(response){
				$('#roomFetch').html(response);
			},
			complete: function(){
				$('#roomFetch').fadeIn("slow");
			}
		});

	}

	function fetchLocal(){

		$.ajax({
			url: '{{ url("api/areas") }}',
			type: 'GET',
			dataType: 'json',
			success: function(response){

				$('#area').find('option').remove();
				$('#area').append('<option value="">All</option>');
				for(var i=0;i<response.areas.length;i++){
					$('#area').append('<option value="'+ response.areas[i].id +'">'+ response.areas[i].room_area +'</option>');
				}

			}
		});

	}

	function roomInformation(){
        $.ajax({
            url: '{{ url("api/room/roomInfoAll") }}',
            method: 'GET',
            dataType: 'json',
            success:function(r){
            	// console.log('roominfotest');
            	console.log(r);
				$('#tableRoomInfoBody').find('tr').remove();
            	$.each(r.roomInfo, function(i, items){
            		$('#tableRoomInfoBody').append('<tr><td style="text-align: left;">'+ items.status +'</td><td>'+ items.status_count +'</td></tr>');
            	});
            },
            error:function(r){
            	console.log(r);
            }
        });
    }

	$('#area').on('change', function (e) {
		//goSearch();
		fetchRooms();
	});

	function goSearch(){
		var search = $("#search").val();
		var area = $("#area").val();

		fetchRMS(search, area);
	}

	function fetchRMS(search, area){
		$.ajax({
			url: "{{URL('/')}}/fetchRMS",
			type: "POST",
			data: {
				"_token": "{{ csrf_token() }}",
				"search": search,
				"area": area

			},
			success: function(data){
				$('#form_body').html(data);
				$("[title]").tooltip();
			}
		});
	}

	$('select').on('change', function() {

	})

	function fullscreen(){
		$('#myHeader').hide();
		$('#myFooter').hide();
		$('#myRMSNavigator').hide();
		$('#myRMSFloater').show();
		var docElm = document.documentElement;
		if (docElm.requestFullscreen) {
			docElm.requestFullscreen();
		}
		else if (docElm.mozRequestFullScreen) {
			docElm.mozRequestFullScreen();
		}
		else if (docElm.webkitRequestFullScreen) {
			docElm.webkitRequestFullScreen();
		}
		else if (docElm.msRequestFullscreen) {
			docElm.msRequestFullscreen();
		}
	}

	function unFullscreen(){
		$('#myHeader').show();
		$('#myFooter').show();
		$('#myRMSNavigator').show();
		$('#myRMSFloater').hide();
		if (document.exitFullscreen) {
		document.exitFullscreen();
		}
		else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		}
		else if (document.webkitCancelFullScreen) {
			document.webkitCancelFullScreen();
		}
		else if (document.msExitFullscreen) {
			document.msExitFullscreen();
		}
	}

	function requestFullScreen(element) {
		// Supports most browsers and their versions.
		var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;

		if (requestMethod) { // Native full screen.
			requestMethod.call(element);
		} else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
			var wscript = new ActiveXObject("WScript.Shell");
			if (wscript !== null) {
				wscript.SendKeys("{F11}");
			}
		}
	}

	function LoadBeloBadge(){

		$.ajax({
			url: '{{ url("api/loadbelobadge") }}',
			type: 'get',
			dataType: 'json',
			success: function(response){

				$('#belobadge1').text('');
				$('#belobadge1').text(response.belo_count);

				$('#belobadge2').text('');
				$('#belobadge2').text(response.belo_count);

			}
		})

	}

	function LoadBalancerBadge(){

		$.ajax({
			url: '{{ url("api/loadbalancerbadge") }}',
			type: 'get',
			dataType: 'json',
			success: function(response){

				$('#balancerbadge1').text('');
				$('#balancerbadge1').text(response.balancercount);

				if(response.isblink){
					$('#btnroombalancer').addClass("blink_me");
				}
				else{
					$('#btnroombalancer').removeClass("blink_me");
				}

			}
		})

	}

	//
	function openNav() {
		document.getElementById("mySidenav").style.width = "100%";
	}

	function closeNav() {
		document.getElementById("mySidenav").style.width = "0";
	}
	//

	// sockets
	socket.on('reloadtags', function(data){
		
		var objsize = Object.objsize(data);
		for(var i=0;i<objsize;i++){
			console.log(data[i].roomId + " " + data[i].btnid);
			reload(data[i].roomId, data[i].btnid);		
		}
		roomInformation();
		
	});

	function reload(roomId, buttonId){
		//console.log("pumasok ako dito");
		$.ajax({
			url: '{{ url("api/getRoomStatusAfterSave") }}',
			method: 'get',
			data:{
				'roomId': roomId,
				'buttonId': buttonId,
			},
			dataType: 'json',
			success:function(response){
				console.log(response);
				if(response.getRooms[0].from_room_status_id=="66"){
					$('#imageStatus'+roomId).attr('src', '{{asset("images/statuses/status_inspectrecovery.png")}}?{{\Carbon\Carbon::now()->format("Y-m-dH:i:s")}}');
					$('#roomid'+roomId).css('background', 'pink');
				}
				else{
					$('#imageStatus'+roomId).attr('src', '{{asset("images/statuses/status_")}}'+response.getRooms[0].room_status_id+'.png?{{\Carbon\Carbon::now()->format("Y-m-dH:i:s")}}');
					$('#roomid'+roomId).css('background', response.getRooms[0].color);
				}
				$('#tooltips').text('');
				if(response.getRooms[0].is_name=="1"){ 
					$('#tooltips'+roomId).text(response.getRooms[0].room_status + " By " +  response.getRooms[0].userinfo);
				}
				else{
					$('#tooltips'+roomId).text(response.getRooms[0].room_status);
				}
				console.log('{{ asset("images/statuses/status_") }}' + response.getRooms[0].room_status_id +'.png?{{\Carbon\Carbon::now()->format("Y-m-dH:i:s")}}');
				if(response.getRooms[0].is_blink=="1"){
					
					$('#div'+roomId).addClass("blink_me");

				}
				else{

					$('#div'+roomId).removeClass("blink_me");

				}

				if(response.getRooms[0].is_timer=="1"){
					Timer(roomId);
				}
				else{

					for(var i=0;i<istimer.length;i++){

						if(istimer[i].id==roomId){
							
							istimer = istimer.slice(0, i);
							break;
						}
						
					}

					$('#timer'+roomId).find('td').remove();
					$('#timer'+roomId).append('<td></td>');

				}

				

			},
			error:function(response){
				console.log(response);
			}
		});
	}

	socket.on('cancelledReload', function(data){
		// roomId, prevStatus, currentStatus
		cancelledReload(data.roomId, data.prevStatus, data.currentStatus);
		roomInformation();
	});

	socket.on('belobadge', function(data){

		$('#belobadge1').text('');
		$('#belobadge1').text(data.belo_count);

		// $('#belobadge2').text('');
		// $('#belobadge2').text(data.belo_count);
		
	});

	socket.on('reloadbalancer', function(data){

		var content = '';
		var room = data.balancer['rooms'];

		for(var i=0;i<room.length;i++){
			
			content += '<h3 style="color: white; margin-bottom: 15px;">'+ room[i]['room_no'] +'</h3>';

		}

		content += '<h3 style="color: white;">Clean Percentage: ' + data.balancer['cleanpercent'] +'</h3>';

		$('#'+data.balancer['roomtype']).find('h3').remove();
		$('#'+data.balancer['roomtype']).append(content);

	});

	socket.on('reloadbalancernotif', function(data){

		$('#balancerbadge1').text('');
		$('#balancerbadge1').text(data.balancercount);

		if(data.isblink){
			$('#btnroombalancer').addClass("blink_me");
		}
		else{
			$('#btnroombalancer').removeClass("blink_me");
		}

	});

	// end of sockets

	//Function Get Object Size
	Object.objsize = function(Myobj) {
	    var osize = 0, key;
	    for (key in Myobj) {
	        if (Myobj.hasOwnProperty(key)) osize++;
	    }
	    return osize;
	};

</script>
@endsection
