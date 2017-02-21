<?php

class ApiPostAgentRegist extends EApiPostService {

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

        if (isset($this->requestData['wechat_id']) === false || strIsEmpty($this->requestData['wechat_id'])) {
            $this->errors[] = 'this wechat_id must input!';
        }
        if (isset($this->requestData['wechat_name']) === false || strIsEmpty($this->requestData['wechat_name'])) {
            $this->errors[] = 'this wechat_name must input!';
        }
        if (isset($this->requestData['real_name']) === false || strIsEmpty($this->requestData['real_name'])) {
            $this->errors[] = 'this trade name must input!';
        }
        if (isset($this->requestData['brokerage_name']) === false || strIsEmpty($this->requestData['brokerage_name'])) {
            $this->errors[] = 'this brokerage_name must input!';
        }
        if (isset($this->requestData['cell']) === false || strIsEmpty($this->requestData['cell'])) {
            $this->errors[] = 'this cell must input!';
        } else {
            $this->requestData['password_raw'] = $this->requestData['cell'];
        }
        if (isset($this->requestData['office_telephone']) === false || strIsEmpty($this->requestData['office_telephone'])) {
            $this->errors[] = 'this office_telephone must input!';
        }
        if (isset($this->requestData['user_role']) === false || strIsEmpty($this->requestData['user_role'])) {
            $this->errors[] = 'choose your role!';
        }
    }

    protected function doPostAction() {
        $user = new User();
        $user->setAttributes($this->requestData);
        $user->createNewModel();
        $user->date_verified = date('Y-m-d H:i:s');
        if ($user->save() === false) {
            $this->errors[] = '注册失败!';
            $std = new stdClass();
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = 'user register failed';
            $this->output = $std;
        } else {
            $this->results->userid = $user->id;
        }
    }

}
