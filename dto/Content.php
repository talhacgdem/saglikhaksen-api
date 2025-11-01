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
}
