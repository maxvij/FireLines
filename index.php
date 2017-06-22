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
<div id="tooltip">
	<p class="title">Test</p>
</div>
<div class="container-fluid">
    <?php include('partials/header.php'); ?>
    <div class="row">
        <div class="col-md-12 col-lg-4">
            <div id="sidebar">
                <div class="row">
                    <div class="col-12">
						<h2>Latest reports</h2>
						<h4>Displaying <span id="number-of-reports"></span> reports</h4>
						<div class="latest-reports-wrapper">
							<ul class="list" id="latest-reports">
							</ul>
						</div>
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
<script type="text/javascript" src="src/js/firelines.js"></script>
</body>
</html>