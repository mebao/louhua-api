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
        $result = ""; //默认返回值
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
            //log记录
            Yii::log($postStr, 'info', '微信推送信息');
            //将字符串变成对象
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //log记录
            Yii::log(json_encode($postObj), 'info', '微信信息对象');
            //消息类型  
            $msgTpye = trim($postObj->MsgType);
            switch ($msgTpye) {//消息类型分离
                //微信的信息推送
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                //用户发送的普通消息
                case "text"://文本消息
                    $result = $this->receiveText($postObj);
                    break;
                case "image"://图片
                    $result = $this->transmitImage($postObj);
                    break;
                case "voice"://语音
                    $result = $this->transmitVoice($postObj);
                    break;
                case "video"://视频
                    $result = $this->transmitVideo($postObj);
                    break;
                case "shortvideo"://小视频
                    $result = $this->transmitShort($postObj);
                    break;
                case "location"://地理位置
                    $result = $this->transmitLocation($postObj);
                    break;
                case "link"://链接消息
                    $result = $this->transmitLink($postObj);
                    break;
            }
            if ($encrypt_type == 'aes') {//对返回给微信服务器的消息进行加密处理
                $encryptMsg = ''; //加密后的密文
                $errCode = $crypt->encryptMsg($result, $timestamp, $nonce, $encryptMsg);
                $result = $encryptMsg;
            }
        }
        return $result;
    }

    //接收事件消息
    public function receiveEvent($object) {
        $content = "";
        switch ($object->Event) {
            //用户关注公众号
            case "subscribe":
                $content = "您好，宝儿蜜是为高端儿童连锁诊所打造的一款微信公众号。
                    \n您可以通过此公众号预约儿童医生,为您节省时间精力!
                    \n你我共同关注初升的太阳";
                break;
            //在模版消息发送任务完成后，微信服务器会将是否送达成功作为通知推送过来
            case "TEMPLATESENDJOBFINISH":
                Yii::log("微信模板消息", "info", json_encode($object));
                break;
            default:
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收文本消息，直接转客服处理
    public function receiveText($object) {
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                   </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复文本消息
    public function transmitText($object, $content = "") {
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                  </xml>";
        if (strIsEmpty($content)) {
            if ($object->Content == '你好') {
                $content = "您好!";
            } else {
                $content = "客服繁忙,请稍后!";
            }
        }
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    public function transmitImage($object, $imageId = "J9qAdK2nKrNzVmAfKiqYuuuCaVhf8RBS3O4_9xeJ0qw") {
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                   </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $imageId);
        return $result;
    }

    //回复语音消息
    public function transmitVoice($object, $voiceId = "J9qAdK2nKrNzVmAfKiqYuv9wbC1PtbpPdjoTx3WV1sw") {
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    <Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>
                   </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $voiceId);
        return $result;
    }

    //回复视频消息
    public function transmitVideo($object) {
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                   </xml>";

        $content = "给我看的?";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    public function transmitShort($object) {
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                   </xml>";

        $content = "excuse me?";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    public function transmitLocation($object) {
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                  </xml>";

        $content = "您所处的经度:" . $object->Location_Y . " 纬度:" . $object->Location_X;
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    public function transmitLink($object) {
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                   </xml>";

        $content = "不是发福利的链接都是耍流氓!";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    public function sendTemplateMessage($type, $token, $openId, $params) {

        switch ($type) {
            case "booking":
                $tid = "BjGGg_MKycPd2bnTGINNdJfAy_g1J5hg4kw2XcJauTs";
                $data = array(
                    "first" => array("value" => "您好，您已预约挂号成功。", "color" => "#743A3A"),
                    "patientName" => array("value" => "占丢丢", "color" => "#173177"),
                    "patientSex" => array("value" => "男", "color" => "#173177"),
                    "hospitalName" => array("value" => "上海人民医院", "color" => "#173177"),
                    "department" => array("value" => "心理科", "color" => "#173177"),
                    "doctor" => array("value" => "杨教授", "color" => "#173177"),
                    "seq" => array("value" => "refNo95270001", "color" => "#C4C400"),
                    "remark" => array("value" => "请按时到达!\n祝您早日康复!", "color" => "#0000FF"),
                );
                break;
            case "pay":
                $tid = "KNkOA3hCzbrYbC55h7wIjGMGi_XGxZo3ILR7VqH03zU";
                break;
            case 'task':
                $tid = "m78ilw_agR5InAuQJ4h5MQNYU1wWUIryfEeCbg9JnY0";
                break;
        }
        $post = array("touser" => $openId, "template_id" => $tid, "url" => $params['url'], "topcolor" => "#7B68EE", "data" => $data);
        $url = sprintf(WxRequestUrl::template_send, $token);
        return https($url, json_encode($post, JSON_UNESCAPED_UNICODE), 'POST');
    }

}
