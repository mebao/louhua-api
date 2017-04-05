<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiPostUserPost
 *
 * @author ShuMing
 */
class ApiPostAdminMatch extends EApiPostService {

    public function __construct($requestData) {
        parent::__construct($requestData);
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
        if (isset($this->requestData['id']) === false || strIsEmpty($this->requestData['id'])) {
            $this->errors[] = 'this id must input!';
        }
        if (isset($this->requestData['post_type']) === false || strIsEmpty($this->requestData['post_type'])) {
            $this->errors[] = 'this type must input!';
        }
        if (isset($this->requestData['user_id']) === false || strIsEmpty($this->requestData['user_id'])) {
            $this->errors[] = 'this user id must input!';
        } else {
            $user = User::model()->getById($this->requestData['user_id']);
            if (isset($user)) {
                $this->requestData['real_name'] = $user->real_name;
            } else {
                $this->errors[] = 'this user id error!';
            }
        }
        if (isset($this->requestData['house_id']) === false || strIsEmpty($this->requestData['house_id'])) {
            $this->errors[] = 'this house id must input!';
        }
    }

    protected function doPostAction() {
        $manger = new PostManager();
        $isSuccess = $manger->createMatch($this->requestData);
        if ($isSuccess == 'again' || $isSuccess == 'no') {
            $this->errors[] = 'match again!';
            $std = new stdClass();
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = 'You have duplicated the match requests, please choose other options.';
            $this->output = $std;
        } else {
            $house = HousingResources::model()->getById($this->requestData['house_id']);
            if (isset($house)) {
                $house->admin_id = null;
                $house->update(array('admin_id'));
            }
            $this->results->msg = $isSuccess;
        }
    }

}
