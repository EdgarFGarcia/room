<div class="modal fade" id="myRoom" role="dialog" style="text-align: left;" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-xl">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h3 class="modal-title"><i id="iinfo" class="fa fa-tasks"> </i></h3>
	    </div>
	    <div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-4">
						<h3>Login Details</h3>
					</div>
				</div>
			</div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-6">
              <p>Username</p>
              <input type="text" autocomplete="off" class="form-control" id="username" name="username" placeholder="Username" required/>
            </div>
            <div class="col-md-6">
              <p>Password</p>
              <input type="password" class="form-control" id="password" name="password" placeholder="Password" required/>
            </div>
          </div>
        </div>
	    </div>
	    <div class="modal-footer">
        	<button type="button" class="btn btn-success" id="login" data-dismiss="modal">Login</button>
        	<button type="button" class="btn btn-info" id="roomStatus" data-dismiss="modal">See Room Detail</button>
	      	<button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>
	    </div>
	  </div>

	</div>
</div>

<div class="modal fade" id="seeRoomDetails" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Room Details</h1>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-6">

								<div class="col-md-12" style="background-color: #323232; margin-bottom: 10px; padding:10px;">
									<h3 id="roomtype" style="color: #fff; font-family: 'Neuton', sans-serif;"></h3>
									<label id="roomName" style="color: #fff; font-family: 'Neuton', sans-serif;"></label><br/>
									<label id="roomNo" style="color: #fff; font-family: 'Neuton', sans-serif;"></label><br/>
								</div>

								<!-- <hr style="width: 100%; background-color: black; height: 1px; border-color : transparent;" /> -->
								

								<div class="col-md-12" style="padding:5px;">
									<ul class="nav nav-tabs nav-justified">
									  <li class="active"><a data-toggle="tab" class="remarks" href="#remarks">Historical Inspection</a></li>
									  <li><a data-toggle="tab" href="#inventory">Inventory</a></li>
									  <li><a data-toggle="tab" href="#menu2">Reservation</a></li>
									</ul>
									<div class="tab-content container-fluid">

									  <div id="remarks" class="tab-pane fade in active" style="height: 300px;
    										overflow-y: scroll;">
									    
									  </div>

									  <div id="inventory" class="tab-pane fade">
									  	<div class="col-md-12 row">
										    <table class="table table-bordered table-condensed" id="inventoryTable" style="width: 100%;">
										    	<thead>
										    		<tr>
										    			<th>Item</th>
										    			<th>QTY</th>
										    			<th>Brand Desc</th>
										    		</tr>
										    	</thead>
										    </table>
									    </div>
									  </div>

									  <div id="menu2" class="tab-pane fade">
									    <div class="col-md-12 row">
									    	<table class="table table-bordered table-condensed" id="reservedTable" style="width: 100%;">
									    		<thead>
									    			<tr>
									    				<th>Date</th>
									    				<th>Time</th>
									    				<th>Hours</th>
									    				<th>Notes</th>
									    			</tr>
									    		</thead>
									    	</table>
									    </div>
									  </div>

									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class="col-md-12" style="padding:5px;">
									<ul class="nav nav-tabs nav-justified">
									  <li class="active"><a data-toggle="tab" class="current" href="#currebt">Current</a></li>
									  <li><a data-toggle="tab" href="#today">Today</a></li>
									  <li><a data-toggle="tab" href="#history">History</a></li>
									</ul>
									<div class="tab-content container-fluid">

									  <div id="currebt" class="tab-pane fade in active text-left">
									    <label>TASK</label>
									    <p id="task"></p>

									    <label>Date</label>
									    <p id="dateCurrent"></p>

									    <label>Assigned By:</label>
									    <p id="assignedBy"></p>

									    <label>Source:</label>
									    <p id="source"></p>
									  </div>

									  <div id="today" class="tab-pane fade text-left statusToday" style="overflow-y: scroll; overflow-x: hidden; max-height: 350px;">
										<!-- <p id="statusToday"></p> -->
									  </div>

									  <div id="history" class="tab-pane fade">
									   	<div class="col-md-12" style="margin-top: 15px;">
									   		<div class="col-md-3">
									   			<select name="statusHistory" id="statusHistory" class="form-control">
									   				
									   			</select>
									   		</div>
									   		<div class="col-md-3">
									           	<input type="text" class="form-control" id="startDateHistory" />
									   		</div>
									   		<div class="col-md-3">
									   			<input type="text" id="endDateHistory" class="form-control"/>
									   		</div>
									   		<div class="col-md-3">
									   			<button type="button" id="goHistory" class="btn btn-info">Go</button>

									   		</div>
									   		<div id="historyRecord" class="text-left">

									   		</div>
									   	</div>

									   </div>

									</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-info" data-toggle="modal" data-target="#reserveCalendar" data-dismiss="modal">Make Reservation</button> -->
				{{-- <button type="button" class="btn btn-info" data-toggle="modal" id="reserveRoom" data-dismiss="modal">Make Reservation</button> --}}
				{{-- <div id="reservationbtn"> --}}
				<a href="#" class="btn btn-info" id="reserveRoom" target="_blank">Make Reservation</a>
				{{-- </div> --}}
		      	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>
</div>



