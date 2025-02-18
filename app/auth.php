<?php

namespace app;

use JetBrains\PhpStorm\NoReturn;

class Auth
{
    #[NoReturn] public function checkAuth(): void
    {
        session_start();

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            echo json_encode([
                "success" => false,
                "error" => "Not authenticated"
            ]);
            http_response_code(401);
            exit;
        }

        echo json_encode([
            "success" => true,
            "role" => $_SESSION['role']
        ]);
        exit;
    }
}

