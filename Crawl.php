<?php
class Crawl {

    protected   $domain,
                $url,
                $dom,
                $pages_ext = ["html", "php", "htm", "asp", "aspx"];

    public function __construct($url) {
        $this->url = $url;
        $pathinfo = pathinfo($this->url);
        $this->domain = $pathinfo["dirname"] . "/";
        if ($this->domain == "./") {
            $this->domain = "";
        }
    }

    protected function pageHtml() {
        $this->dom = new DOMDocument;
        $html = file_get_contents($this->url);
        @$this->dom->loadHTML($html);
        return $html;
    }

    protected function findTag($tag) {
        return $this->dom->getElementsByTagName($tag);
    }

    protected function findAttributes($nodes, $attribute, $only_page = false) {
        $attributes = [];
        foreach ($nodes as $node) {
            $attr = $node->getAttribute($attribute);
            if ($only_page) {
                $href_ext = end(explode(".", $attr));
                if (!in_array($href_ext, $this->pages_ext)) {
                    continue;
                }
            }
            if ($attr != "") {
                $attributes[] = $this->domain . $attr;
            }
        }
        return $attributes;
    }

    protected function getAssets() {
        $images = $this->findAttributes($this->findTag("img"), "src");
        $js = $this->findAttributes($this->findTag("script"), "src");
        $stylesheet = $this->findAttributes($this->findTag("link"), "href");
        return array_merge($images, $js, $stylesheet);
    }

    protected function subCrawl($sub_links) {
        $this->crawler = [];
        foreach ($sub_links as $this_page) {
            $inner_crawl = new Crawl($this_page);
            $inner_crawl->pageHtml();
            $inner_assets = $inner_crawl->getAssets();
            $this->crawler[] = [
                "url" => $this_page,
                "assets" => $inner_assets
            ];
        }
        return $this->crawler;
    }

    public function output() {
        $this->pageHtml();
        $links = $this->findAttributes($this->findTag("a"), "href", true);
        $this->subCrawl($links);
        echo json_encode($this->crawler, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

}

?>
