<?php

// debug
error_reporting(E_ALL);
ini_set('display_errors', true);

chdir(__DIR__);

require '../vendor/autoload.php';

use AfriCC\EPP\Frame\Command\Delete\Domain as DeleteDomain;

$frame = new DeleteDomain;
$frame->setDomain('google.com');
echo $frame;
