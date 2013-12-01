<p>{statistics_tournaments_text}</p>
<script type="text/javascript">
	$(function () {
		$('#hcc').highcharts({
			chart: {
				type: 'spline'
			},
            title: {
				text: '{statistics_tournaments_charttitle}'
			},
            subtitle: {
				text: '{statistics_tournaments_chartsubtitle}'
			},
			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: { // don't display the dummy year
					month: '%e. %b',
                    year: '%b'
				}
			},
			yAxis: {
				title: {
					text: '{statistics_tournaments_playersline}'
				},
				min: 0
			},
            tooltip: {
				formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
                            Highcharts.dateFormat('%e %b %Y', this.x) +': '+ this.y;
				}
			},
			series: [{
				name: '{statistics_tournaments_playersline}',
				// Note that in JavaScript, months start at 0 for January, 1 for February etc.
                data: [ {tournamentParticipants} ]
                },
				{
                    name: '{statistics_tournaments_averageline}',
                    data: [ {tournamentAverage} ]
                }
			]
		});
	});
</script>
<div id="hcc" style="width:90%; height: 450px; margin: 0 auto;"></div>