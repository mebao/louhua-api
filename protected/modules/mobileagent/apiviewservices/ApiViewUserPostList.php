<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewUserPostList
 *
 * @author ShuMing
 */
class ApiViewUserPostList extends EApiViewService {

    private $userId;
    private $postList;

    public function __construct($userId) {
        parent::__construct();
        $this->userId = $userId;
        $this->postList = array();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = new stdClass();
            $this->output->status = self::RESPONSE_OK;
            $this->output->errorCode = 0;
            $this->output->errorMsg = 'success';
            $this->output->results = $this->results;
        }
    }

    protected function loadData() {
        $this->loadPostList();
    }

    private function loadPostList() {
        $models = UserHave::model()->loadAllByUserId($this->userId);
        if (arrayNotEmpty($models)) {
            $this->setPost($models);
        }
        $this->results->postList = $this->postList;
    }

    private function setPost($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->floor_level;
            $std->price = $v->price;
            $std->time = $v->getDateCreated('Y/m/d H:i a');
            $this->postList[] = $std;
        }
    }

}