<div class="modal fade" id="changeStatusModal" role="dialog" style="text-align: left;" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-xl">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <h3 id="h3info" class="modal-title"></h3>
	      <input type="hidden" value="" id="userInfo"/>
	    </div>
	    <div class="modal-body">
			<div class="row">
				<div class="container-fluid">
					<div class="col-md-12">
						<div class="col-md-4">
							<h3>Change Status To:</h3>
						</div>
						<div class="col-md-8">
							<select name="status" id="status" class="form-control">

							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="hidden" id="inner">
				<br/><div style="border: 1px solid gray; width: 100%;"></div><br/>
				<h3>Check In Details</h3>
				<br/><br/>
				<div class="row">
					<div class="col-md-12">
						<!-- <p id="warning"></p> -->
						<div class="col-md-4">
							<label>Hours</label><br/>
						</div>
						<div class="col-md-8">
							<select name="rates" id="rates" class="form-control" style="width: 100%;">
								
							</select>
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4">
							<label>Nationality</label>
						</div>
						<div class="col-md-8">
							<select name="nationality" id="nationality" class="form-control" style="width: 100%;">

							</select>		
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-12">
						<!-- <p id="warning"></p> -->
						<div class="col-md-4">
							<label>Market Source</label>
						</div>
						<div class="col-md-8">
							<select name="marketsource" id="marketsource" class="form-control" style="width: 100%;">

							</select>
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4">
							<label>Vehicle Type</label><br/>
						</div>
						<div class="col-md-8">
							<select name="vehicletype" id="vehicletype" class="form-control" style="width: 100%;">
								
							</select>
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4">
							<label>Car</label><br/>
						</div>
						<div class="col-md-8">
							<select name="car" id="car" class="form-control" style="width: 100%;">
								
							</select>
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4">
							<label>Plate Number:</label><br/>
						</div>
						<div class="col-md-8">
							<input type="text" id="platenumber" name="platenumber" class="form-control"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<br/><div style="border: 1px solid gray; width: 100%;"></div><br/>
						<div class="col-md-4">
							<label>Remarks:</label><br/>
						</div>
						<div class="col-md-8">
							<textarea id="remarkscheckin" name="remarkscheckin" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</div>
	    </div>
	    <div class="modal-footer">
		<button type="button" class="btn btn-success" id="saveStatus">Change Status</button>
		<!-- <button type="button" class="btn btn-info" id="roomStatus">See Room Detail</button> -->
	    <button id="btncancel" name="btncancel" class="btn btn-info btn-flat hidden">Cancel Transaction</button>
        {{-- <button type="button" id="previousStatus" class="btn btn-info hidden" data-dismiss="modal">Cancel</button> --}}
	    <button type="button" id="cancelTransaction" class="btn btn-danger" data-dismiss="modal">Close</button>
        
	    </div>
	  </div>

	</div>
</div>

