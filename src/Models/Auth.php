<?php

namespace App\Models;
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

use App\Core\Database;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Auth
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public static function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function login(int $userId): void {
        $_SESSION['user_id'] = $userId;
    }
    public static function logout(): void
    {
        unset($_SESSION['user_id']);
    }

    public static function checkWithRedirect(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    private function generateShortCode(): string
    {
        $length = 6;
        $chars = '0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }


    public function sendOtp(string $email): void
    {
        $email = trim($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email');
        }

        $hasCodeSent = $this->pdo->query('SELECT * FROM `otps` WHERE `email` = "' . $email . '" AND `expires_at` >= CURRENT_TIMESTAMP')->fetch();
        if ($hasCodeSent) {
            return;
        }

        $code = $this->generateShortCode();

        $query = $this->pdo->prepare("INSERT INTO otps (email, code, expires_at) VALUES (?, ?, NOW() + INTERVAL 10 MINUTE)");
        $query->execute([$email, $code]);

        try {
            $mail = new PHPMailer();

            $mail->IsSMTP();
            $mail->CharSet = 'UTF-8';

            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPDebug = 0;
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];

            $mail->setFrom($_ENV['SMTP_EMAIL']);
            $mail->addAddress($email);

            $mail->isHTML(true);                       // Set email format to HTML
            $mail->Subject = 'Short link OTP';
            $mail->Body = 'Your one time password: <b>' . $code . '</b>';
            $mail->AltBody = $code;

            $mail->Timeout = 20;

            $mail->send();

        } catch (Exception $e) {
            throw new \Exception("Mailer Error: " . $mail->ErrorInfo . '\n');
        }
    }

    public function checkOtp(string $email, string $code): bool
    {
        $query = $this->pdo->prepare('SELECT * FROM `otps` WHERE `email` = ? AND `code` = ? AND `expires_at` >= CURRENT_TIMESTAMP');
        $query->execute([$email, $code]);
        $otp = $query->fetch();
        return !!$otp;
    }

    public function getUserIdFromDb(string $email): int {
        $query = $this->pdo->prepare("SELECT * FROM users WHERE `email`=?");
        $query->execute([$email]);
        $user = $query->fetch();
        if (!$user) {
            $query = $this->pdo->prepare('INSERT INTO users (email) VALUES (?)');
            $query->execute([$email]);
            return $this->pdo->lastInsertId();
        }
        return $user['id'];
    }

}