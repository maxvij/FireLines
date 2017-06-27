<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>FireLines | Real-time fire reports in the Netherlands</title>
    <?php include('partials/metadata.php'); ?>
	<link rel="stylesheet" href="src/css/vendor.css" media="all"/>
	<link rel="stylesheet" href="src/css/style.css" media="all"/>
	<script type="text/javascript" src="src/js/vivus.min.js"></script>
	<script type="text/javascript" src="src/js/svg.min.js"></script>
	<script type="text/javascript" src="src/js/nouislider.min.js"></script>
</head>
<body>
<div id="tooltip"></div>
<div class="container-fluid">
    <?php include('partials/header.php'); ?>
	<div class="row">
		<div class="col-md-12 col-lg-6 col-xl-4">
			<div id="sidebar">
				<div class="row">
					<div class="col-12">
						<div class="row">
							<div class="col-12">
								<h2>Filter options</h2>
								<div class="row">
									<div class="col-12">
										<label>Report date</label>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<div id="date-input" class="radio-pill">
											<div class="pill">
												<input type="radio" value="1" name="date" id="date-1" />
												<label for="date-1">All time</label>
											</div>

											<div class="pill">
												<input type="radio" value="2" name="date" id="date-2" />
												<label for="date-2">Yesterday</label>
											</div>

											<div class="pill">
												<input type="radio" value="3" name="date" id="date-3" checked />
												<label for="date-3">Today</label>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-8">
										<label>Report priority</label>
									</div>
									<div class="col-4">
										<label class="value" id="priority-slider-value">1-3</label>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<div id="priority-slider"></div>
									</div>
								</div>
							</div>
							<div class="col-12">
								<div class="row">
									<div class="col-4">
										<label>Provinces</label>
									</div>
									<div class="col-8">
										<label class="value ion-chevron-down" id="provinces-toggle">Show provinces</label>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<div id="provinces"></div>
									</div>
								</div>
							</div>
							<div class="col-12">
								<div class="row">
									<div class="col-8">
										<label>Amount of reports</label>
									</div>
									<div class="col-4">
										<label class="value" id="amount-slider-value"></label>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<div id="amount-slider"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12">
						<h2 id="latest-reports-title">Latest reports of today</h2>
						<h4>Displaying <span id="number-of-reports"></span> reports</h4>
						<div class="latest-reports-wrapper">
							<ul class="list" id="latest-reports">
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 col-lg-6 col-xl-8">
			<div id="map">
				<div id="map-overlay">
					<object id="map-svg" type="image/svg+xml" data="src/img/nl.svg"></object>
				</div>
				<div id="data"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="src/js/nav.js"></script>
<script type="text/javascript" src="src/js/date.js"></script>
<script type="text/javascript" src="src/js/firelines.js"></script>
</body>
</html>