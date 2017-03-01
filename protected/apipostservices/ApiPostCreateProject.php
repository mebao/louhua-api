<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiPostCreateProject
 *
 * @author ShuMing
 */
class ApiPostCreateProject extends EApiPostService {

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

        if (isset($this->requestData['name']) === false || strIsEmpty($this->requestData['name'])) {
            $this->errors[] = 'this project name must input!';
        }
        if (isset($this->requestData['level_limits']) === false || strIsEmpty($this->requestData['level_limits'])) {
            $this->errors[] = 'this level limits name must input!';
        }
        if (isset($this->requestData['unit_type']) === false || strIsEmpty($this->requestData['unit_type'])) {
            $this->errors[] = 'this unit type  must input!';
        }
        if (isset($this->requestData['open_time']) === false || strIsEmpty($this->requestData['open_time'])) {
            $this->errors[] = 'this open time name must input!';
        }
        if (isset($this->requestData['close_time']) === false || strIsEmpty($this->requestData['close_time'])) {
            $this->errors[] = 'this close time name must input!';
        }
        if (isset($this->requestData['total_units']) === false || strIsEmpty($this->requestData['total_units'])) {
            $this->errors[] = 'this total units name must input!';
        }
        if (isset($this->requestData['message']) === false || strIsEmpty($this->requestData['message'])) {
            $this->errors[] = 'this message name must input!';
        }
    }

    protected function doPostAction() {
        $model = new Project();
        $model->setAttributes($this->requestData);
        if ($model->save() === false) {
            $this->errors[] = '添加失败!';
            $std = new stdClass();
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = 'create project failed';
            $this->output = $std;
        } else {
            $this->results->projectid = $model->id;
        }
    }

}
