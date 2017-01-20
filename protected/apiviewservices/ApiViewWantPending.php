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
class ApiViewWantPending extends EApiViewService {

    private $wantList;

    public function __construct() {
        parent::__construct();
        $this->wantList = array();
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
        $this->loadWant();
    }

    private function loadWant() {
        $models = HousingResources::model()->loadAllWantNotHave();
        if (arrayNotEmpty($models)) {
            $this->setWant($models);
        }
        $this->results->wantList = $this->wantList;
    }

    private function setWant($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->wantId = $v->want_id;
            $std->userWantId = $v->user_want_id;
            $std->userWantName = $v->user_want_name;
            $std->projectId = $v->project_id;
            $std->projectName = $v->project_name;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->expect_floor_low . "-" . $v->expect_floor_high;
            $std->price = $v->price;
            $this->wantList[] = $std;
        }
    }

}
