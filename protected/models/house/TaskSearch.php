<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HaveSearch
 *
 * @author ShuMing
 */
class TaskSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        $searchInputs['order'] = 't.id DESC';
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new HousingResources();
    }

    public function getQueryFields() {
        return array('id', 'project_id', 'project_name', 'have_id', 'user_have_id', 'user_have_name', 'user_want_id', 'want_id', 'user_want_name', 'unit_type',
            'expect_floor_low', 'expect_floor_high', 'price', 'exposure', 'coop', 'time', 'action', 'unit_status', 'post_type');
    }

    public function addQueryConditions() {
        $this->criteria->compare('t.is_deleted', StatCode::DB_ISNOT_DELETED);
        $this->criteria->addCondition('t.admin_id is null');
        if ($this->hasQueryParams()) {
            if (isset($this->queryParams['post_type'])) {
                if ($this->queryParams['post_type'] == 'want') {
                    $this->criteria->addCondition('t.have_id is null');
                    $this->criteria->addCondition('t.want_id is not null');
                } else {
                    $this->criteria->addCondition('t.have_id is not null');
                    $this->criteria->addCondition('t.want_id is null');
                }
            }
            if (isset($this->queryParams['id'])) {
                $this->criteria->compare('t.id', $this->queryParams['id']);
            }
            if (isset($this->queryParams['project_id'])) {
                $this->criteria->compare('t.project_id', $this->queryParams['project_id']);
            }
            if (isset($this->queryParams['have_id'])) {
                $this->criteria->compare('t.have_id', $this->queryParams['have_id']);
            }
            if (isset($this->queryParams['want_id'])) {
                $this->criteria->compare('t.want_id', $this->queryParams['want_id']);
            }
            if (isset($this->queryParams['project_name'])) {
                $this->criteria->addSearchCondition('t.project_name', $this->queryParams['project_name']);
            }
            if (isset($this->queryParams['user_have_id'])) {
                $this->criteria->compare('t.user_have_id', $this->queryParams['user_have_id']);
            }
            if (isset($this->queryParams['user_have_name'])) {
                $this->criteria->addSearchCondition('t.user_have_name', $this->queryParams['user_have_name']);
            }
            if (isset($this->queryParams['user_want_id'])) {
                $this->criteria->compare('t.user_want_id', $this->queryParams['user_want_id']);
            }
            if (isset($this->queryParams['user_want_name'])) {
                $this->criteria->addSearchCondition('t.user_have_name', $this->queryParams['user_have_name']);
            }
            if (isset($this->queryParams['unit_type'])) {
                $unit = str_replace(" ", "+", $this->queryParams['unit_type']);
                $this->criteria->compare('t.unit_type', $unit);
            }
            if (isset($this->queryParams['expect_floor_low'])) {
                $this->criteria->addCondition('t.expect_floor_low >= ' . $this->queryParams['expect_floor_low']);
            }
            if (isset($this->queryParams['expect_floor_high'])) {
                $this->criteria->addCondition('t.expect_floor_high <= ' . $this->queryParams['expect_floor_high']);
            }
            if (isset($this->queryParams['time'])) {
                $this->criteria->addCondition("t.date_created <= '" . $this->queryParams['time'] . "'");
            }
            if (isset($this->queryParams['price'])) {
                $this->criteria->addSearchCondition('t.price', $this->queryParams['price']);
            }
            if (isset($this->queryParams['exposure'])) {
                $this->criteria->compare('t.exposure', $this->queryParams['exposure']);
            }
            if (isset($this->queryParams['coop'])) {
                $this->criteria->addSearchCondition('t.coop', $this->queryParams['coop']);
            }
            if (isset($this->queryParams['action'])) {
                $this->criteria->compare('t.action', $this->queryParams['action']);
            }
            if (isset($this->queryParams['unit_status'])) {
                $this->criteria->compare('t.unit_status', $this->queryParams['unit_status']);
            }
        }
    }

}
