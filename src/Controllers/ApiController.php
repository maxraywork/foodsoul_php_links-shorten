<?php

namespace App\Controllers;

use App\Models\ApiAuth;
use App\Models\Url;

class ApiController
{
    private $apiAuth;
    private $urlModel;

    public function __construct()
    {
        $this->apiAuth = new ApiAuth();
        $this->urlModel = new Url();
        header('Content-Type: application/json');
    }

    private function authenticate(): int
    {
        $header = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $userId = $this->apiAuth->checkToken($matches[1]);
            if ($userId) return $userId;
        }
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized', 'error2' => $header]);
        exit;
    }

    public function listAllUrls(): string
    {
        $userId = $this->authenticate();
        $urls = $this->urlModel->getUrlsByUser($userId);
        $json = json_encode($urls);
        echo $json;
        exit;
    }

    public function createUrl(): void
    {
        $userId = $this->authenticate();
        $longUrl = $_POST['long_url'] ?? '';
        try {
            $url = $this->urlModel->shorten($longUrl, $userId);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
        unset($url['user_id']);
        echo json_encode($url);
        exit;
    }

    public function deleteUrl(array $params): void {
        $userId = $this->authenticate();
        $urlId = (int) ($params['id'] ?? 0);

        $deleted = $this->urlModel->deleteUrl($urlId, $userId);

        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'URL not found']);
        }
        exit;
    }

}