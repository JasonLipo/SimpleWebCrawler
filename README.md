# SimpleWebCrawler

SimpleWebCrawler is very basic web crawling system.
Given an initial start URL, it will scan the page and find all the links on that page. For each page, the crawler will then find all the assets (CSS, JavaScript and Images) that are on the page. The results will be displayed in JSON-encoded format.

### Installing

This program is written in PHP and requires version 5.5 or higher.

**Windows**:
Download PHP for Windows [here](http://windows.php.net/download/)

Alternatively, you can download a full WAMP stack [here](https://bitnami.com/stack/wamp)

For other operating systems, you can find the PHP download page at:
http://php.net/downloads.php

Once the source file has been downloaded, it can be extracted into a single folder.

### Running

The crawler comes with a `demo.php` file which comes with 2 tests. The file can either by run from the command-line (terminal):
```
> cd /path/to/php/exe
> php demo.php
```

Or if you are using a WAMP/LAMP application, it can be run by opening a web browser and navigating to:
```
http://localhost/demo.php
```

As an example, the result of running `demo.php` from the terminal, produces this result.

```javascript
> php demo.php

[
    {
        "url": "sample2.html",
        "assets": [
            "test2.css"
        ]
    }
]
```

### Usage

To modify the demo file or to test the crawler with other URLs, import the `Crawl.php` file into a PHP file and run the following code:

```php
require_once "/path/to/Crawl.php";

$crawler = new Crawl("example.html");
$crawler->output();
```

### Author

Created by Jason Lipowicz

Last modified: 14th November 2016
