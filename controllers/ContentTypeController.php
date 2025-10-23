<?php

namespace controllers;
use dto\ContentType;

class ContentTypeController
{
    /**
     * @return ContentType[]
     */
    public function getContentTypes(): array
    {
        $config = require __DIR__ . '/../config.php';
        return $config['contentTypes'] ?? [];
    }

    public function getContentTypesResponse(): void
    {
        $contentTypes = $this->getContentTypes();
        echo json_encode(['data' => $contentTypes], JSON_UNESCAPED_UNICODE);
    }
}
