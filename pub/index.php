<?php
//Set the constant for the base dir for the app
define('BASEPATH', dirname(__DIR__));
$bootstrap = BASEPATH.'/config/bootstrap.php';

if (file_exists($bootstrap)) {
    require $bootstrap;
} else {
    throw new \Exception(
        'Make sure that bootstrap.php is present.'
    );
}
?>
