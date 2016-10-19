<?php
$uri = preg_replace('@\?.*@', '', $_SERVER['REQUEST_URI']);
if (!file_exists(__DIR__ . '/' . $uri)) {
    $_GET['_url'] = $uri;
    require "index.php";
}
else
{
	return false;
}