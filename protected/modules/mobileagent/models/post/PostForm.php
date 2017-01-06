<?php

class PostForm extends EFormModel {

    public $post_type;
    public $user_id;
    public $project_id;
    public $project_name;
    public $unit_type;
    public $exposure;
    public $floor_level;
    public $expect_floor_low;
    public $expect_floor_high;
    public $price;
    public $coop;
    public $options_project;
    public $options_unittype;
    public $options_exposure;

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, project_name, unit_type, exposure, price, coop', 'required', 'message' => 'must be input {attribute}'),
            array('project_id, floor_level, expect_floor_low, expect_floor_high', 'numerical', 'integerOnly' => true),
            array('project_name, user_id', 'length', 'max' => 20),
            array('unit_type, exposure', 'length', 'max' => 50),
            array('price, coop, post_type', 'length', 'max' => 10),
        );
    }

    public function initModel() {
        $this->post_type = "want";
        $this->loadOptions();
    }

    public function loadOptions() {
        $this->loadOptionsExposure();
        $this->loadOptionsProject();
        $this->loadOptionsUnittype();
    }

    public function loadOptionsProject() {
        if (is_null($this->options_project)) {
            $this->options_project = CHtml::listData(Project::model()->getAll(), 'id', 'name');
        }
        return $this->options_project;
    }

    public function loadOptionsUnittype() {
        if (is_null($this->options_unittype)) {
            $this->options_unittype = StatCode::loadOptionsUnitType();
        }
        return $this->options_unittype;
    }

    public function loadOptionsExposure() {
        if (is_null($this->options_exposure)) {
            $this->options_exposure = StatCode::loadOptionsExposure();
        }
        return $this->options_exposure;
    }

}
