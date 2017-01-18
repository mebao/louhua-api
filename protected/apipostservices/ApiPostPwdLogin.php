<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiPostPwdLogin
 *
 * @author ShuMing
 */
class ApiPostPwdLogin extends EApiPostService {

    private $isadmin;
    private $user;
    private $userManager;
    private $authManager;

    public function __construct($requestData, $isadmin = false) {
        parent::__construct($requestData);
        $this->userManager = new UserManager();
        $this->authManager = new AuthManager();
        $this->isadmin = $isadmin;
    }

    protected function createOutput() {
        if (is_null($this->output) == false) {
            if (arrayNotEmpty($this->errors) === false) {
                // has error.
                $this->output->status = self::RESPONSE_OK;
                $this->output->errorCode = 0;
                $this->output->errorMsg = 'success';
                // pass model data to $output?
            }
            $this->output->results = $this->results;
            //$this->output = $this->results;
        }
    }

    protected function validateRequestData() {
        if (isset($this->requestData['username']) && strIsEmpty($this->requestData['username']) === false) {
            $user = User::model()->loadByUsername($this->requestData['username']);
            if (is_null($user)) {
                $this->errors[] = 'this username is not registered!';
            }
            $this->user = $user;
        } else {
            $this->errors[] = 'this username must input!';
        }

        if (isset($this->requestData['password']) === false || strIsEmpty($this->requestData['password'])) {
            $this->errors[] = 'this password must input!';
        }
    }

    protected function doPostAction() {
        $inputPwd = $this->user->encryptPassword($this->requestData['password']);
        $std = new stdClass();
        if ($this->user->password === $inputPwd) {
            $role = $this->user->user_role;
            if ($this->isadmin && (strIsEmpty($role) || $role == StatCode::ROLE_USER)) {
                $this->errors[] = 'no token login system!';
            }
        } else {
            $this->errors[] = 'password error!';
        }
        if ($this->hasErrors() === false) {
            $authTokenUser = $this->authManager->doTokenUserAutoLogin($this->user);
            $std->username = $this->user->username;
            $std->token = $authTokenUser->token;
            $std->role = $this->user->user_role;
            $this->results->userinfo = $std;
        } else {
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = $this->getFirstErrors();
            $this->output = $std;
        }
    }

}
