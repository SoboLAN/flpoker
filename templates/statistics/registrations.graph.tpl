<p>{statistics_registrations_text}</p>
<script type="text/javascript">
	$(function () {
		$('#highc-reg').highcharts({
			chart: {
				type: 'spline'
			},
            title: {
				text: '{statistics_registrations_charttitle}'
			},
            subtitle: {
				text: '{statistics_registrations_chartsubtitle}'
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
					text: '{statistics_registrations_nrplayersline}'
				},
				min: 0
			},
            tooltip: {
				formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
                            Highcharts.dateFormat('%b %Y', this.x) +': '+ this.y;
				}
			},
			series: [{
				name: '{statistics_registrations_nrplayersline}',
				// Note that in JavaScript, months start at 0 for January, 1 for February etc.
                data: [ {clubRegistrants} ]
            }]
		});
	});
</script>
<div id="highc-reg" style="width:90%; height: 450px; margin: 0 auto;"></div>