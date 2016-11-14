<?php
/**
 * demo.php
 * A simple web crawler
 * This file contains a sample usage example
 * @author Jason Lipowicz
 */

require_once "Crawl.php";

// Test 1
$sample = new Crawl("sample.html");
$sample->output();

// Test 2
$sample2 = new Crawl("http://www.jasonlipowicz.com/home.php");
$sample2->output();

?>
