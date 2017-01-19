<?php

class WechatManager {

    //更新微信操作权限
    public function updateAccessToken($name = 'tongxin') {
        $output = array('status' => 'no');
        $account = WechatAccount::model()->loadByWxName($name);
        $url = sprintf(WechatRequestUrl::qy_access_token, $account->corp_id, $account->corp_secret);
        $data = https($url);
        $account->access_token = $data['access_token'];
        if ($account->update(array('access_token'))) {
            $output['status'] = 'ok';
        }
        return $output;
    }

    //微信授权
    public function wechatAuth($value, $wxname = 'tongxin') {
        $account = WechatAccount::model()->loadByWxName($wxname);
        $code = '';
        if (isset($value['code'])) {
            //请求中有code，通过code获取openid
            $code = $value['code'];
        } else {
            //返回连接 用作第二次获取openid
            $redirect_uri = urlencode($value['url']);
            $url = sprintf(WechatRequestUrl::qy_code_get, $account->corp_id, $redirect_uri);
            header('Location: ' . $url);
            Yii::app()->end();
        }
        //根据code获取openid
        $url = sprintf(WechatRequestUrl::qy_user_get, $account->access_token, $code);
        $data = https($url);
        return $data;
    }

}
