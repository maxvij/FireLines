var coordinates = [];
var tooltip = document.getElementById('tooltip');
var highlightedListItemId = 0;
var maximumNumberOfReports = 50;
var dataLoaded = false;

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
                dataLoaded = true;
            }
        }
    };
    xmlhttp.open("GET", "get_coordinates.php?lastid="+lastid);
    xmlhttp.send();
}

// Filter priorites
var prioritySliderDiv = document.getElementById('priority-slider');
var prioritySliderValue = document.getElementById('priority-slider-value');

var prioritySlider = noUiSlider.create(prioritySliderDiv, {
    start: [1, 3],
    step: 1,
    connect: true,
    range: {
        'min': 1,
        'max': 3
    }
});

var prioritySliderValues = prioritySlider.get();

prioritySlider.on('update', function(){
    var values = prioritySlider.get();
    var readableValues = '';
    if(values[0] === values[1]) {
        readableValues = parseInt(values[0]).toFixed(0);
    } else {
        readableValues = parseInt(values[0]).toFixed(0) + '-' + parseInt(values[1]).toFixed(0);
    }
    prioritySliderValue.innerHTML = readableValues;
    prioritySliderValues = prioritySlider.get();
    if(dataLoaded) {
        resetGraph();
        updateGraph(coordinates);
    }
});

// Filter provinces
var provincesDiv = document.getElementById('provinces');
var provinceList = [];
var availableProvinces = [];
var provincesInnerHtml = [];
var selectedProvinceList = [];

function updateProvincesTags(coordinates) {
    provincesInnerHtml = [];
    availableProvinces = coordinates.map(function(coordinate) {
        if(provinceList.indexOf(coordinate.province) === -1) {
            provinceList.push(coordinate.province);
        }
    });
    var provincesTags = provinceList.map(function(province) {
        var provinceClassName = selectedProvinceList.indexOf(province) !== -1 ? 'province-tag selected' : 'province-tag';
            provincesInnerHtml.push('<div class=\"' + provinceClassName + '\" id=\"province-' + province + '\" onClick=\"javascript: selectProvince(\'' + province + '\')\">' + province + '</div>');
        });
    provincesDiv.innerHTML = provincesInnerHtml.join(' ');
}

function selectProvince(province) {
    var index = selectedProvinceList.indexOf(province);
    if(index === -1) {
        selectedProvinceList.push(province);
    } else {
        selectedProvinceList.splice(index, 1);
    }
    resetGraph();
    updateGraph(coordinates);
}

// Update graph every n seconds
setInterval(function() {
    updateData()
}, 1000);

// SVG canvas options
var options = {
    type: 'delayed',
    duration: 150,
    animTimingFunction: Vivus.EASE,
    pathTimingFunction: Vivus.EASE,
    reverseStack: true,
    start: 'autostart'
};
var vivus = new Vivus('map-svg', options);
var overlay = SVG('map-overlay');

function resetGraph() {
    overlay.clear();
}

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
    data = data.sort(function(a, b) { return a.id - b.id });
    var priorityFilteredData = data.filter(function(a) {
        return parseInt(a.prio) >= parseInt(prioritySliderValues[0]) && parseInt(a.prio) <= parseInt(prioritySliderValues[1]) });
    updateProvincesTags(priorityFilteredData);
    var priorityAndProvincesFilteredData = (selectedProvinceList.length !== 0 ? priorityFilteredData.filter(function(a) {
        return selectedProvinceList.indexOf(a.province) !== -1;
    }) : priorityFilteredData);
    priorityAndProvincesFilteredData.map(location => {
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
    updateList(priorityAndProvincesFilteredData);
}

function updateList(data) {
    var list = document.getElementById('latest-reports');
    var innerList = '';
    var element = '';
    data.map(location => {
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