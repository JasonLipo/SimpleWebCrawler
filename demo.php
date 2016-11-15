<?php
/**
 * demo.php
 * A simple web crawler
 * This file contains a sample usage example
 * @author Jason Lipowicz
 */

// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

require_once "Crawl.php";

// Test 1
$sample = new Crawl("sample.html");
//$sample->output();

// Test 2
$sample2 = new Crawl("http://www.jasonlipowicz.com/home.php");
$sample2->output();

// Test 3
$sample3 = new Crawl("http://www.google.co.uk");
//$sample3->output();

// Test 4
$sample4 = new Crawl("https://gocardless.com/");
//$sample4->output();

// Test 5
$sample5 = new Crawl("http://www.facebook.com");
//$sample5->output();

?>
