<?php

namespace dto;

use JsonSerializable;

class Content implements JsonSerializable
{
    public string $id;
    public string $title;
    public string $content;
    public ?string $image;
    public ?string $created_at;
    public string $author;

    public function __construct(string $title, string $content, ?string $image, ?string $created_at, string $author)
    {

        $this->title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $this->content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $this->image = $image;
        $this->created_at = $created_at;
        $this->author = $author;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'author' => $this->author,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function postToContent(array $post): Content
    {
        return new Content(
            $post['title']['rendered'] ?? '',
            strip_tags($post['excerpt']['rendered'] ?? $post['content']['rendered'] ?? ''),
            $post['jetpack_featured_media_url'] ?? ($post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null),
            $post['date'] ?? null,
            $post['_embedded']['author'][0]['name'] ?? 'Anonim'
        );
    }

    public static function subeToContent(array $sube): Content
    {
        return new Content(
            $sube['sube_adi'] . ' - ' . $sube['il'],
            'Adres: ' . $sube['adres'] . '<br>' .
            'Telefon: ' . $sube['telefon'] . '<br>' .
            'Şube başkanı: ' . $sube['baskan'],
            null,
            null,
            ''
        );
    }

    public static function kurulusToContent(array $kurulus): Content
    {
        return new Content(
            $kurulus['ad'] . ' - ' . $kurulus['sehir'],
            $kurulus['anlasma_detaylari'],
            null,
            null,
            ''
        );
    }
}
