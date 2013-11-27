<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Prize</title>
 
<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Main\Logger;

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

try {
    $db = Database::getConnection();
    
    $result = $db->query (
        'SELECT name_filelist ' .
        'FROM players ' .
        'WHERE name_filelist IS NOT NULL ' .
        'ORDER BY name_filelist ASC');
} catch (\PDOException $e) {
    Logger::log('rendering add.prize.form failed: ' . $e->getMessage());
    die('There was an error');
}

$names = array();
foreach ($result as $name) {
    $names[] = $name->name_filelist;
}

$nameList = 'var availableNames = ["' . implode ('", "', $names) . '"];';

?>
 
<body>
 
<h1>FileList Poker Add Prize</h1>
 
<script type="text/javascript">
 
$(document).ready(function()
{
    <?php echo $nameList; ?>

    $('#player').autocomplete({source: availableNames});
    
    $('#purchasedate').datepicker ({dateFormat: 'yy-mm-dd'});
});
</script>

<form action="add.prize.execute.php" method="POST" target="_blank">
    <div>
        <label>Player: </label>
        <input type="text" name="player" id="player" value="" />
    </div>
    <div>
        <label>Prize Text: </label>
        <input type="text" name="prize" id="prize" value="" />
    </div>
    <div>
        <label>Cost: </label>
        <input type="text" name="cost" id="cost" value="" />
    </div>
    <div>
        <label>Purchase Date: </label>
        <input type="text" name="purchasedate" id="purchasedate" value="" />
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