<?php

namespace dto;

use JsonSerializable;

class ContentType implements JsonSerializable
{
    public int $id;
    public string $name;
    public string $slug;
    public string $icon;
    public Types $type;

    public function __construct($id, $name, $slug, $icon, $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->icon = $icon;
        $this->type = $type;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "icon" => $this->icon,
            "type" => $this->type->name
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}