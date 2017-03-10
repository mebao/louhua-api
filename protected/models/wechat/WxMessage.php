<?php

require_once dirname(__FILE__) . '/../sdk/wechat/WXBizMsgCrypt.php';

class WxMessage {

    /**
     * @params 微信的get参数
     * @msgStr 消息内容
     * $wxconfig 微信配置参数
     */
    public function catchMassage($params, $msgStr, $wxconfig) {
        $timestamp = $params['timestamp'];
        $nonce = $params["nonce"];
        $msg_signature = $params['msg_signature'];
        //判断消息的加密方式 默认为不加密
        $encrypt_type = (isset($params['encrypt_type']) && ($params['encrypt_type'] == 'aes')) ? "aes" : "raw";
        //消息体
        if (strIsEmpty($msgStr) === false) {
            if ($encrypt_type == 'aes') {//加密模式，先解密
                $crypt = new WXBizMsgCrypt($wxconfig['token'], $wxconfig['encodingAESKey'], $wxconfig['appId']);
                $decryptMsg = "";  //引用传递 解密后的明文  
                $errCode = $crypt->DecryptMsg($msg_signature, $timestamp, $nonce, $msgStr, $decryptMsg);
                //$decryptMsg为xml的字符串
                $postStr = $decryptMsg;
            } else {
                $postStr = $msgStr;
            }
            //将字符串变成对象
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //log记录
            Yii::log(json_encode($postObj), 'info', '微信信息对象');
            //消息类型  
            $msgTpye = trim($postObj->MsgType);
            switch ($msgTpye) {//消息类型分离
                //用户发送的普通消息
                case "text"://文本消息
                    $this->receiveText($postObj);
                    break;
            }
        }
        return null;
    }

    //回复文本消息
    public function receiveText($object) {
        //处理微信发回来的信息
        $conver = Conversation::model()->loadByWxUserId($object->FromUserName);
        if (isset($conver)) {
            $model = new AdminMessage();
            $model->conversation_id = $conver->id;
            $model->is_admin = AdminMessage::ISNOT_ADMIN;
            $model->message = $object->Content;
            $model->save();
        }
        //调用前台推送
        $data = array('appkey' => '2ba7f5ae-99ff-417b-bba0-903c4e60a5da', 'channel' => $conver->channel, 'content' => $object->Content);
        $url = 'http://goeasy.io/goeasy/publish';
        $date = https($url, $data, 'POST');
        Yii::log(json_encode($date), 'info', '消息推送');
    }

}
