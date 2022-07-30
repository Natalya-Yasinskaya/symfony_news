<?php
// src/Controller/NewsController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Service\NewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsController extends AbstractController
{
    private NewsService $newsService;

    public function __construct(NewsService $newsService) {
        $this->newsService = $newsService;
    }

    public function get_abc_news(): Response {
        $provider = 'abc';
        $news = $this->newsService->get_news($provider);

        return new Response( // TODO: Return JSON
            '<html><body>'.'</body></html>'
        );
    }

    public function get_rbk_news(): Response {
        $provider = 'rbk';
        $news = $this->newsService->get_news($provider);
        return new Response(
            '<html><body>'.'</body></html>'
        );
    }
}
