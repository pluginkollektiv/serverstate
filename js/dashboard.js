google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {
	/* Init */
	var id,
		rows = [],
		output = jQuery('#serverstate_chart'),
		data = new google.visualization.DataTable();

	/* Nichts im Cache? */
	if ( typeof(serverstate) === 'undefined' ) {
		showSpinner(20, 16, output);

		jQuery.ajax(
			{
				'url': ajaxurl,
				'data': {
					'action': 'serverstate'
				},
				'dataType': 'JSON',
				'success': function(response) {
					serverstate = response;
					drawChart();
				}
			}
		);

		return;
	}

	/* Fehler? */
	if ( typeof(serverstate.error) !== 'undefined' ) {
		return output.text(serverstate.error);
	}

	/* Extrahieren */
	var day = serverstate.day.split(','),
		uptime = serverstate.uptime.split(','),
		response = serverstate.response.split(',');

	/* Loopen */
	for (id in day) {
		rows[id] = [day[id], parseInt(response[id]), parseInt(uptime[id])];
	}

	data.addColumn('string', 'Datum');
	data.addColumn('number', 'Antwortzeit in ms');
	data.addColumn('number', 'Erreichbarkeit in %');
	data.addRows(rows);

	var chart = new google.visualization.AreaChart(output.get(0));

  	chart.draw(
  		data,
  		{
			width: parseInt(jQuery('#serverstate_chart').parent().width(), 10),
			height: 120,
			legend: 'none',
			pointSize: 6,
			lineWidth: 3,
			gridlineColor: '#ececec',
			colors:['#39C'],
			reverseCategories: true,
			backgroundColor: 'transparent',
			vAxis: {
				baselineColor: 'transparent',
				textPosition: 'in',
				textStyle: {
					color: '#8F8F8F',
					fontSize: 10
				}
			},
			hAxis: {
				textStyle: {
					color: '#3399CC',
					fontSize: 10
				}
			},
			chartArea: {
				width: "100%",
				height: "100%"
			}
		}
	);
}


/* Spinner */
function showSpinner(size, bars, target) {
	/* Anlegen */
	var $canvas = jQuery('<canvas />').attr(
		{
			'width': size,
			'height': size
		}
	);

	/* Kein Support? */
	if ( !$canvas[0].getContext ) {
		return;
	}

	/* Zuweisen */
	target.append($canvas);

	/* Eigenschaften */
	ctx = $canvas[0].getContext('2d');
	ctx.translate(size/2,size/2);

	/* Loopen */
	setInterval(
		function() {
			ctx.clearRect(-size/2,-size/2,size,size);
			ctx.rotate(Math.PI*2/bars);

			for (var i=0; i<bars; i++) {
				ctx.beginPath();
				ctx.moveTo(0,size/4);
				ctx.lineTo(0,size/2);
				ctx.strokeStyle = 'rgba(0,0,0,' + i/bars + ')';
				ctx.stroke();
				ctx.rotate(Math.PI*2/bars);
			}
		},
		50
	);
}