<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FireLines | Concept</title>
    <link rel="stylesheet" href="src/css/vendor.css" media="all"/>
    <link rel="stylesheet" href="src/css/style.css" media="all"/>
    <script type="text/javascript" src="src/js/vivus.min.js"></script>
    <script type="text/javascript" src="src/js/svg.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <?php include('partials/header.php'); ?>
    <div class="row">
        <div class="col-md-12 col-lg-4">
            <div id="sidebar">
                <div class="row">
                    <div class="col-12">
                        <h2>Latest reports</h2>
                        <ul class="list">
                            <li>
                                <div class="label prio-1"></div>
                                <p class="name">Huisbrand</p>
                                <p class="location">Makkinga</p>
                                <p class="time">10:56</p>
                            </li>
                            <li>
                                <div class="label prio-2"></div>
                                <p class="name">Brand door kortsluiting</p>
                                <p class="location">Utrecht</p>
                                <p class="time">10:56</p>
                            </li>
                            <li>
                                <div class="label prio-3"></div>
                                <p class="name">Kat in boom</p>
                                <p class="location">Den Bosch</p>
                                <p class="time">10:53</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-12 col-lg-8">
            <div id="map">
                <div id="map-overlay">
                    <object id="map-svg" type="image/svg+xml" data="src/img/nl.svg"></object>
                </div>
				<div id="data">

				</div>
            </div>
        </div>
    </div>
</div>

<!--suppress BadExpressionStatementJS -->
<script type="text/javascript">

	function toRelativeCoordinate(axis, value) {
		// x-axis
        var width = 600;
        var bottomLat = 13557;
        var topLat = 278013;
		// y-axis
        var height = 675;
        var bottomLang = 615084;
        var topLang = 306877;
        var result;
	    if (axis === 'x') {
	        result = (value - bottomLat) * ((20 - width) / (bottomLat - topLat));
		} else {
	        result = (value - bottomLang) * ((20 - height) / (bottomLang - topLang));
		}
		return result;
	}

	var data;
	function updateData() {
        var xmlhttp;
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                data = JSON.parse(this.response);
                var newCoordinates = [];
                newCoordinates = data.map(function(obj) {
                    var rObj = {x: '', y: '', prio: ''};
                    rObj.x = toRelativeCoordinate('x', parseInt(obj.Latitude));
                    rObj.y = toRelativeCoordinate('y', parseInt(obj.Longitude));
                    rObj.prio = 2;
                    return rObj;
                });
            	updateGraph(newCoordinates);
			}
        };
        xmlhttp.open("GET", "get_coordinates.php");
        xmlhttp.send();
    }

    setInterval(function() {
	    updateData()
	}, 5000);

	function updateGraph(data) {
        var options = {
            type: 'oneByOne',
            duration: 50,
            animTimingFunction: Vivus.EASE,
            pathTimingFunction: Vivus.EASE,
            reverseStack: true,
            start: 'autostart'
        };
        var vivus = new Vivus('map-svg', options);
        var overlay = SVG('map-overlay');

        var index = 0;
        var firstX = data[0].x + data[0].prio * 5;
        var firstY = data[0].y + data[0].prio * 5;
        data.map(location => {
            overlay.circle(location.prio * 10)
            .attr({'opacity': 0})
            .move(location.x, location.y)
            .animate(300, '<>', 800 + (index % 2 === 0 ? index * 300 : index * 250))
            .attr({'opacity': 1})
            .attr({fill: '#000'});
        overlay.line(firstX, firstY, location.x + location.prio * 5, location.y + location.prio * 5)
            .attr({'opacity': 0})
            .animate(300, '<>', 800 + (index * 200))
            .attr({'opacity': 1})
            .attr({stroke: '#000'});
        firstX = location.x + location.prio * 5;
        firstY = location.y + location.prio * 5;
        index++;
    })
	}
</script>
</body>
</html>