<!DOCTYPE html>
<html lang="en">
<head>
<title>@yield('title')</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Negotiation Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>


        {{-- CSS --}}
		<link href="{{ asset('fullcalendar/css/fullcalendar.min.css') }}" rel="stylesheet">
		<link href="{{ asset('fullcalendar/css/bootstrap.min.css') }}" rel="stylesheet">
		<link href="{{ asset("css/bootstrap-datetimepicker.min.css") }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset("css/style.css") }}" rel="stylesheet" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset("assets/css/font-awesome.min.css") }}" />
		<link rel="stylesheet" href="{{ asset("css/toast.css") }}">
		<link href="{{ asset("css/datatables.css") }}" rel="stylesheet" type="text/css">
		

		{{-- JS --}}
		<script src="{{ asset('fullcalendar/js/moment.js') }}"></script>
		{{-- <script src="{{ asset('fullcalendar/js/jquery-1.1.1.js') }}"></script> --}}
		<script type="text/javascript" src="{{ asset("assets/js/jquery-2.1.4.min.js") }}"></script>
		<script type="text/javascript" src="{{ asset('fullcalendar/js/bootstrap.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
		<script src="{{ asset('fullcalendar/js/jquery-ui.js') }}"></script>
		<script src="{{ asset('fullcalendar/js/fullcalendar.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset("assets/js/socket.io.js") }}"></script>
		<script src="{{ asset("assets/js/main.js") }}"></script>
		<script src="{{ asset("js/toast.min.js") }}" type="text/javascript"></script>
		<script type="text/javascript" src="{{ asset('js/bootstrap4.js') }}"></script>
        <script src="{{ asset('js/datatables.js') }}"></script>

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
					<img src="{{ asset("assets/images/vc_logo_webtest.png") }}" style="max-width: 200px;"/>
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
		<div class="container">
			<nav class="navbar navbar-default">
				<div class="navbar-header navbar-left">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<!-- <h1><a class="navbar-brand" href="index.html">Negotiation</a></h1> -->
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
					<nav class="cl-effect-13" id="cl-effect-13">
						<div class="col-md-12 row">
							<ul class="nav navbar-nav">
								<li style="font-size: 12px;"><a href="{{ url('/') }}">Room Management</a></li>
								<li style="font-size: 12px;"><a href="{{ url('/historical') }}">For Repairs</a></li>
								<li style="font-size: 12px;"><a href="http://{{ $_SERVER['SERVER_ADDR'] }}/hms_inv/dashboard">Tip / Linen</a></li>
								<li style="font-size: 12px;"><a href="{{ url('/turnaway') }}">Turn Away</a></li>
								<li style="font-size: 12px;"><a href="{{ url('/eventlogs') }}">Event Logs</a></li>
							</ul>
						</div>
						<div class="col-md-12">
							<div class="">
								<p style="color: white; font-size: 10px; margin-top: 16px; text-align: center;"><strong>Server Time: <span id="myDate"></span></strong></p>
							</div>
						</div>


					</nav>
				</div>
			</nav>
		</div>
	</div>
	</div>
<!-- services -->
	<div class="services">
		<div class="container">
		@yield('content')
		</div>
	</div>
<!-- //services -->
<!-- stats -->
	<script src="{{ asset("assets/js/jquery.waypoints.min.js") }}"></script>
	<script src="{{ asset("assets/js/jquery.countup.js") }}"></script>
		<script>
			$('.counter').countUp();
		</script>
<!-- //stats -->
	<!-- footer -->

	<!-- //footer -->
	<!-- copyright -->
	<div id="myFooter" class="agileits-w3layouts-copyright">
		<div class="container">
			<p>© 2018 Room Management System | Design by <a target="_blank" href="http://victoriacourt.biz/index.php">NERDVANA</a></p>
		</div>
	</div>


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