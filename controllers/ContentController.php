<?php

namespace controllers;

use dto\Content;
use dto\ContentType;
use dto\Types;

class ContentController
{
    private string $baseUrl = 'https://www.saglikhaksen.org.tr/wp-json/wp/v2';
    private string $cacheDir;
    private string $username;
    private string $password;

    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        $this->username = $config['auth']['username'];
        $this->password = $config['auth']['password'];

        $this->cacheDir = __DIR__ . '/../cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * @return Content[]
     */
    public function getContents(): array
    {
        $slug = $_GET['contentType'] ?? null;
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);

        if (empty($slug)) {
            http_response_code(400);
            echo json_encode(['error' => 'contentType parametresi zorunludur']);
            return;
        }

        $contentType = $this->findContentType($slug);
        if (!$contentType) {
            http_response_code(404);
            echo json_encode(['error' => 'Content type bulunamadı']);
            return;
        }

        /** @var Content[] $contents */
        $contents = match ($contentType->type) {
            Types::category => $this->fetchPostsByCategory($contentType->id, $page, $perPage),
            Types::page     => $this->fetchPageById($contentType->id),
        };

        echo json_encode([
            'page' => $page,
            'per_page' => $perPage,
            'total_items' => count($contents),
            'data' => $contents, // doğrudan Content nesneleri serialize ediliyor
        ], JSON_UNESCAPED_UNICODE);
    }

    /** @return ?ContentType */
    private function findContentType(string $slug): ?ContentType
    {
        $controller = new ContentTypeController();
        $types = $controller->getContentTypes();

        foreach ($types as $type) {
            if ($type->slug === $slug) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @return Content[]
     */
    private function fetchPostsByCategory(int $catId, int $page, int $perPage): array
    {
        $query = http_build_query([
            'per_page' => $perPage,
            'page' => $page,
            'categories' => $catId,
            '_embed' => true,
        ]);

        $cacheFile = "$this->cacheDir/cat_{$catId}_page$page.json";

        $posts = $this->getCached($cacheFile, function () use ($query) {
            $url = "$this->baseUrl/posts?$query";
            return $this->fetchWithHeaders($url)[0];
        });

        return array_map(function ($post): Content {
            return new Content(
                $post['title']['rendered'] ?? '',
                strip_tags($post['excerpt']['rendered'] ?? ''),
                $post['jetpack_featured_media_url']
                ?? ($post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null),
                $post['date'] ?? null,
                $post['_embedded']['author'][0]['name'] ?? 'Anonim'
            );
        }, $posts ?? []);
    }

    /**
     * @return Content[]
     */
    private function fetchPageById(int $pageId): array
    {
        $cacheFile = "$this->cacheDir/page_$pageId.json";

        return $this->getCached($cacheFile, function () use ($pageId) {
            $url = "$this->baseUrl/pages/$pageId?context=edit";
            $response = $this->fetchWithHeaders($url, true)[0]; // authenticated
            return [
                new Content(
                    $response['title']['raw'] ?? '',
                    strip_tags($response['content']['raw'] ?? ''),
                    null,
                    $response['date'] ?? null,
                    $response['author'] ?? 'Anonim'
                ),
            ];
        });
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function fetchWithHeaders(string $url, bool $auth = false): array
    {
        $headers = [];
        $opts = ['http' => ['method' => 'GET', 'ignore_errors' => true]];

        if ($auth) {
            $authHeader = base64_encode("$this->username:$this->password");
            $opts['http']['header'] = "Authorization: Basic $authHeader\r\nAccept: application/json";
        } else {
            $opts['http']['header'] = "Accept: application/json";
        }

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        if (isset($http_response_header)) {
            foreach ($http_response_header as $headerLine) {
                if (preg_match('/^(X-WP-[A-Za-z-]+):\s*(.+)$/i', $headerLine, $matches)) {
                    $headers[$matches[1]] = trim($matches[2]);
                }
            }
        }

        return [json_decode($response, true) ?? [], $headers];
    }

    /**
     * @template T
     * @param callable():T $fetch
     * @return T
     */
    private function getCached(string $file, callable $fetch)
    {
        $ttl = 600;
        if (file_exists($file) && (time() - filemtime($file)) < $ttl) {
            return json_decode(file_get_contents($file), true);
        }

        $data = $fetch();
        file_put_contents($file, json_encode($data));
        return $data;
    }
}
