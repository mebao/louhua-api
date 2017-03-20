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
class ApiViewSearchHouse extends EApiViewService {

    private $searchInputs;
    private $getCount = false;
    private $pageSize = 10;
    private $modelSearch;
    private $list;
    private $count = 0;

    public function __construct($searchInputs) {
        parent::__construct();
        $this->list = array();
        //page 当前页码
        $this->searchInputs = $searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->modelSearch = new HouseSearch($this->searchInputs, array('project'));
    }

    protected function loadData() {
        $this->loadModels();
        if ($this->getCount) {
            $this->count = $this->modelSearch->count();
        }
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = new stdClass();
            $this->output->status = self::RESPONSE_OK;
            $this->output->errorCode = 0;
            $this->output->dataNum = $this->count;
            $this->output->errorMsg = 'success';
            $this->output->results = $this->results;
        }
    }

    private function loadModels() {
        $models = $this->modelSearch->search();
        if (arrayNotEmpty($models)) {
            $this->setModel($models);
        }
        $this->results->list = $this->list;
    }

    private function setModel($models) {
        $postType = 'have';
        if (isset($this->searchInputs['post_type']) && $this->searchInputs['post_type'] == 'want') {
            $postType = 'want';
        }
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
            $std->house_status = $v->house_status;
            $std->projectMessage = "";
            if (isset($v->project)) {
                $std->projectMessage = $v->project->message;
            }
            $std->postType = $postType;
            $this->list[] = $std;
        }
    }

}
