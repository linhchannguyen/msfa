<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertValueRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!config('variables.convert_param_to_base64')){
            return $next($request);
        }
        // (1) no base64 this apis combine with fields on (2) 
        $request_url_no_base64_combine_with_field = [
            'convention-search/detail/save-plan', //C01-S03
            'convention-search/detail/submit-plan', //C01-S03
            'convention-search/detail/save-result', //C01-S03
            'convention-search/detail/submit-result', //C01-S03
        ];
        // (2) no base64 this fields combine with api on (1) 
        $request_field_no_base64_combine_with_url = [
            'convention_file'
        ];
        $request_field_no_base64_care_url = [
            'link_url_fe',
            'info_button_fe'
        ];
        $check_url = false;
        foreach ($request_url_no_base64_combine_with_field as $rul) {
            $pos = strpos($request->path(), $rul);
            if ($pos !== false) {
                $check_url = true;
                break;
            }
        }
        //Recursive
        $toArray = function ($x) use (&$toArray) {
            return is_scalar($x) || $x === null || $x === ""
                ? ($x === "" ? null : $x)
                : array_map($toArray, (array) $x);
        };
        foreach ($request->all() as $key => $value) {
            if ( ($check_url && in_array($key, $request_field_no_base64_combine_with_url)) || in_array($key, $request_field_no_base64_care_url) ) {//Special Case
                continue;
            }else{
                //Check if is file then continue
                $is_file = false;
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if (is_file($val) || empty($val)) {
                            $is_file = true;
                            break;
                        }
                    };
                }
                if ($is_file) {
                    if (is_array($value)) {
                        $request->merge([$key => []]);
                    } else {
                        $request->merge([$key => ""]);
                    }
                    continue;
                } else {
                    //Check if is file then continue
                    $string = substr($value, 0, -10) ?? "";
                    $string = substr($string, 10);
                    $string = strrev($string);
                    if ($this->isJson(base64_decode($string))) {
                        $value = json_decode(base64_decode($string));
                    } else {
                        $value = base64_decode($string);
                    }
                    //Convert base64 param to string|array|object...
                    $array = $toArray($value ?? "");
                    $request->merge([$key => $array]);
                }
            }
        }
        //Convert id url base64 to data
        $string_id = substr($request->id, 0, -10);
        $string_id = substr($string_id, 10);
        $string_id = strrev($string_id);
        if ($this->isJson(base64_decode($string_id))) {
            $id = json_decode(base64_decode($string_id));
        } else {
            $id = base64_decode($string_id);
        }
        $request->route()->setParameter('id',  $id);
        // dd($request->all());
        return $next($request);
    }

    function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
