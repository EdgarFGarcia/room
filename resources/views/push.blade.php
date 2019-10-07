<html>
	<head>
		
	</head>
	<body>
	
		<textarea id="message"></textarea>
		<input type="text" id="header"/>
		<button type="submit" id="btn">Press Me</button>

		<script type="text/javascript" src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$(document).on('click', '#btn', function(){

					var message = $('#message').val();
					var header = $('#header').val();

					$.ajax({

						url: '{{ url("api/device/pushnotification") }}',
						method: 'POST',
						dataType: 'json',
						data:{
							message : message,
							header : header
						},
						success:function(r){
							console.log(r);
						},
						error:function(r){
							console.log(r);
						}


					});
					
				});
			});
		</script>


	</body>
</html>