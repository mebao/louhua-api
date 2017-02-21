<?php

/**
 * This is the model class for table "user_have".
 *
 * The followings are the available columns in table 'user_have':
 * @property integer $id
 * @property integer $user_id
 * @property integer $project_id
 * @property string $project_name
 * @property string $unit_type
 * @property integer $floor_level
 * @property string $price
 * @property string $exposure
 * @property string $coop
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Project $project
 */
class UserHave extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'user_have';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('project_id, floor_level, user_id', 'numerical', 'integerOnly' => true),
            array('project_name', 'length', 'max' => 20),
            array('unit_type, exposure', 'length', 'max' => 50),
            array('price, coop', 'length', 'max' => 10),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, project_id, project_name, unit_type, floor_level, price, exposure, coop, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    public function beforeValidate() {
        return parent::beforeValidate();
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'resoures' => array(self::HAS_MANY, 'HousingResources', 'have_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'user.id',
            'project_id' => 'project.id',
            'project_name' => '户型(海景房)',
            'unit_type' => '房型(三室一厅)',
            'floor_level' => '几楼',
            'price' => '价格',
            'exposure' => '方向',
            'coop' => '折扣限制率',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('project_id', $this->project_id);
        $criteria->compare('project_name', $this->project_name, true);
        $criteria->compare('unit_type', $this->unit_type, true);
        $criteria->compare('floor_level', $this->floor_level);
        $criteria->compare('price', $this->price, true);
        $criteria->compare('exposure', $this->exposure, true);
        $criteria->compare('coop', $this->coop, true);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('date_updated', $this->date_updated, true);
        $criteria->compare('date_deleted', $this->date_deleted, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserHave the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function loadAllByUserId($values, $with = null) {
        $criteria = new CDbCriteria;
        $criteria->addCondition("t.is_show = 1");
        $criteria->compare("t.user_id ", $values['user_id']);
        if (isset($values['unit_type']) && strIsEmpty($values['unit_type']) === false) {
            $unit = str_replace(" ", "+", $values['unit_type']);
            $criteria->compare('t.unit_type', $unit);
        }
        if (isset($values['exposure']) && strIsEmpty($values['exposure']) === false) {
            $criteria->compare('t.exposure', $values['exposure']);
        }
        if (isset($values['project_id']) && strIsEmpty($values['project_id']) === false) {
            $criteria->compare('t.project_id', $values['project_id']);
        }
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->with = $with;
        $criteria->order = 't.id desc';
        return $this->findAll($criteria);
    }

    public function loadCount($projectId) {
        $criteria = new CDbCriteria;
        $criteria->select = 'SUM(coop) as coop,unit_type,COUNT(1) as id';
        $criteria->compare('is_deleted', self::DB_ISNOT_DELETED);
        $criteria->compare('project_id', $projectId);
        $criteria->group = "unit_type";
        return $this->findAll($criteria);
    }

    public function loadByIdAndUserId($id, $userId, $with = null) {
        return $this->getByAttributes(array("id" => $id, "user_id" => $userId), $with);
    }

    public function loadAllNotMe($values, $with, $order) {
        $criteria = new CDbCriteria;
        $criteria->addCondition("t.is_show = 1");
        //$criteria->addCondition("t.user_id != " . $values['user_id']);
        if (isset($values['unit_type']) && strIsEmpty($values['unit_type']) === false) {
            $unit = str_replace(" ", "+", $values['unit_type']);
            $criteria->compare('t.unit_type', $unit);
        }
        if (isset($values['exposure']) && strIsEmpty($values['exposure']) === false) {
            $criteria->compare('t.exposure', $values['exposure']);
        }
        if (isset($values['project_id']) && strIsEmpty($values['project_id']) === false) {
            $criteria->compare('t.project_id', $values['project_id']);
        }
        if (isset($values['floor_low']) && strIsEmpty($values['floor_low']) === false) {
            $criteria->addCondition("t.floor_level >=" . $values['floor_low']);
        }
        if (isset($values['floor_high']) && strIsEmpty($values['floor_high']) === false) {
            $criteria->addCondition("t.floor_level <=" . $values['floor_high']);
        }
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->with = $with;
        $criteria->order = $order;
        return $this->findAll($criteria);
    }

}
