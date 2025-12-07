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
    public bool $hasImage;
    /** @var ContentType[] */
    public array $subCategories;

    public function __construct(int $id, string $name, string $slug, string $icon, Types $type, bool $hasImage, array $subCategories = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->icon = $icon;
        $this->type = $type;
        $this->hasImage = $hasImage;
        $this->subCategories = $subCategories;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "icon" => $this->icon,
            "type" => $this->type->name,
            "has_image" => $this->hasImage,
            "subCategories" => array_map(fn(ContentType $subCategory) => $subCategory->toArray(), $this->subCategories)
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
