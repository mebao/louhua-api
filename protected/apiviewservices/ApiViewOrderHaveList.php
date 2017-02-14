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
    private $values;
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
        $this->loadUserhave();
    }

    private function loadUserhave() {
        $order = "t." . $this->order . " " . $this->type;
        $models = UserHave::model()->loadAllNotMe($this->values, null, $order);
        if (arrayNotEmpty($models)) {
            $this->setUserhave($models);
        }
        $this->results->postList = $this->postList;
    }

    private function setUserhave($models) {
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
            $std->postType = 'have';
            $this->postList[] = $std;
        }
    }

}
