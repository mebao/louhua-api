<?php

class ApiPostUserAccount extends EApiPostService {

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
        if (isset($this->requestData['user_id']) === false || strIsEmpty($this->requestData['user_id'])) {
            $this->errors[] = 'this user_id must input!';
        }
        if (isset($this->requestData['wechat_id']) === false || strIsEmpty($this->requestData['wechat_id'])) {
            $this->errors[] = 'this wechat_id must input!';
        }
        if (isset($this->requestData['real_name']) === false || strIsEmpty($this->requestData['real_name'])) {
            $this->errors[] = 'this real_name must input!';
        }
        if (isset($this->requestData['brokerage_name']) === false || strIsEmpty($this->requestData['brokerage_name'])) {
            $this->errors[] = 'this brokerage_name must input!';
        }
        if (isset($this->requestData['cell']) === false || strIsEmpty($this->requestData['cell'])) {
            $this->errors[] = 'this cell must input!';
        }
        if (isset($this->requestData['office_telephone']) === false || strIsEmpty($this->requestData['office_telephone'])) {
            $this->errors[] = 'this office_telephone must input!';
        }
        if (isset($this->requestData['reco_number']) === false || strIsEmpty($this->requestData['reco_number'])) {
            $this->errors[] = 'this reco_number must input!';
        }
    }

    protected function doPostAction() {
        $std = new stdClass();
        $std->status = self::RESPONSE_NO;
        $std->errorCode = 502;
        $std->errorMsg = 'operation failed';
        $user = user::model()->getById($this->requestData['user_id']);
        if (isset($user)) {
            $user->setAttributes($this->requestData);
            if ($user->save() === false) {
                $this->output = $std;
            }
        }
    }

}
