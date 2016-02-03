<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Content\PlayersContent;

$site = new Site();

$jQueryPath = Config::getValue('path_jquery');
$jQueryUIPath = Config::getValue('path_jqueryui');
$jQueryCSSPath = Config::getValue('path_jqueryui_css');

$playersContent = new PlayersContent();
$db = Database::getConnection();

$playersNames = $playersContent->getAllPlayersNames();

$nameList = 'var availableNames = ["' . implode ('", "', $playersNames) . '"];';

echo '<!DOCTYPE html>
<html>
<head>
<title>FileList Poker Add Result</title>';

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
 
<h1>FileList Poker Add Result</h1>

<form action="add.result.execute.php" method="POST" target="_blank">
    <div id="tournament-id-info">
        <label>Tournament ID: </label>
        <input type="text" name="tournamentid" id="tournamentid" value="" />
    </div>
    <div id='text-boxes-group'>

    </div>
    <p>
        <label>Password: </label>
        <input type='password' name='flpokerpassword' id='flpokerpassword' value ='' />
    </p>
    <input type='button' value='Get TextBox Value' id='getButtonValue' />
    <input type='submit' value='Submit' id='submitbutton' />
</form>
 
<script type="text/javascript">
 
$(document).ready(function() {
    <?php echo $nameList; ?>
    
    $("#tournamentid").blur(function() {
        var tournamentId = $("#tournamentid").val();
        
        var result = $.ajax({
            url: '<?php echo Config::getValue('site_url'); ?>get.new.tournament.details.php',
            type: 'GET',
            data: {
                id: tournamentId
            },
            dataType: 'json',
            async: true,
            success: function(data) {
                $("#text-boxes-group").empty();
                $("#infolabel").remove();

                var infoLabel = $(document.createElement('span')).attr('id', 'infolabel');
                infoLabel.after().html('Tournament has ' + data.nr_participants + ' participants, therefore ' + data.nr_payed_positions + ' will be payed.');
                infoLabel.appendTo('#tournament-id-info');

                for (i = 1; i <= data.nr_payed_positions; i++) {
                    var newTextBoxDiv = $(document.createElement('div'))
                        .attr("id", 'text-box' + i)
                        .attr('class', 'ui-widget');

                    newTextBoxDiv.after().html(
                        '<label>Position: </label>' +
                            '<input type="text" name="position' + i + '" id="position' + i + '" value="' + i + '" />' +
                        '<label>Player: </label>' +
                            '<input type="text" name="player' + i + '" id="player' + i + '" value="" />' +
                        '<label>Knockouts: </label>' +
                            '<input type="text" name="kos' + i + '" id="kos' + i + '" value="0" />'
                    );

                    newTextBoxDiv.appendTo("#text-boxes-group");

                    $('#player' + i).autocomplete({source: availableNames});
                }
            }
        });
    });

     $("#getButtonValue").click(function () {
        var msg = '';
        var count = $("#text-boxes-group .ui-widget").length;
        for (i = 1; i <= count; i++) {
          msg += "\n Position " + i + ": " + $('#position' + i).val();
          msg += "\n Player " + i + ": " + $('#player' + i).val();
          msg += "\n Knockouts " + i + ": " + $('#kos' + i).val();
        }
        alert(msg);
    });
});
</script>

</body>
</html>