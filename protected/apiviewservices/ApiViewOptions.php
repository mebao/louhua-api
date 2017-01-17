<?php

class ApiViewOptions extends EApiViewService {

    public function __construct() {
        parent::__construct();
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
        $this->loadProject();
        $this->loadOptionsExposure();
        $this->loadOptionsUnitType();
    }

    private function loadProject() {
        $list = array();
        $models = Project::model()->loadAllByTime();
        if (arrayNotEmpty($models)) {
            foreach ($models as $m) {
                $list[$m->id] = $m->name;
            }
        }
        $this->results->optionsProject = $list;
    }

    private function loadOptionsExposure() {
        $this->results->optionsExposure = StatCode::loadOptionsExposure();
    }

    private function loadOptionsUnitType() {
        $this->results->optionsUnitType = StatCode::loadOptionsUnitType();
    }

}
