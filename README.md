# VinTools
Example

1. 阿里ocr

        //$image='图片地址'
        $vinToolsObj = new VinTools();
        $result = $vinToolsObj->aliOcr($image);
    

2. juhe

        //$vin='17位车架号'
        $vinToolsObj = new VinTools();
        $result = $vinToolsObj->juheParse($vin);
