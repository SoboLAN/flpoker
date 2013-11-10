<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Result</title>
 
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
 
<h1>FileList Poker Add Result</h1>
 
<script type="text/javascript">
 
$(document).ready(function()
{
	<?php echo $nameList; ?>

    var counter = 16;
 
    $("#addButton").click(function ()
	{
		if (counter > 25)
		{
			alert("Maximum 25 textboxes allowed");
			return false;
		}   

		var newTextBoxDiv = $(document.createElement('div'))
							.attr("id", 'TextBoxDiv' + counter)
							.attr('class', 'ui-widget');

		newTextBoxDiv.after().html(
		'<label>Position: </label>' +
			  '<input type="text" name="position' + counter + '" id="position' + counter + '" value="" />' +
		  '<label>Player: </label>' +
			  '<input type="text" name="player' + counter + '" id="player' + counter + '" value="" />' +
		  '<label>Points: </label>' +
			  '<input type="text" name="points' + counter + '" id="points' + counter + '" value="" />');

		newTextBoxDiv.appendTo("#TextBoxesGroup");
		
		$('#player' + counter).autocomplete({
			source: availableNames
		});

		counter++;
     });
 
	$("#removeButton").click(function ()
	{
		if(counter==1)
		{
			alert("No more textbox to remove");
			return false;
		}   
 
		counter--;
 
        $("#TextBoxDiv" + counter).remove();
     });
 
     $("#getButtonValue").click(function ()
	 {
		var msg = '';
		for (i = 1; i < counter; i++)
		{
		  msg += "\n Position " + i + ": " + $('#position' + i).val();
		  msg += "\n Player " + i + ": " + $('#player' + i).val();
		  msg += "\n Points " + i + ": " + $('#points' + i).val();
		}
		alert(msg);
	});
	
	for (j = 1; j < counter; j++)
	{
		$('#player' + j).autocomplete({
			source: availableNames
		});
	}
});
</script>

<form action="add.result.execute.php" method="POST" target="_blank">
	<div>
		<label>Tournament ID: </label>
		<input type="text" name="tournamentid" id="tournamentid" value="" />
	</div>
	<div id='TextBoxesGroup'>
		<div id="TextBoxDiv1" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position1" id="position1" value="" />
			<label>Player: </label>
			<input type="text" name="player1" id="player1" value="" />
			<label>Points: </label>
			<input type="text" name="points1" id="points1" value="" />
		</div>
		<div id="TextBoxDiv2" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position2" id="position2" value="" />
			<label>Player: </label>
			<input type="text" name="player2" id="player2" value="" />
			<label>Points: </label>
			<input type="text" name="points2" id="points2" value="" />
		</div>
		<div id="TextBoxDiv3" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position3" id="position3" value="" />
			<label>Player: </label>
			<input type="text" name="player3" id="player3" value="" />
			<label>Points: </label>
			<input type="text" name="points3" id="points3" value="" />
		</div>
		<div id="TextBoxDiv4" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position4" id="position4" value="" />
			<label>Player: </label>
			<input type="text" name="player4" id="player4" value="" />
			<label>Points: </label>
			<input type="text" name="points4" id="points4" value="" />
		</div>
		<div id="TextBoxDiv5" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position5" id="position5" value="" />
			<label>Player: </label>
			<input type="text" name="player5" id="player5" value="" />
			<label>Points: </label>
			<input type="text" name="points5" id="points5" value="" />
		</div>
		<div id="TextBoxDiv6" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position6" id="position6" value="" />
			<label>Player: </label>
			<input type="text" name="player6" id="player6" value="" />
			<label>Points: </label>
			<input type="text" name="points6" id="points6" value="" />
		</div>
		<div id="TextBoxDiv7" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position7" id="position7" value="" />
			<label>Player: </label>
			<input type="text" name="player7" id="player7" value="" />
			<label>Points: </label>
			<input type="text" name="points7" id="points7" value="" />
		</div>
		<div id="TextBoxDiv8" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position8" id="position8" value="" />
			<label>Player: </label>
			<input type="text" name="player8" id="player8" value="" />
			<label>Points: </label>
			<input type="text" name="points8" id="points8" value="" />
		</div>
		<div id="TextBoxDiv9" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position9" id="position9" value="" />
			<label>Player: </label>
			<input type="text" name="player9" id="player9" value="" />
			<label>Points: </label>
			<input type="text" name="points9" id="points9" value="" />
		</div>
		<div id="TextBoxDiv10" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position10" id="position10" value="" />
			<label>Player: </label>
			<input type="text" name="player10" id="player10" value="" />
			<label>Points: </label>
			<input type="text" name="points10" id="points10" value="" />
		</div>
		<div id="TextBoxDiv11" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position11" id="position11" value="" />
			<label>Player: </label>
			<input type="text" name="player11" id="player11" value="" />
			<label>Points: </label>
			<input type="text" name="points11" id="points11" value="" />
		</div>
		<div id="TextBoxDiv12" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position12" id="position12" value="" />
			<label>Player: </label>
			<input type="text" name="player12" id="player12" value="" />
			<label>Points: </label>
			<input type="text" name="points12" id="points12" value="" />
		</div>
		<div id="TextBoxDiv13" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position13" id="position13" value="" />
			<label>Player: </label>
			<input type="text" name="player13" id="player13" value="" />
			<label>Points: </label>
			<input type="text" name="points13" id="points13" value="" />
		</div>
		<div id="TextBoxDiv14" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position14" id="position14" value="" />
			<label>Player: </label>
			<input type="text" name="player14" id="player14" value="" />
			<label>Points: </label>
			<input type="text" name="points14" id="points14" value="" />
		</div>
		<div id="TextBoxDiv15" class="ui-widget">
			<label>Position: </label>
			<input type="text" name="position15" id="position15" value="" />
			<label>Player: </label>
			<input type="text" name="player15" id="player15" value="" />
			<label>Points: </label>
			<input type="text" name="points15" id="points15" value="" />
		</div>
	</div>
	<p>
		<label>Password: </label>
		<input type='password' name='flpokerpassword' id='flpokerpassword' value ='' />
	</p>
	<input type='button' value='Add' id='addButton' />
	<input type='button' value='Remove' id='removeButton' />
	<input type='button' value='Get TextBox Value' id='getButtonValue' />
	<input type='submit' value='Submit' id='submitbutton' />
</form>
<h2 style="font-weight:bold; color: red">WARNING: This cannot be undone. Be VERY SURE before submitting.</h2>

</body>
</html>