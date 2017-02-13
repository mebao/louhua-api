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
class ApiPostUserPost extends EApiPostService {

    private $postManager;

    public function __construct($requestData) {
        parent::__construct($requestData);
        $this->postManager = new PostManager();
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
        if (isset($this->requestData['post_type']) && strIsEmpty($this->requestData['post_type']) === false) {
            if ($this->requestData['post_type'] == 'want') {
                if (isset($this->requestData['expect_floor_low']) === false || strIsEmpty($this->requestData['expect_floor_low'])) {
                    $this->errors[] = 'this expect floor low must input!';
                }
                if (isset($this->requestData['expect_floor_high']) === false || strIsEmpty($this->requestData['expect_floor_high'])) {
                    $this->errors[] = 'this expect floor high must input!';
                }
            } elseif ($this->requestData['post_type'] == 'have') {
                if (isset($this->requestData['floor_level']) === false || strIsEmpty($this->requestData['floor_level'])) {
                    $this->errors[] = 'this floor level must input!';
                }
            } else {
                $this->errors[] = 'this post_type error!';
            }
        } else {
            $this->errors[] = 'this post_type must input!';
        }

        if (isset($this->requestData['user_id']) === false || strIsEmpty($this->requestData['user_id'])) {
            $this->errors[] = 'this user_id must input!';
        }
        if (isset($this->requestData['project_name']) === false || strIsEmpty($this->requestData['project_name'])) {
            $this->errors[] = 'this project_name must input!';
        }
        if (isset($this->requestData['unit_type']) === false || strIsEmpty($this->requestData['unit_type'])) {
            $this->errors[] = 'this unit_type must input!';
        }
        if (isset($this->requestData['exposure']) === false || strIsEmpty($this->requestData['exposure'])) {
            $this->errors[] = 'this exposure must input!';
        }
        if (isset($this->requestData['price']) === false || strIsEmpty($this->requestData['price'])) {
            $this->errors[] = 'this price must input!';
        }
        if (isset($this->requestData['coop']) === false || strIsEmpty($this->requestData['coop'])) {
            $this->errors[] = 'this coop must input!';
        }
    }

    protected function doPostAction() {
        $id = $this->postManager->createPost($this->requestData);
        if ($id === 0) {
            $this->errors[] = "";
            $std = new stdClass();
            $std->status = self::RESPONSE_NO;
            $std->errorCode = 502;
            $std->errorMsg = 'post failed!';
            $this->output = $std;
        } else {
            $this->results->id = $id;
        }
    }

}
