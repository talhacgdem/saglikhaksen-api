<?php

namespace dto;

use JsonSerializable;

class Pageable implements JsonSerializable
{
    public int $page;
    public int $per_page;
    public int $total_items;

    public function __construct(int $page, int $per_page, int $total_items)
    {
        $this->page = $page;
        $this->per_page = $per_page;
        $this->total_items = $total_items;
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->per_page,
            'total_items' => $this->total_items,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
