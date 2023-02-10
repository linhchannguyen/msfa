<?php

namespace App\Traits;

trait Base64StringFileTrait
{
    public function encodeBase64String($path_url, $token = null)
    {
        if ($token == null) {
            $auth = "";
            $request = request();
            if ($request->header('OriginalToken')) {
                $auth = 'OriginalToken';
            } elseif ($request->header('Authorization')) {
                $auth = 'Authorization';
            }
            $token = str_replace('Bearer ', '', $request->header($auth));
        }
        $urlAws = config('filesystems.disks.public_server.url');
        $parram = [
            'token' => $token,
            'path' => $path_url
        ];
        $path_url_encode = $this->encodeStringBase64Json($parram);
        return $urlAws . '/' . $path_url_encode;
    }

    public function encodeStringBase64Json($parram)
    {
        $base_64 = base64_encode(json_encode($parram));
        // reverse string
        $base_64 = strrev($base_64);
        // BU5_MSFA-1715 - change position add random string - sau kí tự thứ 4 và trước 1 kí tự cuối
        return substr($base_64, 0, 4) . randomStringUnique(10) . substr($base_64, 4, -1) . randomStringUnique(10) . substr($base_64, -1);
    }

    public function decodeStringBase64($path_url_encode)
    {
        $string = $this->calculatorDecodeBase64($path_url_encode);
        if (!isset($string->path)) {
            return '';
        } else {
            if (empty($string->path)) {
                return '';
            }
        }
        return storage_path('app/public/' . $string->path);
    }
    public function decodeStringBase64Token($path_url_encode)
    {
        $string = $this->calculatorDecodeBase64($path_url_encode);
        if (!isset($string->token)) {
            return '';
        }
        return $string->token;
    }
    public function calculatorDecodeBase64($path_url_encode)
    {
        // BU5_MSFA-1715 - change position add random string - sau kí tự thứ 4 và trước 1 kí tự cuối
        $string = substr($path_url_encode, 0, 4) . substr($path_url_encode, 14, -11) . substr($path_url_encode, -1);
        // reverse string
        $string = strrev($string);
        $string = json_decode(base64_decode($string));

        return $string;
    }
}
