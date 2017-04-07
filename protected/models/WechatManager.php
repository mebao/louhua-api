<?php

class WechatManager {

    const WX_NAME = 'tongxin';
    const AGENTID = '3';

    //更新微信操作权限
    public function updateAccessToken() {
        $output = array('status' => 'no');
        $account = WechatAccount::model()->loadByWxName(self::WX_NAME);
        $url = sprintf(WechatRequestUrl::qy_access_token, $account->corp_id, $account->corp_secret);
        $data = https($url);
        $account->access_token = $data['access_token'];
        if ($account->update(array('access_token'))) {
            $output['status'] = 'ok';
        }
        return $output;
    }

    //微信授权
    public function wechatAuth($value) {
        $account = WechatAccount::model()->loadByWxName(self::WX_NAME);
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
    public function wechatAuthlogin($value) {
        $account = WechatAccount::model()->loadByWxName(self::WX_NAME);
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
            $loginurl = Yii::app()->params['viewUrl'] . 'wechatlogin?userid=' . $data['UserId'];
        } else {
            $loginurl = Yii::app()->params['viewUrl'] . 'wechatlogin?userid=error';
        }
        header('Location: ' . $loginurl);
        Yii::app()->end();
    }

    //更新用户微信userid
    public function updateWxUserId($user, $value) {
        $account = WechatAccount::model()->loadByWxName(self::WX_NAME);
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
        $viewUrl = Yii::app()->params['viewUrl'] . 'project';
        header('Location: ' . $viewUrl);
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
        $account = WechatAccount::model()->loadByWxName(self::WX_NAME);
        $touser = $values['users'];
        $message = $values['message'];
        $post = array("touser" => $touser, "msgtype" => "text", "agentid" => $account->agent_id, "text" => array("content" => $message), "safe" => "0");
        $data = json_encode($post, JSON_UNESCAPED_UNICODE);
        $url = sprintf(WechatRequestUrl::qy_message_send, $account->access_token);
        return https($url, $data, "POST");
    }

    public function sendMessage($values) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'send message faild!';

        //创建adminmessage
        $model = new AdminMessage();
        $model->conversation_id = $values['id'];
        $model->is_admin = AdminMessage::IS_ADMIN;
        $model->message = $values['message'];
        if ($model->save()) {
            $account = WechatAccount::model()->loadByWxName(self::WX_NAME);
            $touser = $values['userid'];
            $post = array("touser" => $touser, "msgtype" => "text", "agentid" => $account->agent_id, "text" => array("content" => $model->message), "safe" => "0");
            $data = json_encode($post, JSON_UNESCAPED_UNICODE);
            $url = sprintf(WechatRequestUrl::qy_message_send, $account->access_token);
            $wx = https($url, $data, "POST");
            if ($wx['errmsg'] == 'ok') {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            } else {
                $std->errorMsg = 'wechat send message faild!';
            }
        } else {
            $std->errorMsg = 'param error!';
        }
        return $std;
    }

}
