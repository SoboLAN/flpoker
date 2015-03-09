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
    'SELECT player_id, name_pokerstars ' .
    'FROM players ' .
    'WHERE name_pokerstars IS NOT NULL ' .
    'AND LENGTH(name_pokerstars) > 0 ' .
    'ORDER BY name_filelist ASC'
);

echo '<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Edit Player</title>';

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

$players = array();
$names = array();
foreach ($result as $player) {
    $players[] = '{name: "' . $player['name_pokerstars'] . '", id: ' . $player['player_id'] . '}';
    $names[] = $player['name_pokerstars'];
}

$nameList = 'var availableNames = ["' . implode ('", "', $names) . '"];';
$playerList = 'var players = [' . implode (', ', $players) . '];';

?>

<body>

<h1>FileList Poker Edit Player</h1>

<script type="text/javascript">

$(document).ready(function() {
    <?php
        echo $nameList;
        echo "\n";
        echo $playerList;
    ?>

    $("#pname").autocomplete({source: availableNames});
    
    $("#pname").blur(function() {
        var playerName = $('#pname').val();

        var playerID = -1;
        for (var i = 0; i < players.length; i++) {
            if (players[i].name === playerName) {
                playerID = players[i].id;
                break;
            }
        }
        
        if (playerID !== -1) {
            var result = $.ajax({
                url: '<?php echo Config::getValue('site_url'); ?>get.player.details.php',
                type: 'GET',
                data: {
                    id: playerID
                },
                dataType: 'json',
                async: true,
                success: function(data) {
                    $('#fid').val(data.id_filelist);
                    $('#fname').val(data.name_filelist);
                    $('#pid').val(playerID);
                }
            });
        }
    });
});


</script>

<form action="edit.player.execute.php" method="POST" target="_blank">
    <div>
        <label>PokerStars Name: </label>
        <input type="text" name="pname" id="pname" value="" />
        <label>Type the name here. After, make it lose focus (press TAB or click somewhere outside the text box).</label>
    </div>
    <hr />
    <div>
        <label>FileList Name: </label>
        <input type="text" name="fname" id="fname" value="" />
    </div>
    <div>
        <label>FileList ID: </label>
        <input type="text" name="fid" id="fid" value="" />
    </div>
    <input type="hidden" id="pid" name="pid" value="" />
    <p>
        <label>Password: </label>
        <input type='password' name='flpokerpassword' id='flpokerpassword' value ='' />
    </p>
    <input type='submit' value='Submit' id='submitbutton' />
</form>
<h2>PLEASE be careful with this form: if you type in wrong data, the details of a totally different player may be changed.</h2>
</body>
</html>