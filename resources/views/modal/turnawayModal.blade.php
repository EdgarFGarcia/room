<!-- Modal -->
<div id="turnawaymodal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Turn Away</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="col-md-12 text-left">
            <label>Locale</label><br/>
            <select name="locale" id="locale" class="form-control">
              
            </select><br/>
            <label>Market Source</label>
            <select name="marketSource" id="marketSource" class="form-control">
              
            </select><br/>
            <label>Plate Number</label>
            <input type="text" class="form-control" id="plateNumber"/><br/>
            <label>Reasons</label>
            <select name="reasons" id="reasons" class="form-control">
              
            </select><br/>
            <label>Notes</label>
            <textarea class="form-control" id="notes" name="notes"></textarea>
          </div>
        </div>
      </div>
      <br/>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="modal" data-target="#turnawaymodalLogin" id="add">Add</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="turnawaymodalLogin" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Turn Away</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="col-md-6 text-left">
            <label>Username</label><br/>
            <input type="text" class="form-control" id="username" />
          </div>
          <div class="col-md-6 text-left">
            <label>Password</label>
            <input type="password" class="form-control" id="password"/>
          </div>
        </div>
      </div>
      <br/>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="turnawaySave" data-dismiss="modal">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>