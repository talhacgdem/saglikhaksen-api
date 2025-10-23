<?php

namespace dto;

use JsonSerializable;

class Content implements JsonSerializable
{
    public string $title;
    public string $content;
    public ?string $image;
    public ?string $created_at;
    public string $author;

    public function __construct(string $title, string $content, string $image, string $created_at, string $author)
    {
        $this->title = $title;
        $this->content = $content;
        $this->image = $image;
        $this->created_at = $created_at;
        $this->author = $author;
    }

    public static function fromData(array $data): Content
    {
        return new Content(
            $data['title'] ?? '',
            $data['content'] ?? '',
            $data['image'] ?? null,
            $data['created_at'] ?? null,
            $data['author'] ?? 'Anonim'
        );
    }

    public function toArray(): array
    {
        return [
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
