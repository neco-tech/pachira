<?php // -*- mode: php; -*-

date_default_timezone_set("Asia/Tokyo");

/* load application */
require_all(APPLICATION_DIR . "plugins/", false);
require_all(APPLICATION_DIR . "models/", true);
require_once APPLICATION_DIR . "functions.php";

session_start();
setup_db(DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD);

add_router("app");
// add_service("article");
