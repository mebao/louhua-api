<?php

class ApiViewCountProject extends EApiViewService {

    private $project;
    private $projectId;

    public function __construct($projectId) {
        parent::__construct();
        $this->projectId = $projectId;
        $this->project = array("studio" => 0, "one" => 0, "oneandone" => 0, "two" => 0, "twoandone" => 0, "three" => 0, 'averageCoop' => 0, 'totalPosts' => 0);
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
        $model = Project::model()->getById($this->projectId, array('advertisingPictures'));
        if (isset($model)) {
            $models = UserHave::model()->loadCount($this->projectId);
            if (arrayNotEmpty($models)) {
                $this->setCount($models);
            }
            $this->project['id'] = $model->id;
            $this->project['name'] = $model->name;
            $this->project['levelLimits'] = $model->level_limits;
            $this->project['message'] = $model->message;
            $this->project['openTime'] = $model->open_time;
            $this->project['closeTime'] = $model->close_time;
            $this->project['status'] = 'Past';
            $time = date('Y-m-d H:i:s');
            if ($time >= $model->open_time && $model->close_time >= $time) {
                $this->project['status'] = 'Current';
            }
            $this->project['pictures'] = arrayExtractValue($model->advertisingPictures, 'picture_url');
        }
        $this->results->project = $this->project;
    }

    private function setCount($models) {
        $avg = 0;
        $sum = 0;
        foreach ($models as $v) {
            $sum+= $v->id;
            $avg+=$v->coop;
            if ($v->unit_type == 'studio') {
                $this->project['studio'] = $v->id;
            }
            if ($v->unit_type == '1') {
                $this->project['one'] = $v->id;
            }
            if ($v->unit_type == '1+1') {
                $this->project['oneandone'] = $v->id;
            }
            if ($v->unit_type == '2') {
                $this->project['two'] = $v->id;
            }
            if ($v->unit_type == '2+1') {
                $this->project['twoandone'] = $v->id;
            }
            if ($v->unit_type == '3') {
                $this->project['three'] = $v->id;
            }
        }
        $this->project['totalPosts'] = $sum;
        $coop = 0;
        if ($avg % $sum === 0) {
            $coop = $avg / $sum;
        } else {
            $coop = round(($avg / $sum + $avg % $sum), 2);
        }
        $this->project['averageCoop'] = $coop . '%';
    }

}
