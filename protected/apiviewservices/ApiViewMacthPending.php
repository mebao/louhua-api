<?php

class ApiViewMacthPending extends EApiViewService {

    private $macthList;

    public function __construct() {
        parent::__construct();
        $this->macthList = array();
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
        $models = HousingResources::model()->loadAllPending();
        if (arrayNotEmpty($models)) {
            $this->setUserhave($models);
        }
        $this->results->macthList = $this->macthList;
    }

    private function setUserhave($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            if (strIsEmpty($v->floor_level)) {
                $std->macthType = 'want';
                $std->floor = $v->$v->expect_floor_low . "-" . $v->expect_floor_high;
            } else {
                $std->macthType = 'have';
                $std->floor = $v->floor_level;
            }
            $std->projectId = $v->project_id;
            $std->projectName = $v->project_name;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->price = $v->price;
            $std->haveId = $v->have_id;
            $std->userHaveId = $v->user_have_id;
            $std->userHaveName = $v->user_have_name;
            $std->wantId = $v->want_id;
            $std->userWantId = $v->user_want_id;
            $std->userWantName = $v->user_want_name;
            $this->macthList[] = $std;
        }
    }

}
