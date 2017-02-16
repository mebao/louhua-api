<?php

class ApiPostUserRegist extends EApiPostService {

    public function __construct($requestData) {
        parent::__construct($requestData);
    }

    protected function createOutput() {
        if (is_null($this->output) == false) {
            if (arrayNotEmpty($this->errors) === false) {
                $this->output->status = self::RESPONSE_OK;
                $this->output->errorCode = 0;
                $this->output->errorMsg = 'success';
            }
            $this->output->results = $this->results;
        }
    }

    protected function validateRequestData() {
        if (isset($this->requestData['username']) && strIsEmpty($this->requestData['username']) === false) {
            if (User::model()->exists('username=:username', array(':username' => $this->requestData['username']))) {
                $this->errors[] = 'this email has been registered!';
            }
        } else {
            $this->errors[] = 'this email must input!';
        }
        if (isset($this->requestData['password_raw']) === false || strIsEmpty($this->requestData['password_raw'])) {
            $this->errors[] = 'this password must input!!';
        }
        if (isset($this->requestData['wechat_id']) === false || strIsEmpty($this->requestData['wechat_id'])) {
            $this->errors[] = 'this wechat_id must input!!';
        }
        if (isset($this->requestData['real_name']) === false || strIsEmpty($this->requestData['real_name'])) {
            $this->errors[] = 'this full name must input!!';
        }
        if (isset($this->requestData['brokerage_name']) === false || strIsEmpty($this->requestData['brokerage_name'])) {
            $this->errors[] = 'this company must input!!';
        }
        if (isset($this->requestData['cell']) === false || strIsEmpty($this->requestData['cell'])) {
            $this->errors[] = 'this cell phone must input!!';
        }
        if (isset($this->requestData['office_telephone']) === false || strIsEmpty($this->requestData['office_telephone'])) {
            $this->errors[] = 'this office_telephone must input!!';
        }
        if (isset($this->requestData['reco_number']) === false || strIsEmpty($this->requestData['reco_number'])) {
            $this->errors[] = 'this reco_number must input!!';
        }
        if (isset($this->requestData['user_role']) === false || strIsEmpty($this->requestData['user_role'])) {
            $this->requestData['user_role'] = StatCode::ROLE_USER;
        }
    }

    protected function doPostAction() {
        $user = new User();
        $user->setAttributes($this->requestData);
        $user->createNewModel();
        if ($user->save() === false) {
            $this->errors[] = '注册失败!';
            $std = new stdClass();
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = 'user register failed';
            $this->output = $std;
        } else {
            //发送邮件
            $mgr = new EmailManager();
            $mgr->sendEmailVerifyUser($user->username, $this->requestData['url']);
        }
    }

}
