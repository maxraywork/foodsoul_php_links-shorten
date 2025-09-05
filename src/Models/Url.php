<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Url
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function generateShortLink(): string
    {
        $length = 11;
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $exists = $this->findByCode($code);
        } while ($exists);

        return $code;
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM urls WHERE short_url = ?');
        $stmt->execute([$code]);
        $url = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($url) {
            return $this->addDomainNameToShortUrl($url);
        }
        return null;
    }

    public function shorten(string $longUrl, int $userId): array
    {
        if (!$longUrl) {
            throw new \Exception("URL is required");
        }
        if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
            throw new \Exception("Invalid URL format");
        }
        $longUrl = filter_var($longUrl, FILTER_SANITIZE_URL);

        $query = $this->pdo->prepare('SELECT * FROM urls WHERE long_url = ? AND user_id = ?');
        $query->execute([$longUrl, $userId]);
        $alreadyExists = $query->fetch(PDO::FETCH_ASSOC);

        if ($alreadyExists) {
            return $this->addDomainNameToShortUrl($alreadyExists);
        }

        $shortUrl = $this->generateShortLink();

        $query = $this->pdo->prepare('INSERT INTO urls (user_id, short_url, long_url) VALUES (?, ?, ?)');
        $query->execute([$userId, $shortUrl, $longUrl]);

        $getObj = $this->pdo->prepare('SELECT * FROM urls WHERE user_id = ? AND short_url = ?');
        $getObj->execute([$userId, $shortUrl]);
        $shortUrlObj = $getObj->fetch(PDO::FETCH_ASSOC);

        return $this->addDomainNameToShortUrl($shortUrlObj);
    }

    public function getUrlsByUser(int $userId): array
    {
        $query = $this->pdo->prepare('SELECT id, short_url, long_url, created_at FROM urls WHERE user_id = ?');
        $query->execute([$userId]);
        $urls = $query->fetchAll(PDO::FETCH_ASSOC);
        return $this->addDomainNameToShortUrl($urls);
    }

    public function deleteUrl(int $id, int $userId): bool {
        $query = $this->pdo->prepare('DELETE FROM urls WHERE id = ? AND user_id = ?');
        $query->execute([$id, $userId]);
        return $query->rowCount() > 0;
    }

    private function addDomainNameToShortUrl(array $data): array
    {
        $domain = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $recursive = function (&$item) use (&$recursive, $domain) {
            if (is_array($item)) {
                foreach ($item as &$value) {
                    $recursive($value);
                }
                if (isset($item['short_url'])) {
                    $item['short_url'] = rtrim($domain, '/') . '/' . ltrim($item['short_url'], '/');
                }
            }
        };
        $recursive($data);
        return $data;
    }
}