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
	var coordinates = [];

	// Map X-RD and Y-RD (Rijksdriehoeksmeting) to the SVG map of the Netherlands
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
	        result = (value - bottomLat) * ((10 - width) / (bottomLat - topLat));
		} else {
	        result = (value - bottomLang) * ((10 - height) / (bottomLang - topLang));
		}
		return result;
	}

    function isInt(value) {
        return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
    }

    // Parse priority value from p2000 report title
	function parsePriorityFromTitle(title) {
	    var lowerCaseTitle = title.toLowerCase();
	    var prioFound = lowerCaseTitle.indexOf('prio');
	    if(prioFound !== -1) {
	        var firstCharacter = lowerCaseTitle.charAt(prioFound);
	        var secondCharacter = lowerCaseTitle.charAt(prioFound);
	        if(isInt(firstCharacter)) {
                return parseInt(firstCharacter);
            }
			else if(isInt(secondCharacter)) {
			    return parseInt(secondCharacter);
			}
		}
		return 3;
	}

	var data;
	var lastid = 0;

	// Receive data via AJAX call from Database
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
                if(this.responseText !== 'UP-TO-DATE') {
                    data = JSON.parse(this.response);
                    var newCoordinates = [];
                    newCoordinates = data.map(function(obj) {
                        var rObj = {x: '', y: '', prio: '', title: ''};
                        rObj.x = toRelativeCoordinate('x', parseInt(obj.Latitude));
                        rObj.y = toRelativeCoordinate('y', parseInt(obj.Longitude));
                        rObj.prio = parsePriorityFromTitle(obj.Title);
                        rObj.title = obj.Title;
                        lastid = obj.ID;
                        return rObj;
                    });
                    coordinates = coordinates.concat(newCoordinates);
                    console.log(coordinates);
                    updateGraph(newCoordinates);
				}
			}
        };
        xmlhttp.open("GET", "get_coordinates.php?lastid="+lastid);
        xmlhttp.send();
    }

    // Update graph every n seconds
    setInterval(function() {
	    updateData()
	}, 1000);

	// SVG canvas options
    var vivus = new Vivus('map-svg', options);
    var overlay = SVG('map-overlay');

    var options = {
        type: 'oneByOne',
        duration: 50,
        animTimingFunction: Vivus.EASE,
        pathTimingFunction: Vivus.EASE,
        reverseStack: true,
        start: 'autostart'
    };

	function updateGraph(data) {
		// Update Graph lines
        var index = 0;
        var firstX = data[0].x + data[0].prio * 2.5;
        var firstY = data[0].y + data[0].prio * 2.5;
        if(data.length <= 1) {
            // If we only have one new coordinate, grab the last coordinate from the coordinate list
			// and use this as the first coordinate to draw a line from
            var i = coordinates.length;
            var firstX = coordinates[i - 1].x + coordinates[i - 1].prio * 2.5;
            var firstY = coordinates[i - 1].x + coordinates[i - 1].prio * 2.5;
        }
        data.map(location => {
            // Draw a circle with a radius relative to the priority
            overlay.circle(location.prio * 5)
            .attr({'opacity': 0})
            .move(location.x, location.y)
            .animate(300, '<>', 800 + (index % 2 === 0 ? index * 300 : index * 250))
            .attr({'opacity': 1})
            .attr({fill: '#000'});
            // And a line from previous circle to new circle
        overlay.line(firstX, firstY, location.x + location.prio * 2.5, location.y + location.prio * 2.5)
            .attr({'opacity': 0})
            .animate(300, '<>', 800 + (index * 200))
            .attr({'opacity': 1})
            .attr({stroke: '#000'});
        firstX = location.x + location.prio * 2.5;
        firstY = location.y + location.prio * 2.5;
        index++;
    })
	}
</script>
</body>
</html>