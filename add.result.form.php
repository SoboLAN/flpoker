<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Result</title>
 
<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;
use FileListPoker\Main\Config;

$jQueryPath = Config::getValue('path_jquery');
$jQueryUIPath = Config::getValue('path_jqueryui');
$jQueryCSSPath = Config::getValue('path_jqueryui_css');

echo "<script type=\"text/javascript\" src=\"$jQueryPath\"></script>\n";
echo "<script type=\"text/javascript\" src=\"$jQueryUIPath\"></script>\n";
echo "<link rel=\"stylesheet\" href=\"$jQueryCSSPath\" />\n";
?>
 
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

$db = Database::getConnection();

try {
	$result = $db->query (
		'SELECT name_pokerstars ' .
		'FROM players ' .
		'WHERE name_pokerstars IS NOT NULL ' .
		'ORDER BY name_pokerstars ASC');
} catch (\PDOException $e) {
	die ('There was an error');
}

$names = array();
foreach ($result as $name) {
	$names[] = $name->name_pokerstars;
}

$nameList = 'var availableNames = ["' . implode ('", "', $names) . '"];';

?>
 
<body>
 
<h1>FileList Poker Add Result</h1>
 
<script type="text/javascript">
 
$(document).ready(function() {
	<?php echo $nameList; ?>

    var counter = 19;
 
    $("#addButton").click(function () {
        counter++;
        
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
		
		$('#player' + counter).autocomplete({source: availableNames});
     });
 
	$("#removeButton").click(function () {
		if(counter === 0) {
			alert("No more textboxes to remove");
			return false;
		}
 
        $("#TextBoxDiv" + counter).remove();
        
        counter--;
     });
 
     $("#getButtonValue").click(function () {
		var msg = '';
		for (i = 1; i < counter; i++) {
		  msg += "\n Position " + i + ": " + $('#position' + i).val();
		  msg += "\n Player " + i + ": " + $('#player' + i).val();
		  msg += "\n Points " + i + ": " + $('#points' + i).val();
		}
		alert(msg);
	});
	
	for (j = 1; j <= counter; j++) {
		$('#player' + j).autocomplete({source: availableNames});
	}
});
</script>

<form action="add.result.execute.php" method="POST" target="_blank">
	<div>
		<label>Tournament ID: </label>
		<input type="text" name="tournamentid" id="tournamentid" value="" />
	</div>
	<div id='TextBoxesGroup'>
    
    <?php
    
    function positionToPointsConverter($position) {
        switch($position) {
            case 1: return 30;
            case 2: return 27;
            case 3: return 25;
            case 4: return 23;
            case 5: return 21;
            case 6: return 19;
            case 7: return 17;
            case 8: return 15;
            case 9: return 13;
            case 10: return 10;
            case 11: return 9;
            case 12: return 8;
            case 13: return 7;
            case 14: return 6;
            case 15: return 5;
            case 16: return 4;
            case 17: return 3;
            case 18: return 2;
            case 19: return 1;
            default: return 0;
        }
    }
    
    for ($i = 1; $i <= 19; $i++) {
        echo "<div id=\"TextBoxDiv$i\" class=\"ui-widget\">
            <label>Position: </label>
            <input type=\"text\" name=\"position$i\" id=\"position$i\" value=\"$i\" />
            <label>Player: </label>
            <input type=\"text\" name=\"player$i\" id=\"player$i\" value=\"\" />
            <label>Points: </label>
            <input type=\"text\" name=\"points$i\" id=\"points$i\" value=\"" . positionToPointsConverter($i) . "\" />
        </div>";
    }
    
    ?>
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