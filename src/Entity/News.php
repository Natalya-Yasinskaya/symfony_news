<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: NewsRepository::class)]

class News implements JsonSerializable
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
    private ?string $img_src = null;

    #[ORM\Column()]
    private ?string $news_provider = null;

    public function get_news() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'href' => $this->href,
            'img_src' => $this->img_src,
            'full_text' => mb_substr($this->full_text,0,200),
            'rating' => $this->rating,
            'news_provider' => $this->news_provider,
        ];
    }

    public function save($news_item)
    {
        $this->id = $news_item['id'];
        $this->title = $news_item['title'];
        $this->href = $news_item['href'];
        $this->img_src = $news_item['img_src'];
        $this->full_text = $news_item['full_text'];
        $this->rating = $news_item['rating'];
        $this->news_provider = $news_item['news_provider'];
    }

    public function set_rating($rating)
    {
        $this->rating = $rating;
    }

    public function jsonSerialize() {
        return $this->get_news();
    }
}
