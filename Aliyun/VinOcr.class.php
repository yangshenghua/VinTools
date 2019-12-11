<?php


namespace App\Libraries\VinTools\Aliyun;


use App\Libraries\VinTools\Support\CurlRequest;
use App\Libraries\VinTools\Traits\VinToolsResult;
use Zyc\Models\VinToolsLog;

class VinOcr
{
    use VinToolsResult;

    protected $api_path = 'https://vin.market.alicloudapi.com/api/predict/ocr_vin';

    protected $app_code = '';
    protected $image_url = '';

    protected $module = 'ocr';

    public function __construct($image_url)
    {
        $this->app_code = config('vintools.aliyun.app_code');
        $this->image_url = $image_url;
    }

    public function getVin()
    {
        if (!$this->image_url) {
            return false;
        }

        $http = new CurlRequest();
        $request = [
            "image" => $this->image_url
        ];

        $request_options = [
            'body' => json_encode($request),
        ];

        $request_options['headers'] = [
            'Authorization' => 'APPCODE ' . $this->app_code,
            'Content-Type' => 'application/json; charset=UTF-8',
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

        if (!$response_data) {
            return $this->result('error', '请求失败：' . $response[1]);
        }

        $result = ($response_data['success'] == true ? 'success' : 'error');
        $message = 'ok';
        $data = $response_data['vin'];
        return $this->result($result, $message, $data);
    }

    private function result($result = 'success', $message = 'ok', $data = [])
    {
        return compact('result', 'message', 'data');
    }


}