<?php

class CrmapiController extends Controller {

    Const APPLICATION_ID = 'ASCCPE';

    public function setDomainWhiteList() {
        $this->domainWhiteList = array(
            "http://192.168.10.79",
            "http://meb.mingyizd.com"
        );
    }

    public function init() {
        parent::init();
        $this->setDomainWhiteList();
        $domainList = array('http://meb.mingyizd.com');
        $this->addHeaderSafeDomains($domainList);
        header('Access-Control-Allow-Credentials:true');      // 允许携带 用户认证凭据（也就是允许客户端发送的请求携带Cookie）        	
        header('Access-Control-Allow-Headers: Origin,X-Requested-With,Authorization,Accept,Content-Type,if-modified-since,Cache-Control');
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
                case 'needrole'://需要授权的admin
                    $this->userLoginRequired($values);
                    $apiview = new ApiViewUserNeedRole();
                    $output = $apiview->loadApiViewData();
                    break;
                case 'wantpending':
                    $this->userLoginRequired($values);
                    $apiview = new ApiViewWantPending();
                    $output = $apiview->loadApiViewData();
                    break;
                case 'searchhouse':
                    $apiview = new ApiViewSearchHouse($values);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'exporthouse':
                    $apiview = new ApiViewSearchHouse($values);
                    $data = $apiview->loadApiViewData();
                    $mgr = new ExeclManage();
                    $mgr->exporthouse($data->results->list);
                    break;
                case 'havepending':
                    $this->userLoginRequired($values);
                    $apiview = new ApiViewHavePending();
                    $output = $apiview->loadApiViewData();
                    break;
                case 'matchpending':
                    $this->userLoginRequired($values);
                    $apiview = new ApiViewMacthPending();
                    $output = $apiview->loadApiViewData();
                    break;
                case 'searchadmins':
                    //$this->userLoginRequired($values);
                    $apiview = new ApiViewSearchAdmin($values);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'exportadmins'://admin数据导出
                    $apiview = new ApiViewSearchAdmin($values);
                    $data = $apiview->loadApiViewData();
                    $mgr = new ExeclManage();
                    $mgr->exportadmins($data->results->admins);
                    break;
                case 'searchagents':
                    //$this->userLoginRequired($values);
                    $apiview = new ApiViewSearchAgent($values);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'templetadmins'://admin模板导出
                    $mgr = new ExeclManage();
                    $mgr->exportTemplet('adminTemplet', StatCode::loadTempletAdmin());
                    break;
                case 'templetagents'://agent模板导出
                    $mgr = new ExeclManage();
                    $mgr->exportTemplet('agentTemplet', StatCode::loadTempletAgent());
                    break;
                case 'projectlist':
                    $apiview = new ApiViewSearchProject($values);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'optionproject':
                    $apiview = new ApiViewOptionsProject();
                    $output = $apiview->loadApiViewData();
                    break;
                case 'otheroptions':
                    $mgr = new PostManager();
                    $output = $mgr->otherOptions();
                    break;
                case 'optionsuser':
                    $mgr = new UserManager();
                    $output = $mgr->userOptions();
                    break;
                case 'exportagents':
                    $apiview = new ApiViewSearchAgent($values);
                    $data = $apiview->loadApiViewData();
                    $mgr = new ExeclManage();
                    $mgr->exportagents($data->results->agents);
                    break;
                case 'templehave':
                    $mgr = new ExeclManage();
                    $mgr->exportTemplet('houseHaveTemplet', StatCode::loadTempletHave());
                    break;
                case 'templewant':
                    $mgr = new ExeclManage();
                    $mgr->exportTemplet('houseWantTemplet', StatCode::loadTempletHave());
                    break;
                case 'searchtask':
                    $apiview = new ApiViewSearchTask($values);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'mytask':
                    $user = $this->userLoginRequired($values);
                    $apiview = new ApiViewMyTask($user->id);
                    $output = $apiview->loadApiViewData();
                    break;
                //微信相关接口
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
                case 'deleteadmin':
                    $userMgr = new UserManager();
                    $output = $userMgr->deleteUser($id);
                    break;
                case 'deleteagent':
                    $userMgr = new UserManager();
                    $output = $userMgr->deleteUser($id);
                    break;
                case 'deleteproject':
                    $mgr = new HouseManager();
                    $output = $mgr->deleteProject($id);
                    break;
                case 'deletepicture':
                    $mgr = new HouseManager();
                    $output = $mgr->deletePicture($id);
                    break;
                case 'deletehouse':
                    $mgr = new PostManager();
                    $output = $mgr->deleteHouse($id, $values['type']);
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

    //具体信息展示页面
    public function actionView($model, $id) {
        $values = $_GET;
        $statusCode = 200;
        try {
            switch ($model) {
                case 'adminuserinfo':
                    $apiview = new ApiViewAdminUserInfo($id);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'projectinfo':
                    $apiview = new ApiViewCountProject($id);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'macthinfo':
                    $apiview = new ApiViewMatchInfo($id, $values['type']);
                    $output = $apiview->loadApiViewData();
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
                case 'adminregist'://超管添加管理员
                    $apipost = new ApiPostAdminRegist($post);
                    $output = $apipost->run();
                    break;
                case 'adminlogin'://登录
                    $apipost = new ApiPostPwdLogin($post, true);
                    $output = $apipost->run();
                    break;
                case 'adminauth'://管理员授权
                    $this->userLoginRequired($post);
                    $mgr = new UserManager();
                    $output = $mgr->adminAuth($post);
                    break;
                case 'agentregist'://超管添加用户
                    $apipost = new ApiPostAgentRegist($post);
                    $output = $apipost->run();
                    break;
                case 'importadmins'://admin数据导入
                    $file = $_FILES['file'];
                    $mgr = new ExeclManage();
                    $output = $mgr->importAdmin($file);
                    break;
                case 'importagents'://agent数据导入
                    $file = $_FILES['file'];
                    $mgr = new ExeclManage();
                    $output = $mgr->importAgents($file);
                    break;
                case 'addproject'://创建项目
                    $apipost = new ApiPostCreateProject($post);
                    $output = $apipost->run();
                    break;
                case 'projectpicture'://添加project图片
                    $mgr = new HouseManager();
                    $output = $mgr->addPicture($post);
                    break;
                case 'addhouse'://添加房源
                    $apipost = new ApiPostCrmPost($post);
                    $output = $apipost->run();
                    break;
                case 'importhavehouse':
                    $file = $_FILES['file'];
                    $mgr = new ExeclManage();
                    $output = $mgr->importhavehouse($file);
                    break;
                case 'importwanthouse':
                    $file = $_FILES['file'];
                    $mgr = new ExeclManage();
                    $output = $mgr->importwanthouse($file);
                    break;
                case 'adminmacth':
                    $apipost = new ApiPostAdminMatch($post);
                    $output = $apipost->run();
                    break;
                //微信部分接口
                case 'sendhouse'://发送房源信息
                    $mgr = new WechatManager();
                    $output = $mgr->sendHouse($post);
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
        $post = $_POST;
        $statusCode = 200;
        if (empty($post)) {
            //json参数
            $post = CJSON::decode($this->getPostData());
        }
        try {
            switch ($model) {
                case 'updateadmin':
                    $userMgr = new UserManager();
                    $output = $userMgr->updateUser($id, $post);
                    break;
                case 'updateagent':
                    $userMgr = new UserManager();
                    $output = $userMgr->updateUser($id, $post);
                    break;
                case 'updateproject':
                    $mgr = new HouseManager();
                    $output = $mgr->updateProject($id, $post);
                    break;
                case 'updatehouse':
                    $postMgr = new PostManager();
                    $output = $postMgr->crmUpdatePost($id, $post);
                    break;
                case 'gettask':
                    $admin = $this->userLoginRequired($post);
                    $post['id'] = $id;
                    $post['user_id'] = $admin->id;
                    $apiview = new ApiViewGetTask($post);
                    $output = $apiview->loadApiViewData();
                    break;
                case 'taskfinish':
                    $admin = $this->userLoginRequired($post);
                    $mgr = new HouseManager();
                    $output = $mgr->taskFinish($id, $admin->id);
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
