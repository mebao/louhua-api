<?php

class UserManager {

    public function adminAuth($values) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'authorize faild!';
        $admin = User::model()->getById($values['id']);
        if (isset($admin) && strIsEmpty($admin->user_role)) {
            $admin->user_role = StatCode::ROLE_ADMIN;
            if ($admin->update(array('user_role'))) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        }
        return $std;
    }

    public function createUserByWechat($value) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'operation failed!';
        $wxMgr = new WechatManager();
        $data = $wxMgr->wechatAuth($value);
        if (isset($data['UserId'])) {
            $wxuserid = $data['UserId'];
            //验证此用户是否已注册
            $model = User::model()->loadByWxuserid($wxuserid);
            if (isset($model)) {
                $std->errorMsg = 'this wechat has registed!';
            } else {
                //根据微信用户的用户id创建一个用户
                $user = new User();
                $user->wx_userid = $wxuserid;
                $user->user_role = StatCode::ROLE_USER;
                $user->username = $wxuserid . 'wu' . substr(time(), 2);
                $user->password_raw = rand(10000, 1000000);
                $user->createNewModel();
                if ($user->save()) {
                    $std->status = 'ok';
                    $std->errorCode = 200;
                    $std->errorMsg = 'success';
                    $std->results = array("accountId" => str_pad($user->id, 6, "0", STR_PAD_LEFT), 'id' => $user->id);
                }
            }
        }
        return $std;
    }

    public function wechatLogin($value) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'login failed!';
        $wxMgr = new WechatManager();
        $data = $wxMgr->wechatAuth($value);
        if (isset($data['UserId'])) {
            $wxuserid = $data['UserId'];
            $user = user::model()->loadByWxuserid($wxuserid);
            if (isset($user)) {
                $authManager = new AuthManager();
                $authTokenUser = $authManager->doTokenUserAutoLogin($user);
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
                $user = array('username' => $user->username, 'token' => $authTokenUser->token, 'role' => $user->user_role);
                $std->results = array('userinfo' => $user);
            } else {
                $std->errorMsg = 'this wechat not regist!';
            }
        }
        return $std;
    }

}
