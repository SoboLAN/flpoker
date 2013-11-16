<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Player</title>
 
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui-1.10.3.custom.css" />
 
<style type="text/css">
	div{
		padding:8px;
	}
	
	div label {
		padding: 8px;
	}
	
	body {
		background:#BCD5E1;
	}
</style>
 
</head>

<body>
 
<h1>FileList Poker Add Player</h1>
 
<script type="text/javascript">
 
$(document).ready(function()
{
	$('#registrationdate').datepicker ({dateFormat: 'yy-mm-dd'});
});
</script>

<form action="add.player.execute.php" method="POST" target="_blank">
	<div>
		<label>Registration Date: </label>
		<input type="text" name="registrationdate" id="registrationdate" value="" />
	</div>
	<div>
		<label>Name Pokerstars: </label>
		<input type="text" name="nameps" id="nameps" value="" />
	</div>
	<div>
		<label>Name FileList: </label>
		<input type="text" name="namefl" id="namefl" value="" />
	</div>
	<div>
		<label>ID FileList: </label>
		<input type="text" name="idfl" id="idfl" value="" />
	</div>
	<p>
		<label>Password: </label>
		<input type='password' name='flpokerpassword' id='flpokerpassword' value ='' />
	</p>
	<input type='submit' value='Submit' id='submitbutton' />
</form>
<h2 style="font-weight:bold; color: red">WARNING: This cannot be undone. Be VERY SURE before submitting.</h2>

</body>
</html>