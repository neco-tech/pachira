<?php // -*- mode: php; -*-

require_once "../pachira/config.php";
require_once SYSTEM_DIR . "loader.php";

Pachira\Pachira::start(Request::server("PATH_INFO", "/"));
