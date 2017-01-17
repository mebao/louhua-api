<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of authManager
 *
 * @author ShuMing
 */
class AuthManager {

    //验证登录权限
    public function doTokenUserAutoLogin(User $user) {
        $userId = $user->id;
        $username = $user->username;
        $userRole = $user->user_role;
        $authTokenUser = AuthTokenUser::model()->getFirstActiveByUserIdAndRole($userId, $userRole);
        if (isset($authTokenUser) && $authTokenUser->checkExpiry() === false) {
            // token is active but expired, so update it as 'inactive' (is_active=0). 
            $authTokenUser->deActivateToken();
            // unset model.
            $authTokenUser = null;
        }
        if (is_null($authTokenUser)) {
            $userHostIp = Yii::app()->request->userHostAddress;
            $deActivateFlag = false;
            $authTokenUser = $this->createTokenUser($userId, $username, $userRole, $userHostIp, $deActivateFlag);
        }
        return $authTokenUser;
    }

    public function createTokenUser($userId, $username, $userRole, $userHostIp, $deActivateFlag = true) {
        $tokenUser = new AuthTokenUser();
        $tokenUser->createTokenUser($userId, $username, $userRole, $userHostIp);
        if ($deActivateFlag) {
            // deActivate all this user's tokens before creating a new one.
            $tokenUser->deActivateAllUserOldTokens($userId);
        }
        $tokenUser->save();
        return $tokenUser;
    }

    //验证用户的token信息
    public function authenticateUserByToken($username, $token) {
        $authUserIdentity = new AuthUserIdentity($username, $token, AuthUserIdentity::AUTH_TYPE_TOKEN);
        $authUserIdentity->authenticate();
        return $authUserIdentity;
    }

}
