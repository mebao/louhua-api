<?php

class ApiPostCreateConver extends EApiPostService {

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

        if (isset($this->requestData['admin_id']) === false || strIsEmpty($this->requestData['admin_id'])) {
            $this->errors[] = 'this admin id must input!';
        }
        if (isset($this->requestData['admin_name']) === false) {
            $this->errors[] = 'this admin name must input!';
        }
        if (isset($this->requestData['user_id']) === false || strIsEmpty($this->requestData['user_id'])) {
            $this->errors[] = 'this user_id  must input!';
        } else {
            if (Conversation::model()->checkUser($this->requestData['user_id'])) {
                $this->errors[] = 'this user is in another conversation!';
            }
        }
        if (isset($this->requestData['wx_userid']) === false || strIsEmpty($this->requestData['wx_userid'])) {
            $this->errors[] = 'this wxuserid  must input!';
        }
        if (isset($this->requestData['user_name']) === false || strIsEmpty($this->requestData['user_name'])) {
            $this->errors[] = 'this user name  must input!';
        }
        if (isset($this->requestData['channel']) === false || strIsEmpty($this->requestData['channel'])) {
            $this->errors[] = 'this channel must input!';
        }
    }

    protected function doPostAction() {
        $model = new Conversation();
        $model->setAttributes($this->requestData);
        if ($model->save() === false) {
            $this->errors[] = '添加失败!';
            $std = new stdClass();
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = 'create conversation failed';
            $this->output = $std;
        } else {
            $this->results->conversationId = $model->id;
        }
    }

}
