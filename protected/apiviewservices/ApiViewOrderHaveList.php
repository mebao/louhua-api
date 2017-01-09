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
class ApiViewOrderHaveList extends EApiViewService {

    private $postList;
    private $order;
    private $type;

    public function __construct($values) {
        parent::__construct();
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
        $order = "t." . $this->order . " " . $this->type;
        $with = array("userHave");
        $models = HousingResources::model()->loadAllHaveNotWant($with, $order);
        if (arrayNotEmpty($models)) {
            $this->setUserhave($models);
        }
        $this->results->postList = $this->postList;
    }

    private function setUserhave($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->haveId = $v->have_id;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->floor_level;
            $std->price = $v->price;
            $user = $v->userHave;
            $std->avatarUrl = $user->avatar_url;
            $this->postList[] = $std;
        }
    }

}
