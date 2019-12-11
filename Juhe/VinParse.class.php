<?php


namespace App\Libraries\VinTools\Juhe;

use App\Libraries\VinTools\Support\CurlRequest;
use App\Libraries\VinTools\Traits\VinToolsResult;
use Zyc\Models\VinData;
use Zyc\Models\VinToolsLog;

class VinParse
{
    use VinToolsResult;

    protected $api_path = 'http://v.juhe.cn/vinParse/query.php';
    protected $app_key = '';
    protected $vin = '';

    protected $module = 'juhe';

    public function __construct($vin)
    {
        $this->vin = $vin;
        $this->app_key = config('vintools.juhe.app_key');
    }

    public function getParse()
    {
        if (!$this->vin) {
            return false;
        }

        $http = new CurlRequest();
        $request = [
            'vin' => $this->vin,
            'key' => $this->app_key,
        ];
        $request_options = [
            'body' => $request,
        ];

        $response = $http->request('POST', $this->api_path, $request_options);
        VinToolsLog::create([
            'module' => $this->module,
            'method' => 'POST',
            'api' => $this->api_path,
            'params' => json_encode($request_options, JSON_UNESCAPED_SLASHES),
            'response' => json_encode($response, JSON_FORCE_OBJECT),
        ]);

        return $this->parseResult($response);
    }

    private function parseResult($response)
    {
        if ($response[0] != '200') {
            return $this->result('error', '请求失败：' . $response[1]);
        }
        $response_data = json_decode($response[1], true);
        if ($response_data['reason'] != 'success' || $response_data['error_code'] != '0') {
            return $this->result('error', '请求失败：' . $response_data['reason']);
        }

        $result = ($response_data['reason'] == 'success' ? 'success' : 'error');
        $message = 'ok';
        $data = $response_data['result']['vehicleList'];
        return $this->result($result, $message, $data);
    }

}