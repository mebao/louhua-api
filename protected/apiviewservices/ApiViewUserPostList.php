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
        $this->loadHaveList();
        $this->loadWantList();
        $this->listorder();
    }

    private function loadHaveList() {
        $models = UserHave::model()->loadAllByUserId($this->userId);
        if (arrayNotEmpty($models)) {
            $this->setHave($models);
        }
    }

    private function setHave($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->floor_level;
            $std->price = $v->price;
            $std->time = $v->getDateCreated('Y/m/d H:i a');
            $house = $v->resoures;
            $std->isDelete = 1;
            if (isset($house) && strIsEmpty($house->user_want_id) === false) {
                $std->isDelete = 0;
            }
            $this->postList[] = $std;
        }
    }

    public function loadWantList() {
        $models = UserWant::model()->loadAllByUserId($this->userId);
        if (arrayNotEmpty($models)) {
            $this->setWant($models);
        }
    }

    private function setWant($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->expect_floor_low . "-" . $v->expect_floor_high;
            $std->price = $v->price;
            $std->time = $v->getDateCreated('Y/m/d H:i a');
            $house = $v->resoures;
            $std->isDelete = 1;
            if (isset($house) && strIsEmpty($house->user_have_id) === false) {
                $std->isDelete = 0;
            }
            $this->postList[] = $std;
        }
    }

    private function listorder() {
        $postList = array();
        foreach ($this->postList as $v) {
            $postList[] = $v->time;
        }
        array_multisort($postList, SORT_DESC, $this->postList);
        $this->results->postList = $this->postList;
    }

}
