<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Tournament</title>
 
<?php

require_once 'autoload.php';
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