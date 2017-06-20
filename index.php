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
						<ul class="list" id="latest-reports">
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
        var width = 580;
        var bottomLat = 8257;
        var topLat = 278013;
        // y-axis
        var height = 675;
        var bottomLang = 615084;
        var topLang = 306877;
        var result;
        if (axis === 'x') {
            result = (value - bottomLat) * ((0 - width) / (bottomLat - topLat));
        } else {
            result = (value - bottomLang) * ((0 - height) / (bottomLang - topLang));
        }
        return result;
    }

    function getClassForPrio(prio) {
        if(prio == 2) {
            return 'prio-2';
        } else if (prio == 3) {
            return 'prio-3';
        } else {
            return 'prio-1';
        }
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
                    if(data.length === 1) {
						var audio = new Audio('src/sound/brand.mp3');
						audio.play();
					}
                    var newCoordinates = [];
                    newCoordinates = data.map(function(obj) {
                        var rObj = {};
                        rObj.x = toRelativeCoordinate('x', parseInt(obj.rdx));
                        rObj.y = toRelativeCoordinate('y', parseInt(obj.rdy));
                        rObj.title = obj.Title;
                        rObj.prio = obj.prio;
                        rObj.province = obj.Province;
                        lastid = obj.ID;
                        return rObj;
                    });
                    coordinates = coordinates.concat(newCoordinates);
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
        var prioRadius = 30;
        var i = coordinates.length;
        if(index = 1) {
            firstX = coordinates[0].x;
            firstY = coordinates[0].y;
        } else {
            var firstX = coordinates[i - 2].x;
            var firstY = coordinates[i - 2].y;
        }
        data.map(location => {
            // Draw a circle with a radius relative to the priority
            overlay.circle(prioRadius/location.prio)
            .attr({'opacity': 0})
            .move(location.x - ((prioRadius/location.prio) / 2), location.y - ((prioRadius/location.prio) / 2))
            .attr({'class': getClassForPrio(location.prio)})
            .animate(300, '<>', 800 + (index * 200))
            .attr({'opacity': 1})
        // And a line from previous circle to new circle
        overlay.line(firstX, firstY, location.x, location.y)
            .attr({'opacity': 0})
            .animate(300, '<>', 800 + (index * 200))
            .attr({'opacity': 1});
        firstX = location.x;
        firstY = location.y;
        index++;
    })
		updateList();
    }

    function updateList() {
        var list = document.getElementById('latest-reports');
        var innerList = '';
        var element = '';
        sortedCoordinates = coordinates.reverse();
        sortedCoordinates.map(location => {
            var prioClass = 'label prio-' + location.prio;
            element = '<li>' + '<div class=\'' + prioClass + '\'></div><p class=\'name\'>' + location.title + '</p><p class=\'location\'>' + location.province + '</p></li>';
        	innerList = innerList + ' ' + element;
        	return element;
    		});
        list.innerHTML = innerList;
	}
</script>
</body>
</html>