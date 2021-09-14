<?php
namespace App;
use PHPUnit\TextUI\TestRunner;

require_once dirname(__FILE__).'/vendor/autoload.php';
define("_JWT_TOKEN_", 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAbGVsZXJlc3RhcGkucnUifQ.UaM9c3jZAOsclP2dQz0uCp22ovAj7Rn4MXG6RO9p4mY');

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
