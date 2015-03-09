<?php

require_once 'autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Database;
use FileListPoker\Main\Config;

$site = new Site();

$jQueryPath = Config::getValue('path_jquery');
$jQueryUIPath = Config::getValue('path_jqueryui');
$jQueryCSSPath = Config::getValue('path_jqueryui_css');

$db = Database::getConnection();

$result = $db->query(
    'SELECT name_pokerstars ' .
    'FROM players ' .
    'WHERE name_filelist IS NOT NULL ' .
    'ORDER BY name_filelist ASC'
);

echo '<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Player of the Month</title>';

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

$names = array();
foreach ($result as $name) {
    $names[] = $name['name_pokerstars'];
}

$nameList = 'var availableNames = ["' . implode ('", "', $names) . '"];';

?>
 
<body>
 
<h1>FileList Poker Add Player of the Month</h1>
 
<script type="text/javascript">

$(document).ready(function()
{
    <?php echo $nameList; ?>

    $('#player').autocomplete({source: availableNames});
    
    $('#thedate').datepicker ({dateFormat: 'yy-mm', firstDay: 1});
});
</script>

<form action="add.player.month.execute.php" method="POST" target="_blank">
    <div>
        <label>Player: </label>
        <input type="text" name="player" id="player" value="" />
    </div>
    <div>
        <label>For Date: </label>
        <input type="text" name="thedate" id="thedate" value="" />
    </div>
    <p>
        <label>Password: </label>
        <input type='password' name='flpokerpassword' id='flpokerpassword' value ='' />
    </p>
    <input type='submit' value='Submit' id='submitbutton' />
</form>

</body>
</html>