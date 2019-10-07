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

    <div class="col-md-4">

        <div class="form-group">

            {{-- <label for="cmbinspection">Status</label>
            <select name="cmbinspection" id="cmbinspection" class="form-control">
                <option value="All">All</option>
                <option value="Unfinished">Unfinished</option>
                <option value="Finish">Finish</option>
            </select> --}}
            <br>

        </div>

    </div>

    <div class="col-md-7">


    </div>

    <div class="col-md-12">

        <table id="tblhistoricalinspection" class="table table-bordered" style="font-size: 12px; width: 100%;">
            <thead>
              <tr>
                <th colspan="1">Room / Area</th>
                <th colspan="1">Component</th>
                <th colspan="1">Area</th>
                <th colspan="1">Remarks</th>
                <th colspan="1">Status</th>
                <th colspan="1">Remarks By</th>
                <th colspan="1">Date</th>
                <th colspan="1"></th>
              </tr>
            </thead>
            <tbody id="historicalinspectioncontent">
            </tbody>
        </table>

    </div>

    @include('modal.historicalinspectionlogin')
    @include('modal.historicalinspectionvalidation')
    @include('modal.historicalinspectionrepair')

</div>

@endsection

@section('page-script')
<script type="text/javascript">

    //Variables
    var sid;
    var userinfo;
    var roleid;
    var search = "All";

    //New Variables
    var historicalheader = [];
    var historicaldata = [];
    var sname;
    var sstatus;

    $(document).ready(function(){

        //Set
        // SetSelect2();

        //Load
        LoadHistoricalInspection();

        //Timer
        countUp();
       

    });

    $(document).on('click', '#btninfo', function(){

        sid = $(this).val();
        GetSelectedName(historicaldata, sid);
        ClearLogin();
        $('#historicalinspectionlogin').modal('toggle');

    });

    $('#txtpassword').on('keyup', function(event){

        if(event.keyCode==13){

            Login();

        }

    });

    $('#btnlogin').on('click', function(){

        Login();

    });

    $(document).on('click', '.header', function(){

        var data = $(this).prop('id');
        var value = $('#txt'+data).val();


        var compare = $("#"+data).prop('class');
        
        if(compare=="header expand listoff"){

            $("#"+data).attr('class', 'header expand liston');

            $.ajax({
                url: '{{ url("api/historical/loadhistoricalremarks") }}',
                type: 'get',
                data: {
                    id: value
                },
                dataType: 'json',
                success: function(response){

                    $('#historicalinspectioncontent').find('tr.data'+data).remove();

                    for(var i=0;i<response.data.length;i++){

                        var strvalue = value.replace(',', '');
                        var alyssa = response.data[i]["id"].replace(',', '');

                        var name = strvalue + alyssa;
                        historicaldata.push({id: value, name: name, valdata: response.data[i]["id"], status: response.data[i]["status"]});

                        $('<tr name="'+ name +'" class="data'+data+'"><td><i class="fa fa-angle-double-right" style="font-size:12px;"></i> '+ response.data[i]["area"] +'</td><td>'+ response.data[i]["component"] +'</td><td>'+ response.data[i]["standard"] +'</td><td>'+ response.data[i]["remarks"] +'</td><td id="'+ name +'">'+ response.data[i]["status"] +'</td><td>'+ response.data[i]["inspector"] +'</td> <td>'+ response.data[i]["created_at"] +'</td> <td><button id="btninfo" name="btninfo" value="'+ response.data[i]["id"] +'" class="btn btn-flat btn-info"><i class="fa fa-pencil-square"></i></button></td></tr>').insertAfter($('#'+data).closest('tr'));

                    }

                    console.log(historicaldata);

                }
            });

        }else{

            $("#"+data).attr('class', 'header expand listoff');

            $('#historicalinspectioncontent').find('tr.data'+data).remove();

            RemoveArrayContent(historicaldata, value);

            console.log(historicaldata);
            
        }

    });

    $('#btnsaverepair').on('click', function(){

        var remarks = $('#txtrmlremarks').val();

        if(remarks==""){
            remarks = "None";
        }

        $.ajax({
            url: '{{ url("api/historical/saverepairinformation") }}',
            type: 'post',
            data: {
                id: sid,
                remarks: remarks,
                userinfo: userinfo
            },
            dataType: 'json',
            success: function(response){

                if(response.success){

                    SetForValidation();
                    $('#historicalinspectionrepair .close').click();

                }

            }
        });


    });

    $('#btnvapprove').on('click', function(){

        Approve();

    });

    $('#btnvdisapprove').on('click', function(){

        Disapprove();

    });

    function Disapprove(){

        var remarks = $('#txtvvalidatorremarks').val();

        if(remarks==""){
            toastr.error("Please input remarks.");
        }
        else{

            $.ajax({
                url: '{{ url("api/historical/disapproveinformation") }}',
                type: 'post',
                data: {
                    id: sid,
                    remarks: remarks,
                    userinfo: userinfo
                },
                dataType: 'json',
                success: function(response){

                    if(response.success){

                        $('#historicalinspectionvalidation .close').click();
                        toastr.success(response.message);

                        SetForRepair();

                    }

                }
            });

        }

    }

    function Approve(){

        var remarks = $('#txtvvalidatorremarks').val();

        if(remarks==""){
            remarks = "None";
        }

        $.ajax({
            url: '{{ url("api/historical/approveinformation") }}',
            type: 'post',
            data: {
                id: sid,
                remarks: remarks,
                userinfo: userinfo
            },
            dataType: 'json',
            success: function(response){

                if(response.success){

                    $('#historicalinspectionvalidation .close').click();
                    toastr.success(response.message);

                    $('[name="'+sname+'"]').remove();
                    RemoveArrayItem();

                    console.log(historicaldata);

                }

            }
        });

    }

    function RemoveArrayItem(){

        for(var i=0;i<historicaldata.length;i++){

            if(historicaldata[i].valdata==sid){

                var val = historicaldata[i].id;

                historicaldata.splice(i, 1);
                RemoveHeader(historicalheader, val);
                break;

            }

        }

    }

    function RemoveHeader(array, id){

        var remove = false;

        for(var i=0;i<array.length;i++){

            if(historicaldata.length==0){

                var str = id.replace(',', '');
                $('#'+str).remove();

                if(historicaldata.length==0){

                    LoadHistoricalInspection();

                }
                               
                break;

            }
            else{

                for(var y=0;y<historicaldata.length;y++){

                    if(id==array.header){

                        remove = false
                        break;

                    }
                    else{
                        
                        header = historicaldata.header;
                        remove = true;

                    }

                }

                if(remove==true){

                    var str = id.replace(',', '');
                    $('#'+str).remove();

                    if(historicaldata.length==0){

                        LoadHistoricalInspection();

                    }

                    break;

                }

            }

        }

        console.log(historicalheader);


    }

    function SetForRepair(){

        $('#'+sname).text('');
        $('#'+sname).text('For Repair');

        for(var i=0;i<historicaldata.length;i++){

            if(historicaldata[i].valdata==sid){

                historicaldata[i].status = "For Repair";
                break;

            }

        }

        console.log(historicaldata);

    }

    function SetForValidation(){

        $('#'+sname).text('');
        $('#'+sname).text('For Validation');

        for(var i=0;i<historicaldata.length;i++){

            if(historicaldata[i].valdata==sid){

                historicaldata[i].status = "For Validation";
                break;

            }

        }

        console.log(historicaldata);

    }

    function GetSelectedName(array, id){

        for(var i=0;historicaldata.length;i++){

            if(historicaldata[i].valdata==id){

                sname = historicaldata[i].name;
                sstatus = historicaldata[i].status;
                break;

            }

        }

        console.log(sname + " " + sstatus);

    }

    function RemoveArrayContent(array, id){

        var arraylength = array.length;

        for(var i=0;i<arraylength;i++){

            for(var y=0;y<historicaldata.length;y++){

                if(historicaldata[y].id==id){

                    historicaldata.splice(y, 1);

                    break;
                
                }

            }

        }

    }

    function Login(){

        var username = $('#txtusername').val();
        var password = $('#txtpassword').val();

        if(username==""){
            toastr.error("Please input your username.");
            $('#txtusername').focus();
        }
        else if(password==""){
            toastr.error("Please input your password.");
            $('#txtpassword').focus();
        }
        else{

            $.ajax({
                url: '{{ url("api/historical/login") }}',
                type: 'post',
                data: {
                    username: username,
                    password: password
                },
                dataType: 'json',
                success: function(response){

                    if(response.success){

                        toastr.success(response.message);
                        userinfo = response.userinfo;
                        roleid = response.roleid;
                        $('#historicalinspectionlogin .close').click();

                        if(sstatus=="For Maintenance" || sstatus=="For Housework"){

                            if( (roleid==11) || (roleid==27) ){

                                setTimeout(function(){

                                    LoadHistoricalInformationRepair();
                                    $('#historicalinspectionrepair').modal('toggle');
                                    
                                }, 500);

                            }
                            else{
                                toastr.error("You dont have any access here.");
                            }

                        }
                        else {

                            if( (roleid==2) || (roleid==22) || (roleid==59) || (roleid==27) ){

                                    setTimeout(function(){

                                        LoadHistoricalInformationValidation();
                                        $('#historicalinspectionvalidation').modal('toggle');

                                    }, 500);

                            }
                            else{
                                toastr.error("You dont have any access here.");
                            }

                        }


                    }
                    else{

                        toastr.error(response.message);

                    }

                }
            });

        }


    }

    function LoadHistoricalInformationRepair(){

        $.ajax({
            url: '{{ url("api/historical/loadhistoricalinformation") }}',
            type: 'get',
            data: {
                id: sid
            },
            dataType: 'json',
            success: function(response){

                $('#txtrarea').val(response.area);
                $('#txtrcomponent').val(response.component);
                $('#txtrstandard').val(response.standard);
                $('#txtrremarks').val(response.remarks);

            }
        });

    }

    function LoadHistoricalInformationValidation(){

        $.ajax({
            url: '{{ url("api/historical/loadhistoricalinformation") }}',
            type: 'get',
            data: {
                id: sid
            },
            dataType: 'json',
            success: function(response){

                $('#txtvarea').val(response.area);
                $('#txtvcomponent').val(response.component);
                $('#txtvstandard').val(response.standard);
                $('#txtvremarks').val(response.remarks);
                $('#txtvmluser').val(response.service_user_id);
                $('#txtvmlremarks').val(response.mlremarks);
                $('#txtvservicedate').val(response.service_date);

            }
        });

    }


    function ClearLogin(){

        $('#txtusername').val('');
        $('#txtpassword').val('');

    }


    function SetSelect2(){

        $('#cmbinspection').select2({
            theme: 'bootstrap'
        });

    }

    function LoadHistoricalInspection(){

        $.ajax({
            url: '{{ url("api/historical/loadhistoricalinspection") }}',
            type: 'get',
            data: {
                search: search
            },
            dataType: 'json',
            success: function(response){

                historicalheader = [];
                $('#historicalinspectioncontent').find('tr').remove();
                if(response.data.length!=0){

                    for(var i=0;i<response.data.length;i++){

                        historicalheader.push({header: response.data[i]["id"]});

                        var strsplit = response.data[i]["id"].split(",");

                        $('#historicalinspectioncontent').append('<tr id="'+ response.data[i]["id"].replace(',', '') +'" class="header expand listoff" style="cursor:pointer;"><td colspan="8"><input type="hidden" id="txt'+ response.data[i]["id"].replace(',', '') +'" name="txt'+ response.data[i]["id"].replace(',', '') +'" value="'+response.data[i]["id"]+'"><strong> '+ response.data[i]["room_no"] + '<br> Total Record: ' + response.data[i]["data_count"] +'</strong></td></tr>');
                        

                        // $('#historicalinspectioncontent').append('<tr id="'+ strsplit[0] +'" class="header expand listoff" style="cursor:pointer;"><td colspan="6"><input type="hidden" id="txt'+ strsplit[0] +'" name="txt'+ strsplit[0] +'" value="'+response.data[i]["id"]+'"><strong>'+ response.data[i]["room_no"] +'</strong></td></tr>');

                        

                    }

                }
                else{

                    $('#historicalinspectioncontent').append('<tr><td style="text-align: center;" colspan="5"><strong>No Data</strong></td></tr>');

                }

                console.log(historicalheader);


            }
        });

    }


</script>
@endsection
