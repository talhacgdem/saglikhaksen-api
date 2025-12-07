<?php

namespace controllers;

use dto\Response;
use dto\User;

class LoginController
{
    /**
     * @return Response
     * @throws \Exception
     */
    public function login(): Response
    {
        $user = $_POST['identityNumber'];

        if (!$user) {
            throw new \Exception("Authorization header missing or invalid", 401);
        }

        return Response::success($this->loginFromUser($user));
    }

    /**
     * @return User
     */
    private function loginFromUser($user): User
    {
        $data = array(
            'action' => 'Login',
            'user' => array(
                'email' => 'biomuratozekinci@gmail.com',
                'password' => '123456'
            )
        );

        $response = $this->executeCurl($data);

        // JSON parse denemesi
        $decoded = json_decode($response, true);

        // JSON değilse veya AccessToken yoksa text olarak dön
        if (json_last_error() !== JSON_ERROR_NONE || !isset($decoded['AccessToken'])) {
            throw new \Exception("Authorization Failed: " . $response, 401);
        }
        if ($decoded["userData"]['durumu'] !== "Aktif") {
            throw new \Exception("User is inactive ", 403);
        }

        $access_token = $decoded["AccessToken"];
        return $this->getUser($access_token, $user);
    }

    private function getUser($access_token, $identityNumber): User
    {
        $response = $this->executeCurl(array(
            'action' => 'GetUsers'
        ), $access_token);
        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new \Exception('Expected array response');
        }

        foreach ($decoded as $userData) {
            if (
                isset($userData['kimlik_no']) && $userData['kimlik_no'] == $identityNumber &&
                isset($userData['durumu']) && $userData['durumu'] === 'Aktif'
            ) {
                return new User($userData);
            }
        }
        throw new \Exception("User with identity number {$identityNumber} not found");
    }

    /**
     * Basic Auth header saf şekilde alır ve çözer.
     *
     * @return array{username: string, password: string}|null
     */
    private function parseBasicAuth(): ?array
    {
        $header = $this->getAuthorizationHeader();
        if (!$header) {
            return null;
        }

        // Only Basic scheme supported
        if (!preg_match('/^Basic\s+(.*)$/i', $header, $matches)) {
            return null;
        }

        $decoded = base64_decode($matches[1], true);
        if ($decoded === false) {
            return null; // invalid base64
        }

        // must contain username:password
        $parts = explode(':', $decoded, 2);
        if (count($parts) !== 2) {
            return null;
        }

        return [
            'email' => $parts[0],
            'password' => $parts[1]
        ];
    }

    /**
     * Tüm sunucu tipleri için en güvenli Authorization getter
     */
    private function getAuthorizationHeader(): ?string
    {
        // 1. Direct
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        // 2. Nginx
        if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        // 3. Apache
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strtolower($k) === 'authorization') {
                    return trim($v);
                }
            }
        }

        return null;
    }

    private function executeCurl(array $data, string $access_token = null): bool|string
    {

        $headers = array(
            'Content-Type: application/json'
        );
        if (isset($access_token)) {
            $headers[] = 'Authorization: Bearer ' . $access_token;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://server.saglikhaksen.com/index',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        // cURL hata kontrolü
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception("cURL Error: " . $error);
        }

        curl_close($curl);

        return $response;
    }
}


/*
<?php


*/