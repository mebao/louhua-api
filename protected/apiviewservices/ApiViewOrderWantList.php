<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewOrderHaveList
 *
 * @author ShuMing
 */
class ApiViewOrderWantList extends EApiViewService {

    private $postList;
    private $userId;
    private $order;
    private $type;

    public function __construct($values) {
        parent::__construct();
        $this->userId = $userId;
        if (isset($values['order']) === false) {
            $order = 'id';
        } else {
            $order = $values['order'];
        }
        if (isset($values['type']) === false) {
            $type = 'desc';
        } else {
            $type = $values['type'];
        }
        $this->order = $order;
        $this->type = $type;
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
        $this->loadUserhave();
    }

    private function loadUserhave() {
        $options = array("order" => "t." . $this->order . " " . $this->type);
        $with = array("user");
        $models = UserWant::model()->getAll($with, $options);
        if (arrayNotEmpty($models)) {
            $this->setUserWant($models);
        }
        $this->results->postList = $this->postList;
    }

    private function setUserWant($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->expect_floor_low . "-" . $v->expect_floor_high;
            $std->price = $v->price;
            $user = $v->user;
            $std->avatarUrl = $user->avatar_url;
            $this->postList[] = $std;
        }
    }

}
