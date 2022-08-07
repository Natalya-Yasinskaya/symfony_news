<?php
// src/Controller/NewsController.php
namespace App\Controller;

use App\Service\NewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
$request = Request::createFromGlobals();

class NewsController extends AbstractController
{
    private NewsService $newsService;

    public function __construct(NewsService $newsService) {
        $this->newsService = $newsService;
    }

    public function get_abc_news(Request $request): JsonResponse {
        $page = $request->query->get('page');
        $limit = $request->query->get('limit');
        $provider = 'abc';
        $news = $this->newsService->get_news($provider, $page, $limit);

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

    public function update_news(Request $request) {
        $id = $request->get('id');
        $rating = $request->get('rating');
        $this->newsService->update_news_rating($id, $rating);
        return new JsonResponse([
            'success' => true,
            'data'    => [
                'id' => $id,
                'rating' => $rating
            ]
        ]);
    }

    public function delete_news(Request $request) {
        $id = $request->get('id');

        $result = $this->newsService->delete_news($id);
        return new JsonResponse([
            'success' => true,
            'data'    => $result,
        ]);
    }
}
