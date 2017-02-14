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

    private $values;
    private $postList;

    public function __construct($values) {
        parent::__construct();
        $this->values = $values;
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
        if (isset($this->values['post_type']) && $this->values['post_type'] === 'want') {
            $this->loadWantList();
        } else {
            $this->loadHaveList();
        }
        //$this->listorder();
    }

    private function loadHaveList() {
        $with = array('resoures');
        $options = array("order" => "t.date_created desc");
        $models = UserHave::model()->loadAllByUserId($this->values, $with, $options);
        if (arrayNotEmpty($models)) {
            $this->setHave($models);
        }
        $this->results->postList = $this->postList;
    }

    private function setHave($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->projectId = $v->project_id;
            $std->projectName = $v->project_name;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->floor_level;
            $std->price = $v->price;
            $std->time = $v->getDateCreated('Y/m/d H:i a');
            $std->postType = 'have';
            $std->isupdate = 0;
            $house = $v->resoures;
            if (count($house) === 1 && strIsEmpty($house[0]->user_want_id)) {
                $std->isupdate = 1;
            }
            $this->postList[] = $std;
        }
    }

    public function loadWantList() {
        $with = array('resoures');
        $models = UserWant::model()->loadAllByUserId($this->values, $with);
        if (arrayNotEmpty($models)) {
            $this->setWant($models);
        }
        $this->results->postList = $this->postList;
    }

    private function setWant($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->projectId = $v->project_id;
            $std->projectName = $v->project_name;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->floor = $v->expect_floor_low . "-" . $v->expect_floor_high;
            $std->price = $v->price;
            $std->time = $v->getDateCreated('Y/m/d H:i a');
            $std->postType = 'want';
            $std->isupdate = 0;
            $house = $v->resoures;
            if (count($house) === 1 && strIsEmpty($house[0]->user_have_id)) {
                $std->isupdate = 1;
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
