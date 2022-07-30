<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\News;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\NewsRepository;

class NewsService{
    private HttpClientInterface $client;

    public function __construct(ManagerRegistry $doctrine, NewsRepository $repository) {
        $this->client = HttpClient::create();
        $this->entityManager = $doctrine->getManager();
        $this->repository = $repository;
    }

    public function save_news($news)
    {
        foreach ($news as $news_item) {
            // $is_news_exists = $this->repository->findBy(['title', $news_item['title']]);
            // if ($is_news_exists) {
            //     continue;
            // }
            $newsEntity = new News();
            $newsEntity->save($news_item);
            $this->entityManager->persist($newsEntity);
        }
        $this->entityManager->flush();
    }
    
    public function get_news($provider)
    {
        // здесь подумать
        // нам нужно получить новости, они могут быть или не быть в базе данных
        // в базе данных есть entity news, в котором есть колонка news_provider
        // в зависимости от того, с какого сайта мы спрасили новости, мы заполняем эту колонку
        // соответсвующим провайдером

        // в чем нужно разобраться:
        // как получить данные из таблицы news по колонке  news_provider === $provider
        // (в нашем случае news_provider === 'abc')
        // найти как пищется findBy

        $news_exists_in_db = $this->repository.findBy(['news_provider' => 'abc']);

        if ($news_exists_in_db) {
            return $news_exists_in_db;
        } else {
            $news = $this->parse_news($provider);
            $this->save_news($news);
            return $news;
        }
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
                // 'img_href' => $photos->first()->image(), // $node->attr('href')
                'full_text' => implode(' ', $arr),
                'rating' => rand(1, 10),
                'news_provider' => 'abc',
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