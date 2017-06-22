var coordinates = [];
var tooltip = document.getElementById('tooltip');
var highlightedListItemId = 0;
var maximumNumberOfReports = 50;

// Set number of displayed reports
document.getElementById('number-of-reports').innerHTML = maximumNumberOfReports;

// Highlight list item
function highlightListItem(id) {
    var listItem = document.getElementById('list-' + id);
    listItem.setAttribute('class', 'highlighted');
    highlightedListItemId = id;
}

function unhighlightListItem(id) {
    var listItem = document.getElementById('list-' + id);
    listItem.setAttribute('class', '');
    highlightedListItemId = 0;
}

// Tooltip functionality
function showTooltip(id, title, prio) {
    highlightListItem(id);
    tooltip.setAttribute('class', 'show');
    tooltip.innerHTML = '<div class=\"tooltip-inner prio-' + prio + '\"><p class=\"label prio-' + prio + '\"</p><p class=\"title\">' +
        title + '</p></div>';
}

function hideTooltip() {
    tooltip.setAttribute('class', 'hide');
    unhighlightListItem(highlightedListItemId);
}

document.addEventListener("mousemove", function(e) {
    var styleString = '';
    var tooltipHeight = document.getElementById('tooltip').clientHeight;
    if(e.clientX > (window.innerWidth - 320)) {
        if(e.clientY > (window.innerHeight - 200)) {
            styleString = 'left: ' + (e.clientX - 295) + 'px; top:' + (e.clientY - (tooltipHeight + 5)) + 'px;';
        }
        else {
            styleString = 'left: ' + (e.clientX - 295) + 'px; top:' + (e.clientY + 5) + 'px';
        }
    } else {
        if(e.clientY > (window.innerHeight - 200)) {
            styleString = 'left: ' + (e.clientX + 5) + 'px; top:' + (e.clientY - (tooltipHeight + 5)) + 'px';
        }
        else {
            styleString = 'left: ' + (e.clientX + 5) + 'px; top:' + (e.clientY + 5) + 'px';
        }
    }
    tooltip.setAttribute('style', styleString);
});

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

function isNumber(obj) { return !isNaN(parseFloat(obj)) }

function getClassForPrio(prio) {
    if(isNumber(prio)) {
        return 'prio-' + prio;
    } else {
        return 'prio-1'
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
                    rObj.id = obj.ID;
                    rObj.x = toRelativeCoordinate('x', parseInt(obj.rdx));
                    rObj.y = toRelativeCoordinate('y', parseInt(obj.rdy));
                    rObj.title = obj.Title;
                    rObj.prio = obj.prio;
                    rObj.province = obj.Province;
                    rObj.time = obj.Pubdate.substr(obj.Pubdate.length - 8);
                    lastid = obj.ID;
                    return rObj;
                });
                newCoordinates = newCoordinates.sort(function(a, b) { return b.id - a.id });
                newCoordinates = newCoordinates.slice(0, maximumNumberOfReports);
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
    var animDuration = (data.length < 10 ? 150 : 2000 / data.length);
    data.sort(function(a, b) { return a.id - b.id });
    data.map(location => {
        // Draw a circle at the end of the line
        overlay.circle(5)
        .attr({'opacity': 0})
        .move(location.x - 2.5, location.y - 2.5)
        .attr({'class': getClassForPrio(location.prio) + ' small'})
        .animate(300, '<>', 200 + (index * animDuration))
        .attr({'opacity': 1});
    // Draw a circle with a radius relative to the priority
    var circle = overlay.circle(prioRadius/location.prio);
    circle.attr({'opacity': 0})
        .move(location.x - ((prioRadius/location.prio) / 2), location.y - ((prioRadius/location.prio) / 2))
        .attr({'class': getClassForPrio(location.prio)})
        .animate(300, '<>', 200 + (index * animDuration))
        .attr({'opacity': 1});

    circle.node.addEventListener('mouseout', function() { hideTooltip() });
    circle.node.addEventListener('mouseover', function() { showTooltip(location.id, location.title, location.prio)});

    // And a line from previous circle to new circle
    overlay.line(firstX, firstY, location.x, location.y)
        .attr({'opacity': 0})
        .animate(300, '<>', 200 + (index * animDuration))
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
    coordinates.sort(function(a, b) { return b.id - a.id });
    coordinates.map(location => {
        var prioClass = 'label prio-' + location.prio;
    element = '<li id=\"list-' + location.id + '\">' + '<div class=\'' + prioClass + '\'></div>' +
        '<p class=\'name\'>' + location.title + '</p>' +
        '<p class=\'location\'>' + location.province + '</p>' +
        '<p class=\'time\'>' + location.time + '</p>' +
        '</li>';
    innerList = innerList + ' ' + element;
    return element;
});
    list.innerHTML = innerList;
}