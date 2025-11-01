<?php

namespace controllers;

use dto\Content;
use dto\ContentType;
use dto\Pageable;
use dto\Response;
use dto\Types;
use Exception;
use main\Config;

class ContentController
{
    private string $baseUrl = 'https://www.saglikhaksen.org.tr/wp-json';

    public function __construct()
    {
        $config = Config::getConfig();
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function getContents(): Response
    {
        $slug = $_GET['contentType'] ?? null;
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['perPage'] ?? 10);

        if (empty($slug)) {
            throw new Exception('contentType parametresi zorunludur', 400);
        }

        $contentType = $this->findContentType($slug);
        if (!$contentType) {
            throw new Exception('Content type bulunamadı', 404);
        }

        if ($contentType->type === Types::category) {
            [$posts, $headers] = $this->fetchPostsByCategory($contentType->id, $page, $perPage);
            $contents = array_map(fn($post) => $this->mapPostToContent($post), $posts);
            $totalItems = isset($headers['X-WP-Total']) ? (int) $headers['X-WP-Total'] : count($contents);
            $meta = new Pageable($page, $perPage, $totalItems);
        } elseif ($contentType->type === Types::page) {
            $contents = $this->fetchPageById($contentType->id);
            $meta = new Pageable(1, 1, 1);
        } else {
            $contents = $this->getOtherTypes($slug);
            $meta = new Pageable(1, 1, 1);
        }

        $i = 0;
        foreach ($contents as $content) {
            $content->id = $i;
            $i++;
        }

        return Response::success($contents, $meta);
    }

    private function findContentType(string $slug): ?ContentType
    {
        $controller = new ContentTypeController();
        foreach ($controller->getContentTypes() as $type) {
            if ($type->slug === $slug) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @return array{0: array, 1: array} $posts, $headers
     */
    private function fetchPostsByCategory(int $catId, int $page, int $perPage): array
    {
        $query = http_build_query([
            'per_page' => $perPage,
            'page' => $page,
            'categories' => $catId,
            '_embed' => true,
        ]);
        $url = "$this->baseUrl/wp/v2/posts?$query";
        return $this->fetchWithHeaders($url);
    }

    /**
     * @return Content[]
     */
    private function getOtherTypes(string $slug): array
    {

        if ($slug === 'subelerimiz') {
            $url = "$this->baseUrl/subeler/v1/list";
            [$response, $headers] = $this->fetchWithHeaders($url, true);
            return array_map(
                fn($resp) =>
                new Content(
                    $resp['sube_adi'] . ' - ' . $resp['il'],
                    'Adres: ' . $resp['adres'] . '<br>' .
                    'Telefon: ' . $resp['telefon'] . '<br>' .
                    'Şube başkanı: ' . $resp['baskan'],
                    null,
                    null,
                    ''
                )
                ,
                $response
            );
        } else {
            throw new Exception('Geçersiz contentType', 400);
        }
    }

    /**
     * @return Content[]
     */
    private function fetchPageById(int $pageId): array
    {
        $url = "$this->baseUrl/wp/v2/pages/$pageId?context=view&_embed=1";
        [$response, $headers] = $this->fetchWithHeaders($url, true);
        return [$this->mapPostToContent($response)];
    }

    private function mapPostToContent(array $post): Content
    {
        return new Content(
            $post['title']['rendered'] ?? '',
            strip_tags($post['excerpt']['rendered'] ?? $post['content']['rendered'] ?? ''),
            $post['jetpack_featured_media_url'] ?? ($post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null),
            $post['date'] ?? null,
            $post['_embedded']['author'][0]['name'] ?? 'Anonim'
        );
    }

    /**
     * @return array{0: array, 1: array} $data, $headers
     */
    private function fetchWithHeaders(string $url, bool $auth = false): array
    {
        $headers = [];
        $opts = [
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
                'header' => "Accept: application/json"
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return [[], []];
        }

        if (isset($http_response_header)) {
            foreach ($http_response_header as $headerLine) {
                if (preg_match('/^(X-WP-[A-Za-z-]+):\s*(.+)$/i', $headerLine, $matches)) {
                    $headers[$matches[1]] = trim($matches[2]);
                }
            }
        }

        return [json_decode($response, true) ?? [], $headers];
    }
}
