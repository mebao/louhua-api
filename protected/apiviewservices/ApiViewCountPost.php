<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewCountPost
 *
 * @author ShuMing
 */
class ApiViewCountPost extends EApiViewService {

    private $project;
    private $postList;
    private $projectId;
    private $userId;

    public function __construct($projectId, $userId) {
        parent::__construct();
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->project = array("studio" => 0, "one" => 0, "oneandone" => 0, "two" => 0, "twoandone" => 0, "three" => 0);
        $this->postList = array();
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
        $this->loadUserhave();
    }

    private function loadProject() {
        $model = null;
        if (strIsEmpty($this->projectId)) {
            $projects = Project::model()->loadDefault();
            if (arrayNotEmpty($projects)) {
                $model = $projects[0];
            }
        } else {
            $model = Project::model()->getById($this->projectId, array('advertisingPictures'));
        }

        if (isset($model)) {
            $models = UserHave::model()->loadCount($this->projectId);
            if (arrayNotEmpty($models)) {
                $this->setCount($models);
            }
            $this->project['id'] = $model->id;
            $this->project['name'] = $model->name;
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

    private function loadUserhave() {
        $options = array("order" => "t.id desc", "limit" => 4, "offset" => 0);
        $models = UserHave::model()->getAll(null, $options);
        if (arrayNotEmpty($models)) {
            $this->setUserhave($models);
        }
        $this->results->postList = $this->postList;
    }

    private function setUserhave($models) {
        foreach ($models as $v) {
            $std = new stdClass();
            $std->id = $v->id;
            $std->projectId = $v->project_id;
            $std->projectName = $v->project_name;
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->getFloor();
            $std->price = $v->price;
            $std->postType = 'have';
            $std->canMatch = 1;
            if ($this->userId == $v->user_id) {
                $std->canMatch = 0;
            }
            $this->postList[] = $std;
        }
    }

}
