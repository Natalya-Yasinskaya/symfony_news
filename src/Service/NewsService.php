<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class NewsService{
    private HttpClientInterface $client;

    public function __construct() {
        $this->news = null;
        $this->client = HttpClient::create();
    }
    
    public function get_news($provider)
    {
        // $news_exists_in_db = this.database.getnews($provider);

        // if ($news_exists_in_db) {
            // return $news_exists_in_db;
        // } else {
            // return $this->parse_news($provider);
        // }
    }

    // TODO: get news_provider and invoke corresponing method
    private function parse_news($provider) {
        if ($provider === 'abc') {
            return $this->parse_abc_news();
        } else if ($provider === 'rbk') {
            return $this->parse_rbc_news();
        }
    }

    private function parse_abc_news() {
        $url = 'https://abcnews.go.com/';
                
        $response = $this->client->request('GET', $url);
        $statusCode = $response->getStatusCode();

        $content = $this->request_page($url);
        $crawler = new Crawler($content);
        $links = $crawler->filter('a.News__Item ');
        $news_short_data = $links->each(fn($node) => ['href' => $node->attr('href'), 'title' => $node->text()]);
        $sliced_news = array_slice($news_short_data, 0, 15);

        $result_news_data = array_map(function($news_item) {
            $one_news_page_content = $this->request_page($news_item['href']);
            $one_news_page_crawler = new Crawler($one_news_page_content);
            $texts = $one_news_page_crawler->filter('p.fnmMv');
            // $photos = $one_news_page_crawler->filter('img.sRQoy');
            $arr = $texts->each(fn($node) => $node->text());
            return [
                'title' => $news_item['title'],
                'href' => $news_item['href'],
                // 'photo' => $photos->first()->image(),
                'full_text' => implode(' ', $arr),
                'rating' => rand(1, 10),
            ];
        }, $sliced_news);

        return $result_news_data;
    }

    private function parse_rbc_news() {
        // TODO: implementation of bypassing crawler blocking by rbc.news
    }

    private function request_page($url) {
        $response = $this->client->request('GET', $url);
        $statusCode = $response->getStatusCode();

        if ($statusCode=='200'){
            $content = $response->getContent();

            return $content;
        } else {
            throw new BadRequestException(sprintf('Error while requesting page for URL:'.$url));
        }
    }
}