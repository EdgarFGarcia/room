<div class="modal fade" id="newevent" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title" style="color: black;">New Event Information</h5>
			</div>

			<div class="modal-body">

                <div class="container-fluid">

                    <div class="form-group text-left">
                        <label for="cmbreason">Event Reason</label>
                        <select name="cmbreason" id="cmbreason" class="form-control"></select>
                    </div>
                    <div class="form-group text-left">
                        <label for="txtreason">Reason</label>
                        <textarea name="txtreason" id="txtreason" rows="5" class="form-control"></textarea>
                    </div>

                </div>

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-flat btn-primary" id="btnsaveevent">Save Information</button>
		      	<button type="button" class="btn btn-flat btn-danger" data-dismiss="modal" id="btnclose">Close</button>
			</div>	

		</div>

	</div>
</div>

