<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use controllers\ContentController;
use controllers\ContentTypeController;
use dto\Response;

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/controllers/ContentController.php';
require_once __DIR__ . '/controllers/ContentTypeController.php';
require_once __DIR__ . '/dto/Content.php';

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $uri);
$endpoint = $segments[1] ?? '';


try{
    $data = match ($endpoint) {
        'content-types' => (new ContentTypeController())->getContentTypes(),
        'contents' => (new ContentController())->getContents(),
        default => throw new Exception("Endpoint not found"),
    };
    echo json_encode(Response::success($data), JSON_UNESCAPED_UNICODE);
}catch (Exception $e){
    http_response_code(404);
    echo json_encode(Response::error($e->getMessage()), JSON_UNESCAPED_UNICODE);
}
