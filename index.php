<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use controllers\ContentController;
use controllers\ContentTypeController;
use controllers\LoginController;
use dto\Response;

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    exit(0);
}

require_once __DIR__ . '/controllers/ContentController.php';
require_once __DIR__ . '/controllers/ContentTypeController.php';
require_once __DIR__ . '/controllers/LoginController.php';
require_once __DIR__ . '/dto/Content.php';
require_once __DIR__ . '/dto/ContentType.php';
require_once __DIR__ . '/dto/Types.php';
require_once __DIR__ . '/dto/Response.php';
require_once __DIR__ . '/dto/Pageable.php';
require_once __DIR__ . '/config.php';

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $uri);

// endpoint sadece path parçası olmalı
$endpoint = $segments[1] ?? $segments[0] ?? '';

try {
    //sleep(3);
    $data = match ($endpoint) {
        'content-types' => (new ContentTypeController())->getContentTypeResponse(),
        'contents' => (new ContentController())->getContents(),
        'login' => (new LoginController())->login(),
        default => throw new Exception("Endpoint not found", 404),
    };

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $code = $e->getCode() ?: 400;
    http_response_code($code);
    echo json_encode(Response::error($e->getMessage(), $code), JSON_UNESCAPED_UNICODE);
}
