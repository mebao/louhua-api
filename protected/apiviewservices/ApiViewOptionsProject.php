<?php

class ApiViewOptionsProject extends EApiViewService {

    private $currentList;
    private $otherList;

    public function __construct() {
        parent::__construct();
        $this->currentList = array();
        $this->otherList = array();
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
    }

    private function loadProject() {
        $options = array('order' => 't.open_time desc');
        $models = Project::model()->getAll(null, $options);
        if (arrayNotEmpty($models)) {
            $this->setProject($models);
        }
        $this->results->currentList = $this->currentList;
        $this->results->otherList = $this->otherList;
    }

    private function setProject($models) {
        $time = date('Y-m-d H:i:s');
        foreach ($models as $m) {
            if ($time >= $m->open_time && $m->close_time >= $time) {
                $this->currentList[$m->id] = $m->name;
            } else {
                $this->otherList[$m->id] = $m->name;
            }
        }
    }

}
