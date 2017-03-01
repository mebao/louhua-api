<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property integer $id
 * @property string $name
 * @property integer $level_limits
 * @property string $unit_type
 * @property string $open_time
 * @property string $close_time
 * @property integer $total_units
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property Advertising Pictures[] $advertising Pictures
 * @property HousingResources[] $housingResources
 * @property UserHave[] $userHaves
 * @property UserWant[] $userWants
 */
class Project extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'project';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('level_limits, total_units', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 20),
            array('unit_type', 'length', 'max' => 50),
            array('message ,open_time, close_time, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, level_limits, unit_type, open_time, close_time, total_units, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'advertisingPictures' => array(self::HAS_MANY, 'AdvertisingPictures', 'project_id'),
            'housingResources' => array(self::HAS_MANY, 'HousingResources', 'project_id'),
            'userHaves' => array(self::HAS_MANY, 'UserHave', 'project_id'),
            'userWants' => array(self::HAS_MANY, 'UserWant', 'project_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => '项目名称',
            'level_limits' => '最高楼层',
            'unit_type' => '房子类型',
            'open_time' => '开盘时间',
            'close_time' => '收盘时间',
            'total_units' => '总单位数量',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('level_limits', $this->level_limits);
        $criteria->compare('unit_type', $this->unit_type, true);
        $criteria->compare('open_time', $this->open_time, true);
        $criteria->compare('close_time', $this->close_time, true);
        $criteria->compare('total_units', $this->total_units);
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
     * @return Project the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function loadAllByTime() {
        $time = date('Y-m-d H:i:s');
        $criteria = new CDbCriteria;
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->addCondition("t.open_time  <= '{$time}'");
        $criteria->addCondition("t.close_time >= '{$time}'");
        $criteria->order = 't.open_time desc';
        return $this->findAll($criteria);
    }

    public function loadDefault() {
        $time = date('Y-m-d H:i:s');
        $criteria = new CDbCriteria;
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->addCondition("t.open_time  <= '{$time}'");
        $criteria->addCondition("t.close_time >= '{$time}'");
        $criteria->with = array('advertisingPictures');
        $criteria->order = 't.open_time desc';
        return $this->findAll($criteria);
    }

}
