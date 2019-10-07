<!DOCTYPE html>
<html lang="en">
<head>
<title>@yield('title')</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>

<!-- //for-mobile-apps -->

<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->

<!-- Latest compiled and minified CSS -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->

<link rel="stylesheet" href="{{ asset('css/bootstraprms.css') }}">

<!-- Optional theme -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous"> -->

<link href="{{ asset("css/style.css") }}" rel="stylesheet" type="text/css" media="all" />

<link href="{{ asset("css/bootstrap-datetimepicker.min.css") }}" rel="stylesheet" type="text/css"/>

{{-- datatables --}}
<!-- <link href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" /> -->
<link href="{{ asset("css/datatables.css") }}" rel="stylesheet" type="text/css">

{{-- ToolTip CSS --}}
<link rel="stylesheet" href="{{ asset("css/tooltip.css") }}">
<link rel="stylesheet" href="assets/css/flexslider.css" type="text/css" media="screen" property="" />

<!-- font-awesome icons -->
<link rel="stylesheet" href="assets/css/font-awesome.min.css" />

<!-- toastr -->
<!-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> -->
<link rel="stylesheet" href="{{ asset("css/toast.css") }}">

<link rel="stylesheet" href="{{asset('css/blink.css')}}"/>
<link rel="stylesheet" href="{{asset('css/menu.css')}}"/>
<link rel="stylesheet" href="{{asset('css/sidebar.css')}}"/>
<link rel="stylesheet" href="{{asset('css/belo.css')}}"/>
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset("css/select2-bootstrap.min.css") }}">

<!-- js -->
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script> -->
<script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>


<!-- Latest compiled and minified JavaScript -->
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script> -->

<script src="{{ asset('js/bootstrap.js') }}"></script>

<script src="{{ asset('js/tippy.js') }}"></script>

<script src="assets/js/main.js"></script>

{{-- <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script> --}}
<script src="{{ asset('js/datatables.js') }}"></script>

{{-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script> --}}
<script type="text/javascript" src="{{ asset('js/bootstrap4.js') }}"></script>
<script src="{{ asset("js/select2.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("assets/js/socket.io.js") }}"></script>

<!-- <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" type="text/javascript"></script> -->
<script src="{{ asset("js/toast.min.js") }}" type="text/javascript"></script>

<!-- //js -->

<!-- //font-awesome icons -->
<style type="text/css">
@media (min-width: 768px) {
  .modal-xl {
    width: 90%;
   max-width:1200px;
  }
}
</style>
 </head>
<body>
<!-- banner -->
	<div id="myHeader">
		<div class="w3ls-banner-info-bottom">
			<div class="container">
				<div class="banner-address" style="padding-left: 1em; padding-right: 1em;">
					<div class="col-md-3 banner-address-left">
						<img src="assets/images/vc_logo_webtest.png" style="max-width: 200px;"/>
					</div>
					<div class="col-md-3 banner-address-left">
						<!-- <p><i class="fa fa-envelope" aria-hidden="true"></i> <a href="mailto:example@email.com">mail@example.com</a></p> -->
					</div>
					<div class="col-md-3 banner-address-left">
						<!-- <p><i class="fa fa-phone" aria-hidden="true"></i> +1 234 567 8901</p> -->
					</div>
					<div class="col-md-3 agile_banner_social">
					</div>
					<div class="clearfix"> </div>
				</div>
			</div>
		</div>
		<div class="header" style="background: #333; padding-left: 1em; padding-right: 1em;">
			<div class="container-fluid">
				<nav class="navbar navbar-default">
					<div class="navbar-header">
						<!-- <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button> -->
						<!-- <h1><a class="navbar-brand" href="index.html">Negotiation</a></h1> -->
					</div>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse navbar-right text-left" id="bs-example-navbar-collapse-1">
						<nav class="cl-effect-13" id="cl-effect-13">
							<ul class="nav navbar-nav">
								<li style="font-size: 12px;"><a href="{{ url('/') }}">Room Management</a></li>
								<li style="font-size: 12px;"><a href="{{ url('/historical') }}">For Repairs</a></li>
								<li style="font-size: 12px;"><a href="http://{{ $_SERVER['SERVER_ADDR'] }}/hms_inv/dashboard">Tip / Linen</a></li>
								<li style="font-size: 12px;"><a href="{{ url('/turnaway') }}">Turn Away</a></li>
								<li style="font-size: 12px;"><a href="{{ url('/eventlogs') }}">Event Logs</a></li>
							</ul>
							<div class="w3_agile_login">
								<p style="color: white; font-size: 11px; margin-top: 16px;"><strong>Server Time: <span id="myDate"></span></strong></p>
							</div>
						</nav>
					</div>
				</nav>
			</div>
		</div>
	</div>
<!-- services -->
	<div class="services" style="background: #fff !important;">
		<div class="container-full" style="background: #fff !important;">
			@yield('content')
		</div>
	</div>
<!-- //services -->
<!-- stats -->
	<script src="assets/js/jquery.waypoints.min.js"></script>
	<script src="assets/js/jquery.countup.js"></script>
		<script>
			$('.counter').countUp();
		</script>
<!-- //stats -->
	<!-- footer -->

	<!-- //footer -->
	<!-- copyright -->
	{{--<div id="myFooter" class="agileits-w3layouts-copyright">
		<div class="container">
			<p>© 2018 Room Management System | Design by <a target="_blank" href="http://victoriacourt.biz/index.php">NERDVANA</a></p>
		</div>
	</div>--}}


<script>
function formatDate(date) {
  var monthNames = [
    "January", "February", "March",
    "April", "May", "June", "July",
    "August", "September", "October",
    "November", "December"
  ];

  var day = date.getDate();
  var monthIndex = date.getMonth();
  var year = date.getFullYear();
  var hour = date.getHours();
  var minute = date.getMinutes();
  var second = date.getSeconds();

  if(hour > 12){
  	time = "PM";
  	hour = hour - 12;

  }else {
  		time = "AM"

  }

	hour = checkTime(hour);
	minute = checkTime(minute);
	second = checkTime(second);

  return monthNames[monthIndex] + ' ' + day + ', ' + year + ' '+ hour + ':' + minute + ':' + second + ' ' + time;
}

function checkTime(time){
	if(time < 10){
  		time = "0"+ time;
  	}

  	return time;
}

function countUp(){
	var upTime;
	var date = new Date();
	datetime = formatDate(date);

	$('#myDate').html(datetime);

	clearTimeout(upTime);
	upTime = setTimeout(function(){ countUp(); },1000);
}
</script>
@yield('page-script')
</body>
</html>