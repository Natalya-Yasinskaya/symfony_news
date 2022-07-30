<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsRepository::class)]

// - ид
// - заголовок
// - текст
// - рейтинг
// - ссылка на полную новость
// - картинка
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column()]
    private ?string $title = null;

    #[ORM\Column()]
    private ?string $full_text = null;

    #[ORM\Column()]
    private ?int $rating = null;

    #[ORM\Column()]
    private ?string $href = null;

    #[ORM\Column()]
    private ?string $img_href = null;

    #[ORM\Column()]
    private ?string $news_provider = null;

    public function save($news_item)
    {
        $this->title = $news_item['title'];
        $this->href = $news_item['href'];
        // $this->img_href = $news_item['img_href'];
        $this->full_text = $news_item['full_text'];
        $this->rating = $news_item['rating'];
        $this->news_provider = $news_item['news_provider'];

        return $this->title;
    }
}
