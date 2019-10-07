@extends('layouts.main')
@section('title', 'Turn Away')

@section('content')

<div id="myRMSNavigator" class="col-md-12" style="margin-top: 1em;">
    {{-- Nothing --}}
</div>
<div id="form_body">

    <div class="col-md-6 ">

    </div>
    <div class="col-md-6 ">
       <button type="button" class="btn btn-flat btn-success" data-toggle="modal" data-target="#turnawaymodal" id="turnaway" name="turnaway" style="float: right; margin-top: 10px; background: #000 !important; margin-bottom: 10px;"><i class="fa fa-plus"></i></button>
    </div>

    <div class="col-md-12">
        <table class="table table-condensed table-striped" id="turnAwayRecords">
            <thead>
                <tr>
                    <th>Locale</th>
                    <th>Type Of Guest</th>
                    <th>Reason</th>
                    <th>Note</th>
                    <th>Plate Number</th>
                    <th>Created At</th>
                    <th>User</th>
                </tr>
            </thead>
        </table>
    </div>
    {{-- Modal --}}
    @include('modal.turnawayModal')
</div>


@endsection

@section('page-script')
<script type="text/javascript">

    loadTable();
    //Variables
    var turnAwayRecords;

    $(document).ready(function(){

        //Timer
        countUp();

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

        $(document).on('click', '#turnaway', function(){
            loadLocales();
            loadMarket();
            loadReasons();
        });

        $(document).on('click', '#turnawaySave', function(){

            var locale = $('#locale').val();
            var marketSource = $('#marketSource').val();
            var reasons = $('#reasons').val();
            var plateNumber = $('#plateNumber').val();
            var notes = $('#notes').val();
            var username = $('#username').val();
            var password = $('#password').val();

            $.ajax({
                url: '{{ url("api/turnaway/insertRecord") }}',
                method: 'post',
                dataType: 'json',
                data:{
                    'locale': locale,
                    'marketSource': marketSource,
                    'reasons': reasons,
                    'plateNumber': plateNumber,
                    'notes': notes,
                    'username': username,
                    'password': password
                },
                success:function(response){
                    
                    if(response.success){

                        toastr.success(response.message);
                        //Reload
                        reloadtable();

                    }
                    else{

                        toastr.error(response.message);

                    }

                }
            });
        });

    });

    function loadLocales(){

        $.ajax({
            url: '{{ url("api/turnaway/getAllLocale") }}',
            method: 'POST',
            data:{

            },
            dataType: 'json',
            success:function(r){
                console.log(r);

                $('#locale').html('');

                $('#locale').append('<option value="">SELECT</option>');
                $.each(r.locales, function(i, items){
                    $('#locale').append($('<option>', {
                        value: items.id,
                        text: items.room_area
                    }));
                });
                
            },
            error:function(r){
                console.log(r);
            }
        });

    }

    function loadMarket(){

        $.ajax({
            url: '{{ url("api/turnaway/getAllGuest") }}',
            method: 'POST',
            dataType: 'json',
            success:function(r){
                console.log(r);

                $('#marketSource').html('');
                $('#marketSource').append('<option value="">SELECT</option>');
                $.each(r.marketSource, function(i, items){
                    $('#marketSource').append($('<option>', {
                        value: items.id,
                        text: items.MarketSource, 
                    }));
                });
            },
            error:function(r){
                console.log(r);
            }
        });
    }

    function loadReasons(){

        $.ajax({
            url: '{{ url("api/turnaway/getAllReason") }}',
            method: 'POST',
            dataType: 'json',
            success:function(r){
                console.log(r);

                $('#reasons').html('');
                $('#reasons').append('<option value="">SELECT</option>');
                $.each(r.reasons, function(i, items){
                    $('#reasons').append($('<option>', {
                        value: items.id,
                        text: items.reason, 
                    }));
                });
            },
            error:function(r){
                console.log(r);
            }
        });

    }

    function loadTable(){

        turnAwayRecords = $('#turnAwayRecords').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                type: 'get',
                url: '{{ url("api/turnaway/getAllRecords") }}'
            },
            columns: [
                {data: 'locale', name: 'locale'},
                {data: 'typeofguest', name: 'typeofguest'},
                {data: 'reason', name: 'reason'},
                {data: 'note', name: 'note'},
                {data: 'platenumber', name: 'platenumber'},
                {data: 'created_at', name: 'created_at'},
                {data: 'user', name: 'user'}
            ]
        });

    }

    function reloadtable(){

        turnAwayRecords.ajax.reload();

    }

</script>
@endsection
