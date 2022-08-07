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
            $newsEntity = new News();
            $newsEntity->save($news_item);
            $this->entityManager->persist($newsEntity);
        }
        $this->entityManager->flush();
    }

    public function get_news($provider, $page, $limit)
    {
        $news_exists_in_db = $this->repository->findBy(['news_provider' => 'abc']); // 15

        if ($news_exists_in_db) {
            $length = count($news_exists_in_db);
            $offset = ($page - 1) * $limit;
            $news_for_response = array_slice($news_exists_in_db, $offset, $limit);
            return $news_for_response;
        } else {
            $news = $this->parse_news($provider);
            $length = count($news);
            $offset = ($page - 1) * $limit;
            $news_for_response = array_slice($news, $offset, $limit);
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
            $full_texts_arr = $texts->each(fn($node) => $node->text());
            $photos_array_src = $one_news_page_crawler->filter('img.sRQoy')->each(fn($img) => $img->attr('src'));
            $main_photo_src = '';
            if (count($photos_array_src) > 0) {
                $main_photo_src = $photos_array_src[0];
            }
            return [
                'id'=> $news_item['id'],
                'title' => $news_item['title'],
                'href' => $news_item['href'],
                'img_src' => $main_photo_src,
                'full_text' => implode(' ', $full_texts_arr),
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

    public function update_news_rating($id, $rating) {
        $one_news = $this->entityManager->getRepository(News::class)->find($id);

        if (!$one_news) {
            throw $this->createNotFoundException(
                'No news found for id '.$id
            );
        }

        $one_news->set_rating($rating);
        $this->entityManager->persist($one_news);
        $this->entityManager->flush();
        return $one_news;
    }

    public function delete_news($id) {
        $one_news = $this->entityManager->getRepository(News::class)->find($id);

        if (!$one_news) {
            throw $this->createNotFoundException(
                'No news found for id '.$id
            );
        };

        $this->entityManager->remove($one_news);
        $this->entityManager->flush();

        return "ok";
     }
}