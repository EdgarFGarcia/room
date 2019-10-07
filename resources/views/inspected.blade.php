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

	</div>
</div>
<div id="form_body">
	<div class="row">
		<div class="col-lg-12" id="roomFetch" style="background: #fff !important;">
			<select name="" class="form-control" id="dm">
			<table class="table table-condensed table-striped" name="tableReports" id="tableReports" style="width: 100%;">
				<thead>
					<tr>Name</tr>
					<tr>Inspected Rooms</tr>
					<tr>Average Duration</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
@include('modal.roomselect')
@include('extensions.menu')
@endsection

@section('page-script')
<script type="text/javascript">
	loadReports();

	function loadReports(){
		$('#tableReports').DataTable();
		$('#tableReports').DataTable().destroy();
		$('#tableReports').DataTable({
			processing: true,
			serverSide: true,
			searching: false,
			info: false,
			responsive: true,
			ajax:{
				method: 'POST',
				url: 'rms/getRemarksByDate',
				data:{

				}
			},
			columns:{

			}
		});
	}

</script>
@endsection
