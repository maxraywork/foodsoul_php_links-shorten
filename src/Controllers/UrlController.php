<?php

namespace App\Controllers;

use App\Models\Auth;
use App\Models\Url;

class UrlController
{
    private $urlModel;

    public function __construct()
    {
        $this->urlModel = new Url();
    }

    public function showShortLinkView(): void
    {
        Auth::checkWithRedirect();

        $longUrl = $_POST['url'] ?? '';
        try {
            $shortLinkObj = $this->urlModel->shorten($longUrl, Auth::getUserId());
            $code = $shortLinkObj['short_url'];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        require_once __DIR__ . '/../Views/ShortLinkView.php';
    }

    public function redirectToLongUrl(array $params): void
    {
        $shortUrl = trim($params['shortUrl']);

        if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $shortUrl)) {
            http_response_code(400);
            if (($_GET['json'] ?? '') === '1') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid short URL']);
            } else {
                echo "Invalid short URL";
            }
            exit;
        }

        $url = $this->urlModel->findByCode($shortUrl);

        if ($url) {
            if (($_GET['json'] ?? '') === '1') {
                header('Content-Type: application/json');
                echo json_encode([
                    'short_url' => $url['short_url'],
                    'long_url'  => $url['long_url']
                ], JSON_UNESCAPED_SLASHES);
            } else {
                header('Location: ' . $url['long_url'], true, 302);
            }
            exit;
        }

        http_response_code(404);
        if (($_GET['json'] ?? '') === '1') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Short URL not found']);
        }
        exit;
    }

    public function deleteUrl(): void
    {
        Auth::checkWithRedirect();
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->urlModel->deleteUrl((int)$id, Auth::getUserId());
        }
        header("Location: /");
        exit;
    }

}