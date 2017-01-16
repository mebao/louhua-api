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
class ApiPostUserWatch extends EApiPostService {

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
    }

    protected function doPostAction() {
        $manger = new PostManager();
        $isSuccess = $manger->createMatch($this->requestData);
        if ($isSuccess === false) {
            $this->errors[] = 'watch faild!';
            $std = new stdClass();
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = 'watch faild!';
            $this->output = $std;
        }
    }

}
