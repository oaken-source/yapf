<?php

// this is necessary for core framework functionality.
define("VALID_REQUEST", true);

// setup framework environment
require_once("control/setup.php");

// choose what page you want to request
$page = 'index';

// render the given page
RENDERER::renderPage($page);

?>
