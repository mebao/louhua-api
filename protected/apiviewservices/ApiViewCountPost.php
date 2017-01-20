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

    public function __construct() {
        parent::__construct();
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
        $this->loadCount();
        $this->loadUserhave();
    }

    private function loadCount() {
        $models = UserHave::model()->loadCount();
        if (arrayNotEmpty($models)) {
            $this->setCount($models);
        }
        $this->results->project = $this->project;
    }

    private function setCount($models) {
        $avg = 0;
        $sum = 0;
        foreach ($models as $v) {
            $sum+= $v->id;
            $avg+=$v->coop;
            $this->project[$v->unit_type] = $v->id;
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
            $std->type = $v->unit_type;
            $std->exposure = $v->exposure;
            $std->coop = $v->coop;
            $std->floor = $v->floor_level;
            $std->price = $v->price;
            $this->postList[] = $std;
        }
    }

}
