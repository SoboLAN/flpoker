<p>{statistics_aggresion_text}</p>
<script type="text/javascript">
    $(function () {
        $('#highc-aggresion').highcharts({
            chart: {
                type: 'spline'
            },
            title: {
                text: '{statistics_aggresion_charttitle}'
            },
            subtitle: {
                text: '{statistics_aggresion_chartsubtitle}'
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
                    text: '{statistics_aggresion_avgaggressionfactor}'
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
                name: '{statistics_aggresion_avgaggressionfactor}',
                // Note that in JavaScript, months start at 0 for January, 1 for February etc.
                data: [ {aggressionFactors} ]
            }]
        });
    });
</script>
<div id="highc-aggresion" style="width:90%; height: 450px; margin: 0 auto;"></div>