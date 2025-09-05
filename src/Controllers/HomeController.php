<?php

namespace App\Controllers;

use App\Models\ApiAuth;
use App\Models\Auth;
use App\Models\Url;


class HomeController
{
    private $apiAuth;

    public function __construct() {
        $this->apiAuth = new ApiAuth();
    }

    public function index(): void
    {
        Auth::checkWithRedirect();

        $urls = $this->listUserUrls();
        $token = $this->getApiToken();

        require_once __DIR__ . '/../Views/HomeView.php';
    }


    public function listUserUrls(): array
    {

        $userId = Auth::getUserId();
        $urlModel = new Url();
        return $urlModel->getUrlsByUser($userId);
    }

    public function getApiToken(): string {
        $token = $this->apiAuth->getToken();
        if (!$token) {
            $token = $this->apiAuth->createToken(Auth::getUserId());
        }
        return $token;
    }


}