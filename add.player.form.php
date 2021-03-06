<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Config;

$site = new Site();

$jQueryPath = Config::getValue('path_jquery');
$jQueryUIPath = Config::getValue('path_jqueryui');
$jQueryCSSPath = Config::getValue('path_jqueryui_css');

echo '<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Player</title>';

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
 
<h1>FileList Poker Add Player</h1>
 
<script type="text/javascript">
 
$(document).ready(function()
{
    $('#registrationdate').datepicker ({dateFormat: 'yy-mm-dd', firstDay: 1});
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

</body>
</html>