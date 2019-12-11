<?php


namespace App\Libraries\VinTools;


use App\Libraries\VinTools\Aliyun\VinOcr;
use App\Libraries\VinTools\Juhe\VinParse;
use App\Libraries\VinTools\Traits\VinToolsResult;

class VinTools
{
    use VinToolsResult;

    public function aliOcr($image_url)
    {
        if (!$image_url) {
            return false;
        }
        $aliOcr = new VinOcr($image_url);
        $response = $aliOcr->getVin();
        return $this->result($response['result'], $response['message'], $response['data']);

    }

    public function juheParse($vin)
    {
        if (!$vin || strlen($vin) != 17) {
            return false;
        }
        $juhe_obj = new VinParse($vin);
        $response = $juhe_obj->getParse();
        return $this->result($response['result'], $response['message'], $response['data']);
    }

}