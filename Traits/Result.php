<?php

namespace App\Libraries\VinTools\Traits;

trait VinToolsResult
{
    public function result($result = 'success', $message = 'ok', $data = [])
    {
        return compact('result', 'message', 'data');
    }
}

