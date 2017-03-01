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

    //微信授权
    public function wechatAuthlogin($value, $wxname = 'tongxin') {
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
        if (isset($data['UserId'])) {
            $loginurl = 'http://wap.louhua.meb168.com/#/layout/wechatlogin?userid=' . $data['UserId'];
        } else {
            $loginurl = 'http://wap.louhua.meb168.com/#/layout/wechatlogin?userid=error';
        }
        header('Location: ' . $loginurl);
        Yii::app()->end();
    }

    //更新用户微信userid
    public function updateWxUserId($user, $value, $wxname = 'tongxin') {
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
        if (isset($data['UserId'])) {
            $user->wx_userid = $data['UserId'];
            $user->update(array('wx_userid'));
        }
        header('Location: http://wap.louhua.meb168.com/#/layout/project');
        Yii::app()->end();
    }

    public function wechatcode($value) {
        if (isset($value['auth_code'])) {
            $account = WechatAccount::model()->loadByWxName("tongxin");
            $url = sprintf("https://qyapi.weixin.qq.com/cgi-bin/service/get_login_info?access_token=%s", $account->access_token);
            $post = array('auth_code' => $value['auth_code']);
            $data = https($url, arrayToJson($post), 'POST');
            Yii::log(arrayToJson($data), 'info', '微信返回用户消息');
            return $data;
        }
    }

    public function sendHouse($values) {
        $account = WechatAccount::model()->loadByWxName("tongxin");
        $touser = $values['users'];
        $message = $values['message'];
        $post = array("touser" => $touser, "msgtype" => "text", "agentid" => "3", "text" => array("content" => $message), "safe" => "0");
        $data = json_encode($post, JSON_UNESCAPED_UNICODE);
        $url = sprintf(WechatRequestUrl::qy_message_send, $account->access_token);
        return https($url, $data, "POST");
    }

}
