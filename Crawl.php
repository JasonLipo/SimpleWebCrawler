<?php
/**
 * Crawl.php
 * A simple web crawler
 * This file contains all the crawl functions
 * @author Jason Lipowicz
 */

class Crawl {

    // Define some protected class variables
    // which will be stored throughout the program
    protected   $domain,
                $url,
                $dom;

    /**
     * __construct ($url)
     *
     * Class constructor function, initalises the crawler
     * @param $url The URL to crawl
     * @return void
     */
    public function __construct($url) {

        $this->url = $url;

        // Get the path information from the URL
        $pathinfo = pathinfo($this->url);

        // Store the "domain" for nice output
        // e.g. http://example.com or https://www.domain.co.uk
        $this->domain = $pathinfo["dirname"] . "/";
        if ($this->domain == "./") {
            $this->domain = "";
        }
        else if ($this->domain == "http:/" || $this->domain == "https:/") {
            $this->domain .= "/" . $pathinfo["basename"] . "/";
        }

    }

    /**
     * pageHtml ()
     *
     * Use the member variable $url to load the source code
     * @return  The contents of web page to crawl
     *          Returns FALSE if the request was denied due to permissions
     */
    protected function pageHtml() {

        $this->dom = new DOMDocument;

        // Don't throw warnings if these fail, this is handled
        // in the output function
        $html = @file_get_contents($this->url);
        @$this->dom->loadHTML($html);

        return $html;

    }

    /**
     * findTag ($tag)
     *
     * A synonym for scanning the DOM and finding all matching elements
     * with the respective tag name
     * @param $tag The string of the tag name e.g. "a", "img"
     * @return A object of type DOMNodeList containing the matching elements
     */
    protected function findTag($tag) {
        return $this->dom->getElementsByTagName($tag);
    }

    /**
     * findAttributes ($nodes, $attribute)
     *
     * Take a list of nodes and pick out the attribute value of each node
     * e.g. <a href="test.html">Hello World</a> will return "test.html" when
     * selecting the 'href' attribute
     * @param $nodes A list of type DOMNodeList from the findTag method
     * @param $attribute A string representing the attribute to use.
     *        Pre: the attribute exists in the node
     * @return An array containing a list of attribute values
     */
    protected function findAttributes($nodes, $attribute) {

        $attributes = [];

        // Loop through the nodes
        foreach ($nodes as $node) {

            $attr = $node->getAttribute($attribute);
            if ($attr != "") {
                // If the attribute value already contains a domain,
                // don't append the current one
                if (preg_match('/^(http(s)?\:\/\/)(.*)$/', $attr) === 1) {
                    $attributes[] = $attr;
                }
                else {
                    // If the attribute value is a relative URL,
                    // then prepend the relative domain to
                    $attributes[] = $this->domain . $attr;
                }
            }
        }

        return $attributes;

    }

    /**
     * getAssets ()
     *
     * Find all the img, script and link tags which will search the page
     * for the CSS, JavaScript and Image files.
     * @return An array containing all the assets from this page
     */
    protected function getAssets() {
        $images = $this->findAttributes($this->findTag("img"), "src");
        $js = $this->findAttributes($this->findTag("script"), "src");
        $stylesheet = $this->findAttributes($this->findTag("link"), "href");
        return array_merge($images, $js, $stylesheet);
    }

    /**
     * subCrawl ($sub_links)
     *
     * When scanning the initial starting URL, crawl all the links found
     * on this page to find their assets
     * @return  An array in the correct format with each crawled page
     *          and its assets
     */
    protected function subCrawl($sub_links) {

        $this->crawler = [];

        foreach ($sub_links as $this_page) {

            // Crawl inside this page
            $inner_crawl = new Crawl($this_page);
            $page_reachable = $inner_crawl->pageHtml();
            if ($page_reachable === false) {
                continue;
            }

            if ($inner_crawl->validHtml()) {
                // Get the assets on this page
                $inner_assets = $inner_crawl->getAssets();
                if (!empty($inner_assets)) {
                    // Format the array
                    $this->crawler[] = [
                        "url" => $this_page,
                        "assets" => $inner_assets
                    ];
                }
            }

        }

        return $this->crawler;

    }

    /**
     * validHtml ()
     *
     * Check if the given URL is a valid HTML page
     * i.e. we are not trying to scan a pdf file for example
     * @return Either true or false
     */
    private function validHtml() {

        // If this path is an absolute URL
        if (strpos($this->url, "http") !== false) {
            $fetch_type = get_headers($this->url, 1);
            $content_type = (is_array($fetch_type["Content-Type"])) ?
                            $fetch_type["Content-Type"][0] :
                            $fetch_type["Content-Type"];
            $valid_html = strpos($content_type, "text/html") !== false;
        }
        // If this path is a relative file
        else {
            $content_type = mime_content_type($this->url);
            $valid_html = ( $content_type == "text/html" ||
                            $content_type == "text/plain");
        }

        return $valid_html;

    }

    /**
     * output ()
     *
     * Scan the page and output the results to STDOUT
     * @return A JSON-encoded string which represents the result
     */
    public function output() {

        $page_reachable = $this->pageHtml();
        if ($page_reachable === false) {
            echo json_encode(["error" => "Request denied"]);
        }

        if ($this->validHtml()) {
            $links = $this->findAttributes($this->findTag("a"), "href");
            $this->subCrawl($links);
            echo json_encode($this->crawler,    JSON_PRETTY_PRINT |
                                                JSON_UNESCAPED_SLASHES);
        }
        else {
            echo json_encode(["error" => "Invalid HTML source"]);
        }

    }

}

?>
