<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApimdController
 *
 * @author shuming
 */
class XlhapiController extends Controller {

    Const APPLICATION_ID = 'ASCCPE';

    public function setDomainWhiteList() {
        $this->domainWhiteList = array(
            "http://wap.louhua.meb168.com",
        );
    }

    public function init() {
        parent::init();
        $this->setDomainWhiteList();
        $domainList = array('http://wap.louhua.meb168.com');
        $this->addHeaderSafeDomains($domainList);
        header('Access-Control-Allow-Credentials:true');      // 允许携带 用户认证凭据（也就是允许客户端发送的请求携带Cookie）        	
        header('Access-Control-Allow-Headers: Origin,X-Requested-With,Authorization,Accept,Content-Type,if-modified-since');
        header('Access-Control-Allow-Methods: OPTIONS,GET,PUT,DELETE,POST');
    }

    public function actionLie($model) {
        Yii::app()->end(200, true);
    }

    //查看列表
    public function actionList($model) {
        $values = $_GET;
        $statusCode = 200;
        try {
            switch ($model) {
                case 'uploadtoken':
                    $fileMgr = new FileManager();
                    $output = $fileMgr->getUploadToken();
                    break;
                case 'postlist':
                    $user = $this->userLoginRequired($values);
                    $values['user_id'] = $user->id;
                    $apiview = new ApiViewUserPostList($values);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'project':
                    $projectid = null;
                    if (isset($values['projectid'])) {
                        $projectid = $values['projectid'];
                    }
                    $apiview = new ApiViewCountPost($projectid);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'orderhavelist':
                    $user = $this->userLoginRequired($values);
                    $values['user_id'] = $user->id;
                    $apiview = new ApiViewOrderHaveList($values);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'orderwantlist':
                    $user = $this->userLoginRequired($values);
                    $values['user_id'] = $user->id;
                    $apiview = new ApiViewOrderWantList($values);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'selectoptions'://下拉 
                    $apiview = new ApiViewOptions();
                    $output = $apiview->loadApiViewData();
                    break;
                case 'userinfo':
                    $user = $this->userLoginRequired($values);
                    $apiview = new ApiViewUserInfo($user);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'sendemail':
                    $url = $this->createAbsoluteUrl("xlhapi/verifyemail");
                    $email = $values['email'];
                    $mgr = new EmailManager();
                    $output = $mgr->sendEmailVerifyUser($email, $url);
                    break;
                case 'verifyemail':
                    $authMgr = new AuthManager();
                    $authMgr->verifyAuthSmsCode($values['email'], $values['code']);
                    break;
                //微信部分接口
                case 'tasktoken':
                    $mgr = new WechatManager();
                    $output = $mgr->updateAccessToken();
                    break;
                case 'wechatuser':
                    $values['url'] = $this->createAbsoluteUrl("xlhapi/wechatuser");
                    $mgr = new WechatManager();
                    $output = $mgr->wechatAuthlogin($values);
                    break;
                case 'loginwxuser':
                    $mgr = new UserManager();
                    $output = $mgr->wechatLogin($values);
                    break;
                case 'wxcheck':
                    if (isset($values['echostr'])) {
                        $this->checkSignature($values);
                    } else {
                        $this->responseMsg($values);
                    }
                    break;
                default:
                    $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                    Yii::app()->end();
            }
        } catch (CHttpException $chex) {
            $statusCode = 400;
        } catch (CDbException $cdbex) {
            $statusCode = 503;
        } catch (CException $cex) {
            $statusCode = 500;
        }
        if (empty($output)) {
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {
            //$output = $this->encryptOutput($output);
            $this->renderJsonOutput($output, true, $statusCode);
        }
    }

    //具体信息展示页面
    public function actionDelete($model, $id) {
        $values = $_GET;
        $statusCode = 200;
        try {
            switch ($model) {
                case 'mypost':
                    $user = $this->userLoginRequired($values);
                    $mgr = new PostManager();
                    $output = $mgr->deleteMyPost($values['post_type'], $id, $user->id);
                    break;
                default:
                    $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                    Yii::app()->end();
            }
        } catch (CHttpException $chex) {
            $statusCode = 400;
        } catch (CDbException $cdbex) {
            $statusCode = 503;
        } catch (CException $cex) {
            $statusCode = 500;
        }
        if (empty($output)) {
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {
            $this->renderJsonOutput($output, true, $statusCode);
        }
    }

    //创建
    public function actionCreate($model) {
        $post = $_POST;
        $statusCode = 200;
        if (empty($post)) {
            //json参数
            $post = CJSON::decode($this->getPostData());
        }
        try {
            switch ($model) {
                case 'userregist'://用户注册
                    $url = $this->createAbsoluteUrl("xlhapi/verifyemail");
                    $post['url'] = $url;
                    $apipost = new ApiPostUserRegist($post);
                    $output = $apipost->run();
                    break;
                case 'useraccount'://用户补全信息
                    $apipost = new ApiPostUserAccount($post);
                    $output = $apipost->run();
                    break;
                case 'userlogin'://登录
                    $apipost = new ApiPostPwdLogin($post);
                    $output = $apipost->run();
                    break;
                case 'userpost':
                    $user = $this->userLoginRequired($post);
                    $post['user_id'] = $user->id;
                    $apipost = new ApiPostUserPost($post);
                    $output = $apipost->run();
                    break;
                case 'userwatch':
                    $user = $this->userLoginRequired($post);
                    $post['user_id'] = $user->id;
                    $post['real_name'] = $user->real_name;
                    $apipost = new ApiPostUserWatch($post);
                    $output = $apipost->run();
                    break;
                default:
                    $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                    Yii::app()->end();
            }
        } catch (CHttpException $chex) {
            $statusCode = 400;
        } catch (CDbException $cdbex) {
            $statusCode = 503;
        } catch (CException $cex) {
            $statusCode = 500;
        }
        if (empty($output)) {
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {
            //$output = $this->encryptOutput($output);
            $this->renderJsonOutput($output, true, $statusCode);
        }
    }

    public function actionUpdate($model, $id) {
        if (isset($id) === false) {
            $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => 'Error: Parameter <b>id</b> is missing'));
        }
        $values = $_POST;
        $statusCode = 200;
        if (empty($values)) {
            //json参数
            $values = CJSON::decode($this->getPostData());
        }
        try {
            switch ($model) {
                case 'updatemypost':
                    $user = $this->userLoginRequired($values);
                    $values['user_id'] = $user->id;
                    $postMgr = new PostManager();
                    $output = $postMgr->updatePost($id, $values);
                    break;
                default:
                    $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                    Yii::app()->end();
            }
        } catch (CHttpException $chex) {
            $statusCode = 400;
        } catch (CDbException $cdbex) {
            $statusCode = 503;
        } catch (CException $cex) {
            $statusCode = 500;
        }
        if (empty($output)) {
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {
            $this->renderJsonOutput($output, true, $statusCode);
        }
    }

    //获取请求内容以及根据类型回复相关消息
    public function responseMsg($params) {
        $account = WechatAccount::model()->loadByWxName("tongxin");
        //内容体
        $msgStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $wxconfig = array("token" => $account->token, "encodingAESKey" => $account->encoding_key, "appId" => $account->corp_id);
        $message = new WxMessage();
        $result = $message->catchMassage($params, $msgStr, $wxconfig);
        Yii::log($result, "info", "微信回复结果");
        echo $result;
        Yii::app()->end();
    }

    private function checkSignature($values) {
        require_once dirname(__FILE__) . '/../sdk/wechat/WXBizMsgCrypt.php';
        $account = WechatAccount::model()->loadByWxName("tongxin");
        $msgSignature = $values["msg_signature"];
        $timestamp = $values["timestamp"];
        $nonce = $values["nonce"];
        $echoStr = $values['echostr'];
        // 需要返回的明文
        $sEchoStr = "";
        $wxcpt = new WXBizMsgCrypt($account->token, $account->encoding_key, $account->corp_id);
        $errCode = $wxcpt->VerifyURL($msgSignature, $timestamp, $nonce, $echoStr, $sEchoStr);
        if ($errCode == 0) {
            print($sEchoStr);
        } else {
            print("ERR: " . $errCode . "\n\n");
        }
        Yii::app()->end();
    }

    private function userLoginRequired($values) {
        $output = new stdClass();
        $output->status = EApiViewService::RESPONSE_NO;
        $output->errorCode = ErrorList::BAD_REQUEST;
        if (isset($values['username']) === false || isset($values['token']) === false) {
            $output->errorMsg = 'not token!';
            $this->renderJsonOutput($output);
        }
        $username = $values['username'];
        $token = $values['token'];
        $authMgr = new AuthManager();
        $authUserIdentity = $authMgr->authenticateUserByToken($username, $token);
        if (is_null($authUserIdentity) || $authUserIdentity->isAuthenticated === false) {
            $output->errorMsg = 'username or token error!';

            $this->renderJsonOutput($output);
        }

        return $authUserIdentity->getUser();
    }

    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html') {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        header('Content-type: ' . $content_type);

        if ($body != '') {
            echo $body;
        } else {
            $message = '';
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <html>
        <head>
        <meta http-equiv = "Content-Type" content = "text/html; charset=iso-8859-1">
        <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
        </head>
        <body>
        <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
        <p>' . $message . '</p>
        <hr />
        <address>' . $signature . '</address>
        </body>
        </html>';

            echo $body;
        }
        Yii::app()->end();
    }

    private function _getStatusCodeMessage($status) {
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}
