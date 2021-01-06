<?php

namespace CapoSourceWordpress\Types;

class Page
{
    protected ?string $siteUrl;

    public $id;
    public $date;
    public $date_gmt;
    public $guid;
    public $modified;
    public $modified_gmt;
    public $slug;
    public $status;
    public $type;
    public $link;
    public $title;
    public $content;
    public $excerpt;
    public $author;
    public $featured_media;
    public $parent;
    public $menu_order;
    public $comment_status;
    public $ping_status;
    public $template;
    public $meta;
    public $acf;
    public $yoast_head;
    public $blocks;
    public $yoast_title;
    public $yoast_meta;
    public $yoast_json_ld;
    public $_links;
    
    public function __construct($apiResponse, ?string $siteUrl = null)
    {
        $apiVars = get_object_vars($apiResponse);

        foreach ($apiVars as $key => $value) {
            $this->{$key} = $value;
        }

        $this->siteUrl = $siteUrl;
    }

    /**
     * Get the page title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title->rendered;
    }
    
    /**
     * Get the url for the page without the host
     *
     * @return string
     */
    public function getRouteUrl(): string
    {
        $remoteUrl = $this->link;

        $routeUrl = str_replace($this->siteUrl, '', $remoteUrl);

        return trim(ltrim($routeUrl, '/'), '/');
    }

    /**
     * Get blocks
     *
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return array_map(
            fn($block) => new Block($block),
            $this->blocks
        );
    }
}
