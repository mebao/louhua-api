<?php

class ApiPostForgetPwd extends EApiPostService {

    private $user;
    private $mobile;
    private $verifyCode;
    private $userHostIp;
    private $userManager;
    private $authManager;

    public function __construct($requestData) {
        parent::__construct($requestData);
        $this->userManager = new UserManager();
        $this->authManager = new AuthManager();
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
        if (isset($this->requestData['mobile']) && strIsEmpty($this->requestData['mobile']) === false) {
            $this->mobile = $this->requestData['mobile'];
            $user = User::model()->getByMobile($this->mobile);
            if (is_null($user)) {
                $this->errors[] = '该用户不存在!';
            }
            $this->user = $user;
        } else {
            $this->errors[] = '手机号码未输入!';
        }
        if (isset($this->requestData['verify_code']) && strIsEmpty($this->requestData['verify_code']) === false) {
            $this->verifyCode = $this->requestData['verify_code'];
        } else {
            $this->errors[] = '未输入验证码!';
        }

        if (isset($this->requestData['password']) === false || strIsEmpty($this->requestData['password'])) {
            $this->errors[] = '未输入密码!';
        }
        if (strIsEmpty($this->mobile) === false && strIsEmpty($this->verifyCode) === false) {
            //验证码验证
            $authSmsVerify = $this->authManager->verifyCodeForForgetPwd($this->mobile, $this->verifyCode, $this->userHostIp);
            if ($authSmsVerify->isValid() === false) {
                $this->errors[] = $authSmsVerify->getError('code');
            }
        }
    }

    protected function doPostAction() {
        $this->user->password_raw = $this->requestData['password'];
        $this->user->password = $this->user->encryptPassword($this->requestData['password']);
        if ($this->user->update(array("password_raw", "password")) === false) {
            $this->errors[] = '密码修改失败!';
            $std = new stdClass();
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = '密码修改失败!';
            $this->output = $std;
        }
    }

}
