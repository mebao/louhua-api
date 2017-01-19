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
            //未登陆过
            $isAccount = 1;
            if (isset($user) === false) {
                $user = new User();
                $user->wx_userid = $wxuserid;
                $user->user_role = StatCode::ROLE_USER;
                $user->username = $wxuserid . 'wu' . substr(time(), 2);
                $user->password_raw = rand(10000, 1000000);
                $user->createNewModel();
                $user->save();
            } else {
                if (strIsEmpty($user->wechat_id) === false) {
                    $isAccount = 0;
                }
            }
            $authManager = new AuthManager();
            $authTokenUser = $authManager->doTokenUserAutoLogin($user);
            $std->status = 'ok';
            $std->errorCode = 200;
            $std->errorMsg = 'success';
            $userarray = array('username' => $user->username, 'token' => $authTokenUser->token,
                'role' => $user->user_role, 'isAccount' => $isAccount);
            $std->results = array('userinfo' => $userarray);
        }
        return $std;
    }

}
