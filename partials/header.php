<?php
	function checkActivePage($page) {
		$activePage = $_SERVER['PHP_SELF'];
		return strpos($activePage, $page);
	}
	$activeClass = 'class="active"';
?>

<div class="header-wrapper">
    <div class="row">
        <div class="col-12 col-sm-5">
            <nav>
				<div class="menu-trigger"><span></span></div>
                <ul id="menu-list">
                    <li <?php if(!checkActivePage('about/index.php') && !checkActivePage('safety/index.php')) { print $activeClass; } ?>>
                        <a href="/">Map</a>
                    </li>
                    <li <?php if(checkActivePage('about/index.php')) { print $activeClass; } ?>>
                        <a href="/about">About</a>
                    </li>
                    <li <?php if(checkActivePage('safety/index.php')) { print $activeClass; } ?>>
                        <a href="/safety">Safety</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="col-12 col-sm-2 col-click-through">
            <div class="logo col-click-through">
                <h1>FireLines</h1>
            </div>
        </div>
    </div>
</div>