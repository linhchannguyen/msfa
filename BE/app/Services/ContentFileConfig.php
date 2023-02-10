<?php

namespace App\Services;


class ContentFileConfig
{
    /**
     * @return Array
     */
    public function getContentFile () {
        $dataConfig = @file_get_contents(config('config_general_source.url_file_config_web'), true);
        if ( $dataConfig ) {
            $dataConfig = json_decode($dataConfig, true);
            return $dataConfig;
        }
        return [];
    }
    public function getValueConfig ($nameField) {
        $dataConfig = $this->getContentFile();
        try {
            if (strpos($nameField, '.') !== false) {
                $arrayDatas = explode('.', $nameField);
                $result = "";
                foreach($arrayDatas as $key => $arrayData) {
                    if ($key == 0) {
                        $result = $dataConfig[$arrayData];
                    } else {
                        $result = $result[$arrayData];
                    }
                }
                return $result;
            } else {
                return $dataConfig[$nameField];
            }
        } catch (\Throwable $th) {
            return null;
        }
    }
}
