<?php
namespace App;
use PHPUnit\TextUI\TestRunner;

require_once dirname(__FILE__).'/vendor/autoload.php';

function getcurrenturlts(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . '/';
}

define("_SHOP_URL_", getcurrenturlts());

$phpunit = new TestRunner;

try {
    $test_results = $phpunit->dorun($phpunit->getTest(__DIR__. '/tests/', '', ''));
} catch (\Exception $e) {
    print $e->getMessage() . "\n";
    die ("Unit tests failed.");
}
