<?php

namespace CapoSourceWordpress;

use GuzzleHttp\Client;
use CapoSourceWordpress\Types\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class Api
{
    public string $siteUrl;

    public string $apiUrl;

    public Client $client;

    private string $cacheDir;

    public function __construct(string $siteUrl)
    {
        $this->cacheDir = site_cache_path() . '/capo-source-wordpress';

        $this->siteUrl = $siteUrl;

        $this->apiUrl = $siteUrl . '/wp-json/';

        $this->client = new Client([
            'base_uri' => $this->siteUrl . '/wp-json/',
        ]);
    }

    public function source()
    {
        $this->getPosts();
        $this->getPages();
    }

    public function getPosts()
    {
        $cached = $this->getCached('posts.json');

        $entries = $cached ?? $this->getAll('wp/v2/posts');

        $posts = array_map(function ($page) {
                return new Page($page, $this->siteUrl);
        }, $entries);

        if (!$cached) {
            File::put($this->cacheDir . '/posts.json', json_encode($posts));
        }

        return $posts;
    }

    /**
     * Return pages
     *
     * @return Page[]
     */
    public function getPages(): array
    {
        $cached = $this->getCached('pages.json');

        $entries = $cached ?? $this->getAll('wp/v2/pages');

        $posts = array_map(function ($page) {
                return new Page($page, $this->siteUrl);
        }, $entries);

        if (!$cached) {
            File::put($this->cacheDir . '/pages.json', json_encode($posts));
        }

        return $posts;
    }

    private function getTotalsFromHeaders(array $headers): array
    {
        $records = (int) $headers['X-WP-Total'][0];

        $pages = (int) $headers['X-WP-TotalPages'][0];

        return [
            'records' => $records,
            'pages'   => $pages,
        ];
    }

    private function getAll(string $endpoint)
    {
        $url = $this->apiUrl . $endpoint;

        $query = [
            'per_page' => 100,
            'page'     => 1
        ];

        $res = Http::get($url, [
            'query' => $query
        ]);

        $totals = $this->getTotalsFromHeaders($res->getHeaders());

        $json = (string) $res->getBody();

        $data = json_decode($json);

        $posts = [];

        foreach ($data as $page) {
            $posts[] = $page;
        }

        while ($query['page'] < $totals['pages']) {
            ++$query['page'];

            $res = Http::get($url, [
                'query' => $query
            ]);

            $data = json_decode((string) $res->getBody());

            foreach ($data as $page) {
                $posts[] = $page;
            }
        }

        return $posts;
    }

    public function getAcfOptions(string $option)
    {
        $filename = 'acf.' . $option . '.json';
    
        $cached = $this->getCached($filename);
    
        if ($cached) {
            return $cached;
        }
    
        $res = $this->client->get('acf/v3/options/' . $option);
    
        $body = (string) $res->getBody();
    
        File::put($this->cacheDir . '/' . $filename, $body);
    
        return json_decode($body);
    }
    
    private function getCached(string $filename)
    {
        $cacheFilePath = $this->cacheDir . '/' . $filename;

        if (!File::exists($cacheFilePath)) {
            return null;
        }

        $json = File::get($cacheFilePath);

        return json_decode($json);
    }
}
