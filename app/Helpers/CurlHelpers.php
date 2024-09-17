<?php

namespace App\Helpers;

Class CurlHelpers{
    public static function exec($method, $url, $param = array(), $token)
    {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        if ($token != '' || $token != null) {
            $auth = 'Authorization: Bearer '.$token;

            array_push($headers, $auth);
        }

        $method = strtoupper($method);

        $curl = curl_init();

        switch ($method) {
            case 'GET':
                if (!empty($param)) {
                    $url .= '?' . http_build_query($param);
                }
                break;

            case 'POST':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($curl, CURLOPT_POST, TRUE);
                if (!empty($param)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($param));
                }
                break;

            case 'PUT':
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                if (!empty($param)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($param));
                }
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // execute
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        if (curl_errno($curl)) {
            throw new \Exception('Request Error: ' . curl_error($curl), $info['http_code']);
        }

        curl_close($curl);

        // return
        $header = trim(substr($response, 0, $info['header_size']));
        $body = substr($response, $info['header_size']);

        $finalResponse = [
            'status' => $info['http_code'],
            'header' => $header,
            'data' => json_decode($body)
        ];

        return $finalResponse;
    }

    public static function get($url, $param = array(), $token)
    {
        return CurlHelpers::exec('GET', $url, $param, $token);
    }

    public static function post($url, $param = array(), $token)
    {
        return CurlHelpers::exec('POST', $url, $param, $token);
    }

    public static function put($url, $param = array(), $token)
    {
        return CurlHelpers::exec('PUT', $url, $param, $token);
    }
}
