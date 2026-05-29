<?php
$debug = false;
if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require_once DOCROOT . '/app/src/Models/DataInterface.php';
require_once DOCROOT . '/app/src/Models/DataConnector.sqlite.php';
require_once DOCROOT . '/app/src/Controllers/IndexController.php';