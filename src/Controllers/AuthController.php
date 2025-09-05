<?php

namespace App\Controllers;

use App\Models\Auth;

class AuthController
{

    private $auth;

    public function __construct()
    {
        $this->auth = new Auth;
    }

    public function showLoginView(): void
    {
        //Redirect if logged in
        if (Auth::check()) {
            header("Location: /");
            exit;
        }
        require_once __DIR__ . "/../Views/LoginView.php";
    }

    public function showVerifyView(): void
    {
        //Redirect if logged in
        if (Auth::check()) {
            header("Location: /");
            exit;
        }
        $email = $_POST["email"] ?? "";
        $code = $_POST["code"] ?? "";
        if ($email === "") {
            header("Location: /login");
            exit;
        }
        if ($code === "") {
            try {
                $this->auth->sendOtp($email);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        } else {
            $otpIsGood = $this->auth->checkOtp($email, $code);
            if ($otpIsGood) {
                $userId = $this->auth->getUserIdFromDb($email);
                $this->auth->login($userId);
                if (Auth::check()) {
                    header("Location: /");
                    exit;
                }
            } else {
                $error = 'Wrong OTP';
            }
        }
        require_once __DIR__ . "/../Views/VerifyView.php";
    }

    public function logout(): void
    {
        $this->auth->logout();
        header("Location: /");
        exit;
    }
}