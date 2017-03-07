<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewSearchHave
 *
 * @author ShuMing
 */
class ApiViewMyTask extends EApiViewService {

    private $list;
    private $adminId;

    public function __construct($adminId) {
        parent::__construct();
        $this->adminId = $adminId;
        $this->list = array();
    }

    protected function loadData() {
        $this->loadModels();
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

    private function loadModels() {
        $models = HousingResources::model()->loadAllByAdminId($this->adminId);
        if (arrayNotEmpty($models)) {
            $this->setModel($models);
        }
        $this->results->list = $this->list;
    }

    private function setModel($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->projectId = $v->project_id;
            $std->projectName = $v->project_name;
            $std->haveId = $v->have_id;
            $std->userHaveId = $v->user_have_id;
            $std->userHaveName = $v->user_have_name;
            $std->wantId = $v->want_id;
            $std->userWantId = $v->user_want_id;
            $std->userWantName = $v->user_want_name;
            $std->unitType = $v->unit_type;
            $std->floorLow = $v->expect_floor_low;
            $std->floorHigh = $v->expect_floor_high;
            $std->price = $v->price;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->action = $v->action;
            $std->unitStatus = $v->unit_status;
            $std->time = $v->getDateCreated('Y-m-d H:i:s');
            $postType = 'want';
            if (strIsEmpty($v->want_id)) {
                $postType = 'have';
            }
            $std->postType = $postType;
            $this->list[] = $std;
        }
    }

}
