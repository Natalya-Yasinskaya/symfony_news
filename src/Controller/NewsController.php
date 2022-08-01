<?php
// src/Controller/NewsController.php
namespace App\Controller;

use App\Service\NewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class NewsController extends AbstractController
{
    private NewsService $newsService;

    public function __construct(NewsService $newsService) {
        $this->newsService = $newsService;
    }

    public function get_abc_news(): JsonResponse {
        $provider = 'abc';
        $news = $this->newsService->get_news($provider);
        
        // TODO: slice description to 200 symbols

        return new JsonResponse([
            'success' => true,
            'data'    => $news,
        ]);
    }

    public function get_rbk_news(): JsonResponse {
        $provider = 'rbk';
        $news = $this->newsService->get_news($provider);
        return new JsonResponse([
            'success' => true,
            'data'    => $news,
        ]);
    }

    public function update_news_rating($id): JsonResponse {
        $update = $this->newsService->update_news_rating($id);

        return new JsonResponse([
            'success' => true,
            'data'    => $update,
        ]);
    }
    
    public function update_news($provider) {
        $update = $this->newsService->update_news($provider);

        return new JsonResponse([
            'success' => true,
            'data'    => $update,
        ]);
    }
}
