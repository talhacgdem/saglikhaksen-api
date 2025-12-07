<?php

namespace controllers;

use dto\Response;

class LoginController
{
    /**
     * @return Response
     * @throws \Exception
     */
    public function login(): Response
    {
        $user = $this->parseBasicAuth();

        if (!$user) {
            throw new \Exception("Authorization header missing or invalid", 401);
        }

        return Response::success($this->loginFromUser($user));
    }

    private function loginFromUser($user)
    {
        $curl = curl_init();

        $data = array(
            'action' => 'Login',
            'user' => $user
        );

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
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        // cURL hata kontrolü
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception("cURL Error: " . $error);
        }

        curl_close($curl);

        // JSON parse denemesi
        $decoded = json_decode($response, true);

        // JSON değilse veya AccessToken yoksa text olarak dön
        if (json_last_error() !== JSON_ERROR_NONE || !isset($decoded['AccessToken'])) {
            throw new \Exception("Authorization Failed: " . $response, 401);
        }
        if ($decoded["userData"]['durumu'] !== "Aktif") {
            throw new \Exception("User is inactive ", 403);
        }
        // JSON geçerli ve AccessToken varsa parse edilmiş veriyi dön
        return [
            "name" => $decoded['userData']["k_adi"],
            "surname" => $decoded['userData']["k_soyadi"],
            "email" => $decoded['userData']["eposta"],
            "phone" => $decoded['userData']["tel_no"],
            "memberNo" => '',
            "identityNumber" => '',
            "location" => '',
            "job" => $decoded['userData']["rolu"],
            "active" => $decoded['userData']["durumu"],
            "access_token" => $decoded["AccessToken"]
        ];
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
}


/*
<?php


*/