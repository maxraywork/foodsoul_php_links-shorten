<?php
namespace App\Models;

use App\Core\Database;

class ApiAuth
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function createToken(int $userId, int $ttl = 86400): string
    {
        $token = bin2hex(random_bytes(32));
        $stmt = $this->pdo->prepare('INSERT INTO api_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))');
        $stmt->execute([$userId, $token, $ttl]);
        return $token;
    }

    public function getToken(): string {
        $query = $this->pdo->prepare('SELECT token FROM api_tokens WHERE user_id = ? AND (expires_at IS NULL OR expires_at >= NOW())');
        $query->execute([Auth::getUserId()]);
        $token = $query->fetch(\PDO::FETCH_ASSOC);
        return $token['token'] ?? "";
    }

    public function checkToken(string $token): ?int
    {
        $query = $this->pdo->prepare('SELECT user_id FROM api_tokens WHERE token = ? AND (expires_at IS NULL OR expires_at >= NOW())');
        $query->execute([$token]);
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        return $row['user_id'] ?? null;
    }
}