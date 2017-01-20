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
class ApiViewHavePending extends EApiViewService {

    private $haveList;

    public function __construct() {
        parent::__construct();
        $this->haveList = array();
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
        $this->loadhave();
    }

    private function loadhave() {
        $models = HousingResources::model()->loadAllHaveNotWant();
        if (arrayNotEmpty($models)) {
            $this->setHave($models);
        }
        $this->results->haveList = $this->haveList;
    }

    private function setHave($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->haveId = $v->have_id;
            $std->userHaveId = $v->user_have_id;
            $std->userHaveName = $v->user_have_name;
            $std->projectId = $v->project_id;
            $std->projectName = $v->project_name;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->floor_level;
            $std->price = $v->price;
            $this->haveList[] = $std;
        }
    }

}
