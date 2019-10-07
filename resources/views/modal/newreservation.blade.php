<div class="modal fade" id="reserveCalendar" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-xl">

		<div class="modal-content">

			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title" style="color: black;">Reservation Details</h5>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 text-left">
						<label>Reservation Details</label>
						<div style="border: 1px solid black;"></div>
						<div class="col-md-6 text-left">
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label>Type:</label>
								</div>
								<div class="col-md-8">
									<select name="roomtypesReservation" id="roomtypesReservation" class="form-control">
										
									</select>
								</div>
								
							</div>
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label>Rate:</label>
								</div>
								<div class="col-md-8">
									<select name="rateReservation" id="rateReservation" class="form-control">
										
									</select>
								</div>
							</div>
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label>Check-in (FROM):</label>
								</div>
								<div class="col-md-8">
									<input type='text' class="form-control" id="checkinDateFrom" name="checkinDateFrom" />
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label>Category:</label>
								</div>
								<div class="col-md-8">
									<select name="reserveCategories" id="reserveCategories" class="form-control">
										
									</select>
								</div>
							</div>
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label>Time:</label>
								</div>
								<div class="col-md-8">
									<input type='text' class="form-control" id="timeInReservation" />
								</div>
							</div>
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label>Check-in (TO):</label>
								</div>
								<div class="col-md-8">
									<input type='text' class="form-control" id="checkinDateTo" name="checkinDateTo" />
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 text-left">
                        <br>
						<label>Guest Details</label>
						<div style="border: 1px solid black;"></div>
						<div class="col-md-6 text-left">
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label for="fnameReservation">Firstname:</label>
								</div>
								<div class="col-md-8">
									<input type="text" class="form-control" id="fnameReservation" />
								</div>
							</div>
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label for="pnumberReservation">Phone Number:</label>
								</div>
								<div class="col-md-8">
									<input type="number" class="form-control" id="pnumberReservation" />
								</div>
							</div>
						</div>
						<div class="col-md-6 text-left">
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label for="lnameReservation">Lastname</label>
								</div>
								<div class="col-md-8">
									<input type="text" class="form-control" id="lnameReservation"/>
								</div>
							</div>
							<div class="col-md-12 container-fluid" style="margin-top: 15px;">
								<div class="col-md-4">
									<label for="emailReservation">Email:</label>
								</div>
								<div class="col-md-8">
									<input type="text" class="form-control" id="emailReservation" />
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 text-left">
                        <br>
						<label>Payment</label>
						<div style="border: 1px solid black;"></div>
						<div class="col-md-12" style="margin-top: 15px; margin-bottom: 15px;">
							<div class="col-md-2">
								<label>Deposit</label>
							</div>
							<div class="col-md-4">
								<select name="deposit" id="deposit" class="form-control">
									<option value="">-- Please select --</option>
									<option value="php">PHP</option>
									<option value="usd">USD</option>
								</select>
							</div>
							<div class="col-md-6">
								<input type="text" class="form-control" id="amount"/>
							</div>
						</div>
					</div>

					<div class="col-md-12 text-left style="margin-top: 15px;">
                        <br>
                        <div style="border: 1px solid black;"></div>
                        <br>
						<label>Notes</label>
						<textarea id="notesForReservation" class="form-control"></textarea>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-flat btn-primary" id="reservationSave">Save Changes</button>
		      	<button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</button>
			</div>	

		</div>

	</div>
</div>