<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Tournament</title>
 
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<link rel="stylesheet" href="css/dark-hive/jquery-ui-1.10.3.custom.min.css" />
 
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
 
<h1>FileList Poker Add Tournament</h1>
 
<script type="text/javascript">
 
$(document).ready(function()
{
	$('#tournamentdate').datepicker ({dateFormat: 'yy-mm-dd'});
});
</script>

<form action="add.tournament.execute.php" method="POST" target="_blank">
	<div>
		<label>Tournament Date: </label>
		<input type="text" name="tournamentdate" id="tournamentdate" value="" />
	</div>
	<div>
		<label>Participants: </label>
		<input type="text" name="participants" id="participants" value="" />
	</div>
	<div>
		<label>Duration Hours: </label>
		<input type="text" name="hours" id="hours" value="" />
	</div>
	<div>
		<label>Duration Minutes: </label>
		<input type="text" name="minutes" id="minutes" value="" />
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