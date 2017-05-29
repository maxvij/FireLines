<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FireLines | Concept</title>
    <link rel="stylesheet" href="src/css/vendor.css" media="all"/>
    <link rel="stylesheet" href="src/css/style.css" media="all"/>
    <script type="text/javascript" src="src/js/vivus.min.js"></script>
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
                <div class="row">
                    <div class="col-12 col-md-8 offset-md-2">
                        <object id="map-svg" type="image/svg+xml" data="src/img/nl.svg"></object>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var options = {
        type: 'oneByOne',
        duration: 300,
        animTimingFunction: Vivus.EASE,
        pathTimingFunction: Vivus.EASE,
        reverseStack: true,
        start: 'autostart'
    };
    var vivus = new Vivus('map-svg', options);
</script>
</body>
</html>