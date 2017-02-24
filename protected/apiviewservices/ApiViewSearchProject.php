<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewProjectList
 *
 * @author ShuMing
 */
class ApiViewSearchProject extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of Doctors satisfying the search conditions.
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
        $this->modelSearch = new ProjectSearch($this->searchInputs, array('advertisingPictures'));
    }

    protected function loadData() {
        $this->loadModels();
        if ($this->getCount) {
            $this->count = $this->agentSearch->count();
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
        foreach ($models as $value) {
            $std = new stdClass();
            $std->id = $value->id;
            $std->name = $value->name;
            $std->levelLimits = $value->level_limits;
            $std->unitType = $value->unit_type;
            $std->openTime = $value->open_time;
            $std->closeTime = $value->close_time;
            $std->totalUnits = $value->total_units;
            $std->pictures = arrayExtractKeyValue($value->advertisingPictures, 'id', 'picture_url');
            $this->list[] = $std;
        }
    }

}
