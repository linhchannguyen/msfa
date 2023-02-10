<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use JWTAuth;
use App\Traits\ApiResponseTrait;
use App\Traits\Base64StringFileTrait;

class FileController extends Controller
{
    use ApiResponseTrait, Base64StringFileTrait;
    public function outputDataFile($filename)
    {
        try {
            $domain = $this->getRefererRequest();
            if ($domain && $this->isDomainAllowGetData($domain)) {
                $path = $this->decodeStringBase64($filename);
                if (!File::exists($path)) {
                    return $this->responseCreated('');
                }
                // check if file is pdf
                if (substr($path, -4) == '.pdf') {
                    $file = File::get($path);
                    $type = File::mimeType($path);

                    $response = Response::make($file, 200);
                    $response->header("Content-Type", $type);

                    return $response;
                }

                return response()->download($path);
            }
            return $this->responseErrorForbidden('forbident');
        } catch (\Throwable $th) {
            throw $th;
            return $this->responseSystemError($th->getMessage());
        }
    }
    protected function getRefererRequest()
    {
        $serveCallTo = '';
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $serveCallTo = $_SERVER['HTTP_ORIGIN'];
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            $serveCallTo = $_SERVER['HTTP_REFERER'];
        } else if (isset($_SERVER['HTTP_HOST'])) {
            $serveCallTo = $_SERVER['HTTP_HOST'];
        }
        if ($serveCallTo) {
            $serveCallTo = str_replace(['http://', 'https://', '/'], '', $serveCallTo);
        }
        return $serveCallTo;
    }
    protected function isDomainAllowGetData($domain)
    {
        $allowUrlGetFiles = explode(",", config('variables.allow_url_get_file'));
        if (in_array($domain, $allowUrlGetFiles)) {
            return true;
        }
        return false;
    }
    protected function isValidTokenDownload($request)
    {
        $auth = '';
        if ($request->header('OriginalToken')) {
            $auth = 'OriginalToken';
        } elseif ($request->header('Authorization')) {
            $auth = 'Authorization';
        }
        $token = str_replace('Bearer ', '', $request->header($auth));

        return true;
    }
}
