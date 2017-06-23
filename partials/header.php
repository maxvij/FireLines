<?php
	function checkActivePage($page) {
		$activePage = $_SERVER['PHP_SELF'];
		return strpos($activePage, $page);
	}
	$activeClass = 'class="active"';
?>

<div class="header-wrapper">
    <div class="row">
        <div class="col-6 col-md-5">
            <nav>
                <ul>
                    <li <?php if(!checkActivePage('about/index.php') && !checkActivePage('contact/index.php')) { print $activeClass; } ?>>
                        <a href="/">Map</a>
                    </li>
                    <li <?php if(checkActivePage('about/index.php')) { print $activeClass; } ?>>
                        <a href="/about">About</a>
                    </li>
                    <li <?php if(checkActivePage('contact/index.php')) { print $activeClass; } ?>>
                        <a href="/contact">Contact</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="col-6 col-md-2">
            <div class="logo">
                <h1>FireLines</h1>
            </div>
        </div>
    </div>
</div>