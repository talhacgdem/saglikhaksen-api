<?php

namespace controllers;

use dto\ContentType;
use dto\Response;
use main\Config;

class ContentTypeController
{
    /**
     * @return ContentType[]
     */
    public function getContentTypes(): array
    {
        return Config::getConfig()['contentTypes'] ?? [];
    }

    /**
     * @return Response
     */
    public function getContentTypeResponse(): Response
    {
        return Response::success($this->getContentTypes());
    }
}