<script>
	// Login
	var userInfo;
	var id;
	
	//Timer
	var istimer = [];

	//Selected Status
	var toStatus = 0;

	//Bool
	var isnego = false;

	var currentRoomStatus;

	$(document).ready(function(){

		LoadSelect2();

	});

	// status

	$(document).on('click', '#roomStatus', function(){
		$('#historyRecord').html('');
		$('#historyTable tbody').empty();
		$.ajax({
			url: '{{ url('api/tagboard/getRoomDetails') }}',
			method: 'POST',
			dataType: 'json',
			data:{
				'roomId': roomId,
			},
			success:function(r){

				setTimeout(function() {
					$('#seeRoomDetails').modal("toggle");
				}, 500);

				var url = '{{ url("/reservation/") }}';
				$('#reserveRoom').attr('href', url + "/" + roomId);

				$('#roomtype').text(r.roomDetail.room_type);
				$('#roomName').text(r.roomDetail.room_description);
				$('#roomNo').text("Room #: " + r.roomDetail.room_no);
				loadInventory();
				loadReservation();
				loadCurrent();
				loadToday();
				loadHistoryRoom();
				// console.log(r);
			},
			error:function(r){
				console.log(r);
			}
		});

	});

	$(document).on('click', '#reservationSave', function(){

		var roomtypesReservation = $('#roomtypesReservation').val();
		var checkinDate = $('#checkinDate').val();
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
			url: '{{ url('api/reservation/addReservation') }}',
			method: 'POST',
			dataType: 'json',
			data:{
				'roomNo': roomNo,
				'roomtypesReservation': roomtypesReservation,
				'checkinDate': checkinDate,
				'reserveCategories': reserveCategories,
				'timeInReservation': timeInReservation,
				'fnameReservation': fnameReservation,
				'pnumberReservation': pnumberReservation,
				'lnameReservation': lnameReservation,
				'emailReservation': emailReservation,
				'deposit': deposit,
				'amount': amount,
				'notesForReservation': notesForReservation,
				'userInfo': userInfo
			},
			success:function(r){
				console.log(r);
			},
			error:function(r){
				console.log(r);
			}
		});
		
	});

	$(document).on('click', '#goHistory', function(){
		var statusHistory = $('#statusHistory').val();
		var startDateHistory = $('#startDateHistory').val();
		var endDateHistory = $('#endDateHistory').val();

		$.ajax({

			type: 'POST',
			url: '{{ url('api/room/history/post') }}',
			datatype: 'json',
			data:{
				'roomId': roomId,
				'startDateHistory': startDateHistory,
				'endDateHistory': endDateHistory,
				'statusHistory': statusHistory
			},
			success:function(r){
				console.log(r);
				$('#historyRecord').css("position", "absolute");
				$('#historyRecord').css("top", "40px");
				$('#historyRecord').css("left", "35px");
				$('#historyRecord').css("width", "100%");
				$('#historyRecord').css("overflow-y", "scroll");
				$('#historyRecord').css("max-height", "350px");
				$('#historyRecord').html('');
				for(var i = 0; i < r.historyLogs.length; i++){
					// console.log(r.historyLogs[i]["fname"]); 
					$('#historyRecord').append("Start Status: " + "<b>" + r.historyLogs[i]["startStat"] + "</b>" + " By: " + "<b>" + r.historyLogs[i]["lastname"] + ", " 
						+ r.historyLogs[i]["firstname"] + "</b>" + "<br/>" + "Date: " + "<b>" + r.historyLogs[i]["startDate"] + "</b>" + "<br/>" +
						"Source: " + "<b>" + r.historyLogs[i]["sSource"] + "</b>" + "<br/>" + 
						"End Status: " + "<b>" + r.historyLogs[i]["endStat"] + "</b>" + " By: " + "<b>" + r.historyLogs[i]["elastname"] + ", " + r.historyLogs[i]["efirstname"] + "</b>" + "</b>" + "<br/>" +
						"Date: " + "<b>" + r.historyLogs[i]["endDate"] + "</b>" + "</br>" + 
						"Source: " + "<b>" + r.historyLogs[i]["eSource"] +"</b>" + "</br>" + 
						"<div style='border:1px solid black;'>" + "</div>");
				}
			},
			error:function(r){

			}

		});

	});

	$('#password').on('keyup', function(event){

		if(event.keyCode==13){

			Login();
			$('#myRoom .close').click();
			toStatus = 0;
			$('#inner').addClass("hidden");

		}

	});

	$(document).on('click', '#login', function(){

		Login();
		toStatus = 0;
		$('#inner').addClass("hidden");

	});

	$(document).on('click', '#previousStatus', function(){
		$.ajax({
			url: '{{ url("api/getPrevStatus") }}',
			method: 'post',
			dataType: 'json',
			data: {
				'roomId': roomId,
				'userInfo': userInfo
			},
			success: function(r){

				// console.log(r);
				// toastr.success(r.message);
				// console.log(r.prevStat.id + " " + r.prevStat.from_room_status_id);
				cancelledReload(r.prevStat.id, r.prevStat.from_room_status_id, r.prevStat.room_status_id);
				socket.emit('cancelledReload', {
					'roomId': r.prevStat.id,
					'prevStatus': r.prevStat.from_room_status_id,
					'currentStatus': r.prevStat.room_status_id
				});

			},
			error: function(r){
				// console.log(r);
			}

		});
	})

	// $(document).on('change', '#status', function(){

	// 	id = $(this).val();
	// 	toStatus = $('#status').val();
	// 	// console.log(roomId + " " + toStatus);
	// 	// console.log(userInfo);
	// 	if(id == "20"){

	// 		//Clear
	// 		ClearOnNegoInformation();

	// 		$.ajax({
	// 			url: '{{ url("api/changeStatusOfRoom") }}',
	// 			method: 'POST',
	// 			data:{
	// 				'roomId': roomId,
	// 				'toStatus': toStatus,
	// 				'roomNo': roomNo,
	// 				'userInfo': userInfo
	// 			},
	// 			dataType: 'json',
	// 			success:function(response){

	// 				console.log(response);
	// 				toastr.success("Updating Successful");
					
					
	// 			},
	// 			error:function(response){
	// 				console.log(response);
	// 			}
	// 		});


	// 		$.ajax({
	// 			url: '{{ url("api/getRoomTypeEtc") }}',
	// 			method: 'post',
	// 			dataType: 'json',
	// 			data:{
	// 				'roomType': roomType,
	// 				'roomNo': roomNo,
	// 			},
	// 			success:function(r){
	// 				console.log(r);

	// 				// rateDesc = items.RateDesc;
	// 				// rateAmount = items.Amount;
	// 				var ratescount = 1;
	// 				var nationalitycount = 1;
	// 				var marketsourcecount = 1;
	// 				var vehiclecount = 1;

	// 				$('#rates').html('');
	// 				$('#nationality').html('');
	// 				$('#marketsource').html('');
	// 				$('#vehicletype').html('');
	// 				$('#car').html('');

	// 				// $('#rates').append('<option value=""></option>');
	// 				$.each(r.getRates, function (i, items){
	// 					if(ratescount==1){
	// 						$('#rates').append($('<option>', {
	// 							value: items.ID,
	// 							text: items.RateDesc + " - Price: " + items.Amount,
	// 							selected: true
	// 						}));
	// 					}
	// 					else{
	// 						$('#rates').append($('<option>', {
	// 							value: items.ID,
	// 							text: items.RateDesc + " - Price: " + items.Amount
	// 						}));
	// 					}
	// 					ratescount ++;
	// 				});

	// 				// $('#nationality').append('<option value="1" selected="selected">Filipino</option>');
	// 				$.each(r.nationality, function (i, items){
	// 					if(nationalitycount==1){
	// 						$('#nationality').append($('<option>', {
	// 							value: items.id,
	// 							text: items.name,
	// 							selected:  true
	// 						}));
	// 					}
	// 					else{
	// 						$('#nationality').append($('<option>', {
	// 							value: items.id,
	// 							text: items.name
	// 						}));
	// 					}

	// 					nationalitycount++;
	// 				});

	// 				// $('#marketsource').append('<option value="1" selected="selected">Private</option>');
	// 				$.each(r.marketSource, function(i, items){
	// 					if(marketsourcecount==1){
	// 						$('#marketsource').append($('<option>', {
	// 							value: items.id,
	// 							text: items.MarketSource,
	// 							selected: true
	// 						}));
	// 					}
	// 					else{
	// 						$('#marketsource').append($('<option>', {
	// 							value: items.id,
	// 							text: items.MarketSource
	// 						}));
	// 					}
	// 					marketsourcecount++;
	// 				});

	// 				// $('#vehicletype').append('<option value="1" selected="selected">Private</option>');
	// 				$.each(r.vehicleType, function(i, items){
	// 					if(vehiclecount==1){
	// 						$('#vehicletype').append($('<option>',{
	// 							value: items.id,
	// 							text: items.name,
	// 							selected: true
	// 						}));
	// 					}
	// 					else{
	// 						$('#vehicletype').append($('<option>',{
	// 							value: items.id,
	// 							text: items.name
	// 						}));
	// 					}
	// 					vehiclecount++;
						
	// 				});
	// 				$('#car').append('<option value="">SELECT</option>');
	// 				$.each(r.carMake, function(i, items){
	// 					$('#car').append($('<option>', {
	// 						value: items.id,
	// 						text: items.CarMake
	// 					}));
	// 				});
	// 			},
	// 			error:function(r){
	// 				console.log(r);
	// 			}
	// 		});
			
	// 		$('#inner').removeClass("hidden");
	// 		// $(document).on('click', '#saveStatus', function(){
				
	// 		// });
	// 	}else{
	// 		$('#inner').addClass("hidden");
	// 	}

	// });


	//onclick of save change status whatever it is
	// $(document).on('click', '#saveStatus', function(){
	// 	// console.log(id);
	// 	// console.log("test");
	// 	console.log(id);
	// 	if(id == "20"){
	// 		checkin();
	// 	}
	// 	// else if(id === undefined){
	// 	// 	toastr.success("Status Empty");
	// 	// 	$('#changeStatusModal').modal('hide');
	// 	// }
	// 	else{
	// 		saveStats();
	// 	}
				
	// });

	// GELO ===========================================================

	$(document).on('change', '#status', function(){

		id = $(this).val();
		toStatus = $('#status').val();

		if(toStatus == "20"){

			//Clear
			ClearOnNegoInformation();
			
			//Update
			OnNegoChange();

			//Set
			GetCheckInInformation();
			isnego = true;
			
			$('#inner').removeClass("hidden");

		}else{
			$('#inner').addClass("hidden");
			//isnego = false;
		}

	});


	$(document).on('click', '#saveStatus', function(){

		if($('#status').val()=="" || $('#status').val()==null){
			
			if(isnego){
				CheckIn();
			}
			else{
				toastr.error("Please select a status.");
			}

		}
		else{

			if(toStatus == "20"){
				CheckIn();
			}
			else{
				ChangeStatus();
			}

		}

				
	});

	$(document).on('click', '#cancelTransaction', function(){

		if(toStatus==20 || isnego==true){
			
			CancelOnNego();

		}

	});

	$('#btncancel').on('click', function(){

		if(toStatus==20 || isnego==true){
			
			CancelOnNego();

		}
		else{

			if( (currentRoomStatus==19) || (currentRoomStatus==13) || (currentRoomStatus==4) || (currentRoomStatus==6) || (currentRoomStatus==22) || (currentRoomStatus==24) || (currentRoomStatus==51) || (currentRoomStatus==40) ){

				CancelOnGoings();

			}

		}

	});

	$('#marketsource').on('change', function(){

		var marketsource = $(this).select2('data');

		if(marketsource[0].text=="Taxi"){


			$.ajax({
				url: '{{ url("api/operation/getvehicleid") }}',
				type: 'get',
				data: {
					marketsource: marketsource[0].text
				},
				dataType: 'json',
				success: function(response){

					$('#vehicletype').val(response.vehicleid).trigger("change");
					$('#car').val(response.carid).trigger("change");

				}
			});
	

		}
		

	});

	function CancelOnGoings(){

		$.ajax({
			url: '{{ url("api/operation/cancelongoings") }}',
			type: 'post',
			data: {
				roomId: roomId,
				userInfo: userInfo,
				currentRoomStatus: currentRoomStatus
			},
			dataType: 'json',
			success: function(response){

				if(response.success){

					toastr.success(response.message);
					$('#changeStatusModal').modal('hide');

				}

			}
		});

	}

	function CheckIn(){

		var rates = $('#rates').val();
		var marketsource = $('#marketsource').val();
		var vehicletype = $('#vehicletype').val();
		var car = $('#car').val();
		var platenumber = $('#platenumber').val();
		var nationality = $('#nationality').val();
		var remarks = $('#remarkscheckin').val();

		var letters = /[^a-zA-Z0-9 ]/g; //Alpha Numeric
		
		
		//Validation
		if(marketsource=="1"){ //Private
			
			if(rates=="" || rates==null){
				toastr.error("Please select a rate.");
			}
			else if(car==""){
				toastr.error("Please select brand of car.");
			}
			else if(platenumber==""){
				toastr.error("Please input the platenumber.");
			}
			else if(platenumber.match(letters)){
				toastr.error("Please input alpha numeric only.");
				$('#platenumber').val('');
				$('#platenumber').focus();
			}
			else if(remarks.match(letters)){
				toastr.error("Please input alpha numeric only.");
				$('#remarkscheckin').val('');
				$('#remarkscheckin').focus();
			}
			else{

				if(remarks==""){
					remarks = "None";
				}

				$.ajax({
					url: '{{ url("api/operation/checkin") }}',
					method: 'post',
					dataType: 'json',
					data:{
						roomId: roomId,
						toStatus: toStatus,
						userInfo: userInfo,
						rates: rates,
						marketsource: marketsource,
						vehicletype: vehicletype,
						car: car,
						platenumber: platenumber,
						nationality: nationality,
						remarks: remarks
					},
					success: function(response){
						
						if(response.success){

							toastr.success("CheckIn Successful");
							$('#changeStatusModal').modal('hide');
							isnego = false;

						}
	
					},
					error: function(response){
						console.log(response);
					}
				});

			}
		}
		else{ //Others

			if(rates=="" || rates==null){
				toastr.error("Please select a rate.");
			}
			else if(platenumber.match(letters)){
				toastr.error("Please input alpha numeric only.");
				$('#platenumber').val('');
				$('#platenumber').focus();
			}
			else if(remarks.match(letters)){
				toastr.error("Please input alpha numeric only.");
				$('#remarkscheckin').val('');
				$('#remarkscheckin').focus();
			}
			else{

				if(remarks==""){
					remarks = "None";
				}

				if(platenumber==""){
					platenumber = "None";
				}

				if(car==""){
					car = 0;
				}

				$.ajax({
					url: '{{ url("api/operation/checkin") }}',
					method: 'post',
					dataType: 'json',
					data:{
						roomId: roomId,
						toStatus: toStatus,
						userInfo: userInfo,
						rates: rates,
						marketsource: marketsource,
						vehicletype: vehicletype,
						car: car,
						platenumber: platenumber,
						nationality: nationality,
						remarks: remarks
					},
					success: function(response){
						
						if(response.success){

							toastr.success("CheckIn Successful");
							$('#changeStatusModal').modal('hide');
							isnego = false;

						}
	
					},
					error: function(response){
						console.log(response);
					}
				});

			}


		}

	}

	function CancelOnNego(){

		$.ajax({
			url: '{{ url("api/operation/cancelonnego") }}',
			type: 'post',
			data: {
				roomId: roomId,
				userInfo: userInfo,
				toStatus: toStatus
			},
			dataType: 'json',
			success: function(response){

				if(response.success){

					toastr.success("Cancel Nego Successful");

				}
				isnego = false;

			}
		});

	}

	function ChangeStatus(){

		//Validation
		$.ajax({
			url: '{{ url("api/operation/changestatusvalidation") }}',
			type: 'get',
			data: {
				roomId: roomId,
				toStatus: toStatus,
				userInfo: userInfo
			},
			dataType: 'json',
			success: function(response){

				if(response.success){

					$.ajax({
						url: '{{ url("api/operation/changestatus") }}',
						method: 'post',
						data:{
							roomId: roomId,
							toStatus: toStatus,
							userInfo: userInfo
						},
						dataType: 'json',
						success:function(response){
							
							if(response.success){

								toastr.success("Updating Successful");
								$('#changeStatusModal').modal('hide');

							}
							
						}
					});

				}
				else{

					toastr.error(response.message);

				}

			}
		});

	}

	function GetCheckInInformation(){

			$.ajax({
				url: '{{ url("api/operation/getcheckininformation") }}',
				method: 'get',
				data:{
					roomId: roomId
				},
				dataType: 'json',
				success:function(response){

					// console.log(response);

					$('#rates').find('option').remove();
					for(var i=0;i<response.rates.length;i++){

						if(i==0){
							$('#rates').append('<option value="'+response.rates[i]["ID"]+'" selected="true">'+response.rates[i]["price"]+'</option>');
						}
						else{
							$('#rates').append('<option value="'+response.rates[i]["ID"]+'">'+response.rates[i]["price"]+'</option>');
						}
						
	
					}

					$('#nationality').find('option').remove();
					for(var i=0;i<response.nationality.length;i++){

						$('#nationality').append('<option value="'+response.nationality[i]["id"]+'">'+response.nationality[i]["name"]+'</option>');
	
					}

					$('#marketsource').find('option').remove();
					for(var i=0;i<response.marketsource.length;i++){

						$('#marketsource').append('<option value="'+response.marketsource[i]["id"]+'">'+response.marketsource[i]["MarketSource"]+'</option>');
	
					}

					$('#vehicletype').find('option').remove();
					for(var i=0;i<response.vehicletype.length;i++){

						$('#vehicletype').append('<option value="'+response.vehicletype[i]["id"]+'">'+response.vehicletype[i]["name"]+'</option>');
	
					}

					$('#car').find('option').remove();
					$('#car').append('<option value="">Select a car</option>');
					for(var i=0;i<response.carmake.length;i++){

						$('#car').append('<option value="'+response.carmake[i]["id"]+'">'+response.carmake[i]["CarMake"]+'</option>');

					}

					
				},
				error:function(response){
					console.log(response);
				}
			});

	}

	function OnNegoChange(){

		$.ajax({
			url: '{{ url("api/operation/onnegochange") }}',
			type: 'post',
			data: {
				roomId: roomId,
				toStatus: toStatus,
				userInfo: userInfo
			},
			dataType: 'json',
			success: function(response){

				if(response.success){

					toastr.success("On-Nego Successfull");

				}

			}
		});

	}
	//End Gelo ===============================================================

	function saveStats(){
		// TODO SaveStats
		// console.log("PASOK1");
		toStatus = $('#status').val();
		username = $('#username').val();
		var remarksNote = $('#remarksNote').val();
		var password = $('#password').val();

		// console.log(remarksNote);
		$.ajax({
			url: '{{ url("api/changeStatusOfRoom") }}',
			method: 'POST',
			data:{
				'roomId': roomId,
				'roomNo': roomNo,
				'toStatus': toStatus,
				'password': password,
				'username': username,
				'userInfo': userInfo,
				'remarksNote': remarksNote
			},
			dataType: 'json',
			success:function(response){
				
				console.log(response);

				// rmsLogId = response.logs.lastRmsLogId;

				// console.log(rmsLogId);

				$('#username').val('');
				$('#password').val('');
				toastr.success("Updating Successful");
				// $('#modal').modal('hide');
				
				// HERE
				// reload(roomId, 'roomid' + roomId);
				// socket.emit('reloadtags', {
				// 	'roomId': roomId,
				// 	'btnid': 'roomid' + roomId,
				// });
				// HERE

				$('#changeStatusModal').modal('hide');

				// if(response.saveChanges){
					
				// 	//RMS LOGS
				// 	setTimeout(RMSLogs, 3000);
					
				// }
				// $('#inner').removeClass("hidden");
			},
			error:function(response){
				// console.log(response);
			}
		});
	}

	// function checkin(){
	// 	//TODO checkin
	// 	// console.log("PASOK2");
	// 	var rates = $('#rates').val();
	// 	var marketsource = $('#marketsource').val();
	// 	var vehicletype = $('#vehicletype').val();
	// 	var car = $('#car').val();
	// 	var platenumber = $('#platenumber').val();
	// 	var nationality = $('#nationality').val();
	// 	var remarks = $('#remarkscheckin').val();

	// 	var letters = /[^a-zA-Z0-9 ]/g; //Alpha Numeric
		
		
	// 	//Validation
	// 	if(marketsource=="1"){ //Private
			
	// 		console.log("PASOK");
	// 		if(rates=="" || rates==null){
	// 			toastr.error("Please select a rate.");
	// 		}
	// 		else if(car==""){
	// 			toastr.error("Please select brand of car.");
	// 		}
	// 		else if(platenumber==""){
	// 			toastr.error("Please input the platenumber.");
	// 		}
	// 		else if(platenumber.match(letters)){
	// 			toastr.error("Please input alpha numeric only.");
	// 			$('#platenumber').val('');
	// 			$('#platenumber').focus();
	// 		}
	// 		else if(remarks.match(letters)){
	// 			toastr.error("Please input alpha numeric only.");
	// 			$('#remarkscheckin').val('');
	// 			$('#remarkscheckin').focus();
	// 		}
	// 		else{
				
	// 			$.ajax({
	// 				url: '{{ url("api/checkin") }}',
	// 				method: 'post',
	// 				dataType: 'json',
	// 				data:{
	// 					'roomId': roomId,
	// 					'roomType': roomType,
	// 					'id': id,
	// 					'roomNo': roomNo,
	// 					'rates': rates,
	// 					'marketsource': marketsource,
	// 					'vehicletype': vehicletype,
	// 					'car': car,
	// 					'platenumber': platenumber,
	// 					'nationality': nationality,
	// 					'userInfo': userInfo,
	// 					'remarks': remarks
	// 				},
	// 				success: function(r){

	// 					// console.log(r.response.checkin);
	// 					$('#changeStatusModal').modal('hide');
	// 					// if(r.response.checkin){

	// 					// 	//RMS LOGS
	// 					// 	setTimeout(RMSLogs, 3000);

	// 					// }


	// 				},
	// 				error: function(r){
	// 					// alert('error');
	// 					console.log(r);
	// 				}
	// 			});

	// 		}

	// 	}
	// 	else{ //Others
			
	// 		if(rates=="" || rates==null){
	// 			toastr.error("Please select a rate.");
	// 		}
	// 		else if(platenumber.match(letters)){
	// 			toastr.error("Please input alpha numeric only.");
	// 			$('#platenumber').val('');
	// 			$('#platenumber').focus();
	// 		}
	// 		else if(remarks.match(letters)){
	// 			toastr.error("Please input alpha numeric only.");
	// 			$('#remarkscheckin').val('');
	// 			$('#remarkscheckin').focus();
	// 		}
	// 		else{

	// 			$.ajax({
	// 				url: '{{ url("api/checkin") }}',
	// 				method: 'post',
	// 				dataType: 'json',
	// 				data:{
	// 					'roomId': roomId,
	// 					'roomType': roomType,
	// 					'id': id,
	// 					'roomNo': roomNo,
	// 					'rates': rates,
	// 					'marketsource': marketsource,
	// 					'vehicletype': vehicletype,
	// 					'car': car,
	// 					'platenumber': platenumber,
	// 					'nationality': nationality,
	// 					'userInfo': userInfo,
	// 					'remarks': remarks
	// 				},
	// 				success: function(r){

	// 					$('#changeStatusModal').modal('hide');
	// 					// if(r.response.checkin){

	// 					// 	//RMS LOGS
	// 					// 	setTimeout(RMSLogs, 3000);

	// 					// }


	// 				},
	// 				error: function(r){
	// 					// alert('error');
	// 					console.log(r);
	// 				}
	// 			});

	// 		}
			

	// 	}
	
		
	// }

	function checkIfCancelled(){
		console.log(userInfo);
		$('#rates').html('');
		$('#nationality').html('');
		$('#marketsource').html('');
		$('#vehicletype').html('');
		$('#car').html('');
		$.ajax({
			url: '{{ url("api/getPrevStatus") }}',
			method: 'get',
			dataType: 'json',
			data: {
				'roomId': roomId,
				'userInfo': userInfo
			},
			success: function(r){

				// console.log(r);
				// toastr.success(r.message);
				// console.log(r.prevStat.id + " " + r.prevStat.from_room_status_id);
				cancelledReload(r.prevStat.id, r.prevStat.from_room_status_id, r.prevStat.room_status_id);
				socket.emit('cancelledReload', {
					'roomId': r.prevStat.id,
					'prevStatus': r.prevStat.from_room_status_id,
					'currentStatus': r.prevStat.room_status_id
				});

			},
			error: function(r){
				// console.log(r);
			}
		});

	}

	// $(document).on('click', '#cancelTransaction', function(){

	// 	if(toStatus==20){
	// 		checkIfCancelled();
	// 	}

	// });

	function ClearOnNegoInformation(){

		$('#platenumber').val('');
		$('#remarkscheckin').val('');
		
	}

	// sockets ajax responses

	function cancelledReload(roomId, prevStatus, currentStatus){

		// console.log(roomId, prevStatus);
		$.ajax({
			url: '{{ url("api/implementPrevStat") }}',
			method: 'POST',
			dataType: 'json',
			data:{
				'roomId': roomId,
				'prevStatus': prevStatus,
				'currentStatus': currentStatus,
				'roomNo': roomNo
			},
			success:function(r){
				// console.log(r);
				$('#roomid'+roomId).css('background', r.query[0].color);
				$('#imageStatus'+roomId).attr('src', 'images/statuses/status_'+r.query[0].room_status_id+'.png?{{\Carbon\Carbon::now()->toDateTimeString()}}');
			},
			error:function(r){
				// console.log(r);
			}
		});
	}

	// end of socket ajax responses

	// RMS Logs
	function RMSLogs(){

		$.ajax({
			method: 'POST',
			url: '{{ url("api/tagboard/logs") }}',
			dataType: 'json',
			data:{
				'roomId': roomId,
				'userInfo': userInfo,
				'trans_from': 'WEB'
			},
			success:function(response){

			},
			error:function(response){
				console.log(response);
			}
		});

	}

	function Timer(id){

		$.ajax({
			url: '{{ url("api/tagboard/timer") }}',
			type: 'get',
			data: {
				id: id
			},
			dataType: 'json',
			success: function(response){

				var min = parseInt((response.sec / 60) % 60);
				var hour = parseInt(response.hour % 24);
				var day = parseInt(response.day % 24);
				var sec = parseInt(response.sec % 60);
				
				istimer.push({ id: id, min: min, sec: sec, hour: hour, day: day });

				$('#timer'+id).find('td').remove();
				if(day!=0){
					$('#timer'+id).append('<td><label style="font-size: 9px; color: black;">'+String('00'+day).slice(-2)+' D<br>'+String('00'+hour).slice(-2)+':'+String('00'+min).slice(-2)+':'+String('00'+sec).slice(-2)+'</label></td>');
				}
				else{
					$('#timer'+id).append('<td><label style="font-size: 9px; color: black;">'+String('00'+hour).slice(-2)+':'+String('00'+min).slice(-2)+':'+String('00'+sec).slice(-2)+'</label></td>');
				}
				
			}
		});

	}

	function loadInventory(){
		// inventory
		$('#inventoryTable').DataTable();
		$('#inventoryTable').DataTable().destroy();
		$('#inventoryTable').DataTable({
			processing: true,
			serverSide: true,
			searching: false,
			info: false,
			responsive: true,
			ajax:{
				method: 'POST',
				url: '{{ url("api/tagboard/inventory") }}',
				data: {
					'roomId': roomId,
				}
			},
			columns: [
				{data: 'brand', name: 'brand'},
				{data: 'quantity', name: 'quantity'},
				{data: 'brand_description', name: 'brand_description'},
			]
		});
	}

	function loadReservation(){
		// $.ajax({
		// 	url : "{{ url('api/vcreserve/getlocale') }}",
		// 	method : "POST",
		// 	dataType : "JSON",
		// 	data : {
		// 		'roomId' : roomId
		// 	},
		// 	success:function(r){
		// 		console.log(r);
		// 	},
		// 	error:function(r){

		// 	}
		// });
		// reservation
		// $('#reservedTable').DataTable();
		// $('#reservedTable').DataTable().destroy();
		tblreserve.destroy();
		tblreserve = $('#reservedTable').DataTable({
			processing: true,
			serverSide: true,
			searching: false,
			info: false,
			// responsive: true,
			ajax:{
				method: 'POST',
				url: '{{ url("api/vcreserve/getlocale") }}',
				data: {
					'roomId' : roomId
				}
			},
			columns: [
				{data: 'date', name: 'date'},
				{data: 'time', name: 'time'},
				{data: 'hours', name: 'hours'},
				{data: 'notes', name: 'notes'},
			]
		});
		
	}

	function loadCurrent(){
		$.ajax({
			url: '{{ url('api/vclogs/logs/current') }}',
			method: 'POST',
			dataType: 'json',
			data:{
				'roomNo': roomNo,
				'roomId': roomId,
			},
			success:function(r){
				// console.log(r);
				$('#task').text('');
				$('#dateCurrent').text('');
				$('#assignedBy').text('');
				$('#source').text('');

				$('#task').text(r.getCurrentStatus.room_status);
				$('#dateCurrent').text(r.getInstanceRoom.updated_at);
				$('#assignedBy').text(r.getUserLoggedBy.lname + ", " + r.getUserLoggedBy.fname);
				$('#source').text(r.source.trans_from);
			},
			error:function(r){
				console.log(r);
			}
		});
	}

	function loadToday(){
		$.ajax({
			url: '{{ url('api/vclogs/logs/today') }}',
			method: 'POST',
			dataType: 'json',
			data:{
				'roomId': roomId,
			},
			success:function(r){
				console.log(r);
				$('.statusToday').html('');
				for(var i = 0; i < r.todayLogs.length; i++){
					// console.log(r.todayLogs[i]["fname"]); 
					$('.statusToday').append("Start Status: " + "<b>" + r.todayLogs[i]["start_status_text"] + "</b>" + " By: " + "<b>" + r.todayLogs[i]["lname"] + ", " 
						+ r.todayLogs[i]["fname"] + "</b>" + "<br/>" + "Date: " + "<b>" + r.todayLogs[i]["start_datetime"] + "</b>" + "<br/>" +
						"Source: " + "<b>" + r.todayLogs[i]["sfrom"] + "</b>" + "<br/>" + 
						"End Status: " + "<b>" + r.todayLogs[i]["end_status_text"] + "</b>" + " By: " + "<b>" + r.todayLogs[i]["endlname"] + ", " + r.todayLogs[i]["endfname"] + "</b>" + "</b>" + "<br/>" +
						"Date: " + "<b>" + r.todayLogs[i]["end_datetime"] + "</b>" + "</br>" + 
						"Source: " + "<b>" + r.todayLogs[i]["efrom"] +"</b>" + "</br>" + 
						"<div style='border:1px solid black;'>" + "</div>");
				}
			},
			error:function(r){
				console.log(r);
			}
		});
	}

	function loadHistoryRoom(){

		$.ajax({
			url: '{{ url('api/room/history') }}',
			method: 'get',
			dataType: 'json',
			data:{
				'roomNo': roomNo,
				'roomId': roomId
			},
			success:function(r){
				console.log(r);
				$('#startDateHistory').datetimepicker({
                    format: 'YYYY-MM-DD'
                });
                $('#endDateHistory').datetimepicker({
                    format: 'YYYY-MM-DD'
                });
				// roomStatus
				// statusHistory
				$('#statusHistory').append('<option value="all">ALL</option>');
				$.each(r.roomStatus, function(i, items){
					$('#statusHistory').append($('<option>', {
						value: items.id,
						text: items.room_status
					}));
				});
			},
			error:function(r){
				console.log(r);
			}
		})
	}

	function Login(){

		var username = $('#username').val();
		var password = $('#password').val();
		$.ajax({
			url: '{{ url("api/login") }}',
			method: 'POST',
			data:{
				'username': username,
				'password': password,
				'roomId': roomId
			},
			dataType: 'json',
			success:function(response){

				console.log(response);
				if(response.access){

					$('#username').val('');
					$('#password').val('');

					userInfo = response.query.username;
					currentRoomStatus = response.roomInfo.room_status_id;

					//Validation
					$.ajax({
						url: '{{ url("api/operation/validateongoinguser") }}',
						type: 'get',
						data: {
							userinfo: userInfo,
							roomstatus: currentRoomStatus,
							roomid: roomId
						},
						dataType: 'json',
						success: function(val){

							if(val.success){

								//Show Cancel Transaction
								if( (currentRoomStatus == 4) || (currentRoomStatus == 6) || (currentRoomStatus == 13) || 
									(currentRoomStatus == 19) || (currentRoomStatus == 22) ||
									(currentRoomStatus == 24) || (currentRoomStatus == 51) ){

									setTimeout(function() {
										$('#h3info').text('');
										$('#h3info').append('<i id="iinfo" class="fa fa-tasks"></i> ROOM # '+ sroomno);
										$('#changeStatusModal').modal("toggle");
									}, 500);

									if(val.bypass==1){
										$('#saveStatus').addClass("hidden");
									}
									
									$('#btncancel').removeClass("hidden");

								}
								else if(currentRoomStatus==20){ //Show Form On Nego

									//Check if has access
									$.ajax({
										url: '{{ url("api/operation/checkuserrole") }}',
										type: 'get',
										data: {
											userInfo: userInfo
										},
										dataType: 'json',
										success: function(response){

											if(response.allow){

												setTimeout(function() {
													$('#h3info').text('');
													$('#h3info').append('<i id="iinfo" class="fa fa-tasks"></i> ROOM # '+ sroomno);
													$('#changeStatusModal').modal("toggle");
												}, 500);

												$('#inner').removeClass("hidden");
												isnego = true;
												toStatus = currentRoomStatus;
												GetCheckInInformation();

											}
											else{
												
												toastr.error("You dont have any access here.");

											}

										}
									});


								}
								else{

									setTimeout(function() {
										$('#h3info').text('');
										$('#h3info').append('<i id="iinfo" class="fa fa-tasks"></i> ROOM # '+ sroomno);
										$('#changeStatusModal').modal("toggle");
									}, 500);

									if(val.bypass==0){
										$('#saveStatus').removeClass("hidden");
									}
									
									$('#btncancel').addClass("hidden");
								}

								LoadRoomStatus(response.query);

							}
							else{

								toastr.error(val.message);

							}

						}					
					});
					//


				}
				else{
					
					$('#username').val('');
					$('#password').val('');
					toastr.error(response.message);

				}

			},
			error:function(response){
				console.log(response);
			}
		});

	}

	function LoadSelect2(){

        $('#rates').select2({
            theme: 'bootstrap'
        });

        $('#nationality').select2({
            theme: 'bootstrap'
        });

        $('#marketsource').select2({
            theme: 'bootstrap'
        });

        $('#vehicletype').select2({
            theme: 'bootstrap'
        });

        $('#car').select2({
            theme: 'bootstrap'
        });

	}

	function LoadRoomStatus(data){
		//TODO I am here load room status
		$.ajax({
			url: '{{ url("api/getRoomInstance") }}',
			method: 'get',
			dataType: 'json',
			data:{
				'id': roomId,
				'role_id': data.role_id
			},
			success:function(response){
				console.log(response);
				roomType = response.getCurrentRoomStatus.room_type_id;
				roomNo = response.getCurrentRoomStatus.room_no;
				// console.log(roomType);
				
				$('#status').html('');
				$('#status').append($('<option value="">SELECT</option>'));
				// if(response.getCurrentRoomStatus.room_status_id==31){
				// 	if(data.role_id==10){
				// 		$('#status').append($('<option value="3">Dirty</option>'));
				// 	}
				// }
				$.each(response.getNextStatus, function(i, items){
					$('#status').append($('<option>', {
						value: items.id,
						text: items.room_status
					}));
				});


			}
		});

	}

	function ReloadTimer(){

		for(var i=0;i<istimer.length;i++){

			istimer[i].min;
			istimer[i].hour;
			istimer[i].day;
			istimer[i].sec += 1;

			if(istimer[i].sec==60){
				istimer[i].min += 1;
				istimer[i].sec = 0;
			}

			if(istimer[i].min==60){
				istimer[i].hour += 1;
				istimer[i].min = 0;
			}

			if(istimer[i].hour==24){
				istimer[i].day += 1;
				istimer[i].hour = 0;
			}

			$('#timer'+istimer[i].id).find('td').remove();
			if(istimer[i].day!=0){
				$('#timer'+istimer[i].id).append('<td><label style="font-size: 9px; color: black;">'+String('00'+istimer[i].day).slice(-2)+' D<br>'+String('00'+istimer[i].hour).slice(-2)+':'+String('00'+istimer[i].min).slice(-2)+':'+String('00'+ istimer[i].sec).slice(-2)+'</label></td>');
			}
			else{
				$('#timer'+istimer[i].id).append('<td><label style="font-size: 9px; color: black;">'+String('00'+istimer[i].hour).slice(-2)+':'+String('00'+istimer[i].min).slice(-2)+':'+String('00'+ istimer[i].sec).slice(-2)+'</label></td>');
			}
			
		

		}

		
	}

	setInterval(function(){
		
		ReloadTimer();
		
	}, 1000);

</script>
