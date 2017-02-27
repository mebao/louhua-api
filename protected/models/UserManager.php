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
        if (isset($value['userid'])) {
            $wxuserid = $value['userid'];
            $user = User::model()->loadByWxuserid($wxuserid);
            //未登陆过 自动创建一个账号
            $isAccount = 1;
            if (isset($user) === false) {
                $user = new User();
                $user->wx_userid = $wxuserid;
                $user->user_role = StatCode::ROLE_USER;
                $user->username = $wxuserid . 'wu' . substr(time(), 2);
                $user->password_raw = rand(10000, 1000000);
                $user->createNewModel();
                $user->date_verified = date('Y-m-d H:i:s');
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
            $userarray = array('accountId' => str_pad($user->id, 6, "0", STR_PAD_LEFT), 'id' => $user->id, 'username' => $user->username,
                'token' => $authTokenUser->token, 'role' => $user->user_role, 'isAccount' => $isAccount);
            $std->results = array('userinfo' => $userarray);
        }
        return $std;
    }

    public function updateUser($id, $values) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'update failed';
        $user = User::model()->getById($id);
        if (isset($user)) {
            if ($user->cell !== $values['cell']) {
                $user->password_raw = $values['cell'];
                $user->password = $user->encryptPassword($values['cell']);
            }
            $user->setAttributes($values);
            if ($user->save()) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        } else {
            $std->errorMsg = 'user is null';
        }
        return $std;
    }

    public function deleteUser($id) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'update failed';
        $user = User::model()->getById($id);
        if (isset($user)) {
            if ($user->delete(false)) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        } else {
            $std->errorMsg = 'user is null';
        }
        return $std;
    }

    public function userOptions() {
        $std = new stdClass();
        $std->status = 'ok';
        $std->errorCode = 200;
        $std->errorMsg = 'success';
        $results = new stdClass();
        $models = User::model()->loadAllByUserRole();
        $results->user = arrayExtractKeyValue($models, 'id', 'real_name');
        $std->results = $results;
        return $std;
    }

}
