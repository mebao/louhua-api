<?php

require_once dirname(__FILE__) . '/../sdk/vendor/autoload.php';
require_once dirname(__FILE__) . '/../sdk/vendor/config.php';

// 引入鉴权类
use Qiniu\Auth;

class FileManager {

    private $accessKey = Config::ACCESS_KEY;
    private $secretKey = Config::SECRET_KEY;

    //获取上传的权限
    public function getUploadToken() {
        //根据文件类型 定位其空间名
        $bucket = Config::getBucketByTableName("aa");
        $auth = new Auth($this->accessKey, $this->secretKey);
        $token = $auth->uploadToken($bucket);
        $output = array('uptoken' => $token);
        return $output;
    }

}
