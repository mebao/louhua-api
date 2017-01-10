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
        $this->results->optionsProject = CHtml::listData(Project::model()->getAll(), 'id', 'name');
    }

    private function loadOptionsExposure() {
        $this->results->optionsExposure = StatCode::loadOptionsExposure();
    }

    private function loadOptionsUnitType() {
        $this->results->optionsUnitType = StatCode::loadOptionsUnitType();
    }

}
