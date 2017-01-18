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
        $order = "t." . $this->order . " " . $this->type;
        $models = UserHave::model()->loadAllNotMe($this->userId, null, $order);
        if (arrayNotEmpty($models)) {
            $this->setUserhave($models);
        }
        $this->results->postList = $this->postList;
    }

    private function setUserhave($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
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
