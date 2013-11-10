<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Bonus</title>
 
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

<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;

$db = Database::getConnection();

try
{
	$result = $db->query (
		'SELECT name_pokerstars ' .
		'FROM players ' .
		'WHERE name_pokerstars IS NOT NULL ' .
		'ORDER BY name_pokerstars ASC');
}
catch (\PDOException $e)
{
	die ('There was an error');
}

$names = array();
foreach ($result as $name)
{
	$names[] = $name->name_pokerstars;
	
}

$nameList = 'var availableNames = ["' . implode ('", "', $names) . '"];';

?>
 
<body>
 
<h1>FileList Poker Add Bonus</h1>
 
<script type="text/javascript">
 
$(document).ready(function()
{
	<?php echo $nameList; ?>

	$('#player').autocomplete({source: availableNames});
	
	$('#bonusdate').datepicker ({dateFormat: 'yy-mm-dd'});
});
</script>

<form action="add.bonus.execute.php" method="POST" target="_blank">
	<div>
		<label>Player: </label>
		<input type="text" name="player" id="player" value="" />
	</div>
	<div>
		<label>Bonus Description: </label>
		<input type="text" name="bonusdesc" id="bonusdesc" value="" />
	</div>
	<div>
		<label>Tournament ID: </label>
		<input type="text" name="tid" id="tid" value="" />
	</div>
	<div>
		<label>Bonus Value: </label>
		<input type="text" name="bonusvalue" id="bonusvalue" value="" />
	</div>
	<div>
		<label>Bonus Date: </label>
		<input type="text" name="bonusdate" id="bonusdate" value="" />
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