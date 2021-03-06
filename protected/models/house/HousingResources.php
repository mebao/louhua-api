<?php

/**
 * This is the model class for table "housing_resources".
 *
 * The followings are the available columns in table 'housing_resources':
 * @property integer $id
 * @property integer $have_id
 * @property integer $user_have_id
 * @property string $user_have_name
 * @property integer $want_id
 * @property integer $user_want_id
 * @property integer $floor_level
 * @property integer $expect_floor_low
 * @property string $user_want_name
 * @property integer $project_id
 * @property string $project_name
 * @property string $unit_type
 * @property integer $expect_floor_high
 * @property string $price
 * @property string $exposure
 * @property string $coop
 * @property string $action
 * @property string $unit_status
 * @property integer $situation
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property User $userHave
 * @property Project $project
 * @property User $userWant
 */
class HousingResources extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'housing_resources';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('floor_level, expect_floor_low, project_id, expect_floor_high, user_have_id, user_want_id, want_id, have_id, house_status, situation', 'numerical', 'integerOnly' => true),
            array('user_have_name, user_want_name, project_name, price, exposure, action, unit_status', 'length', 'max' => 50),
            array('coop', 'length', 'max' => 10),
            array('unit_type', 'length', 'max' => 20),
            array('date_updated, date_deleted, house_status', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_have_id, user_have_name, user_want_id, floor_level, expect_floor_low, user_want_name, project_id, project_name, unit_type, expect_floor_high, price, exposure, coop, action, unit_status, situation, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'userHave' => array(self::BELONGS_TO, 'User', 'user_have_id'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'userWant' => array(self::BELONGS_TO, 'User', 'user_want_id'),
            'postHave' => array(self::BELONGS_TO, 'UserHave', 'have_id'),
            'postWant' => array(self::BELONGS_TO, 'UserWant', 'want_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'have_id' => 'user_have.id',
            'user_have_id' => 'user.id',
            'user_have_name' => '拥有的',
            'want_id' => 'user_want.id',
            'user_want_id' => 'user.id',
            'floor_level' => '楼层',
            'expect_floor_low' => '最高期望楼层',
            'user_want_name' => '想要的',
            'project_id' => 'project.id',
            'project_name' => '户型',
            'unit_type' => '房型',
            'expect_floor_high' => '最低期望楼层',
            'price' => '价格',
            'exposure' => '方向',
            'coop' => '限制折扣率',
            'action' => '处理状态',
            'unit_status' => '单元状态',
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
        $criteria->compare('user_have_id', $this->user_have_id);
        $criteria->compare('user_have_name', $this->user_have_name, true);
        $criteria->compare('user_want_id', $this->user_want_id);
        $criteria->compare('floor_level', $this->floor_level);
        $criteria->compare('expect_floor_low', $this->expect_floor_low);
        $criteria->compare('user_want_name', $this->user_want_name, true);
        $criteria->compare('project_id', $this->project_id);
        $criteria->compare('project_name', $this->project_name, true);
        $criteria->compare('unit_type', $this->unit_type);
        $criteria->compare('expect_floor_high', $this->expect_floor_high);
        $criteria->compare('price', $this->price, true);
        $criteria->compare('exposure', $this->exposure, true);
        $criteria->compare('coop', $this->coop, true);
        $criteria->compare('action', $this->action, true);
        $criteria->compare('unit_status', $this->unit_status, true);
        $criteria->compare('situation', $this->situation, true);
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
     * @return HousingResources the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function loadByIdAndHaveId($id, $haveId) {
        return $this->getByAttributes(array("id" => $id, "have_id" => $haveId));
    }

    public function deleteByHaveId($haveId) {
        return $this->updateAllByAttributes(array("is_deleted" => self::DB_IS_DELETED, "date_deleted" => date('Y-m-d H:i:s')), array('have_id' => $haveId));
    }

    public function deleteByWantId($wantId) {
        return $this->updateAllByAttributes(array("is_deleted" => self::DB_IS_DELETED, "date_deleted" => date('Y-m-d H:i:s')), array('want_id' => $wantId));
    }

    public function loadByHaveIdAndUserWantId($haveId, $userWantId) {
        return $this->getByAttributes(array('have_id' => $haveId, 'user_want_id' => $userWantId));
    }

    public function loadByWantIdAndUserHaveId($wantId, $userHaveId) {
        return $this->getByAttributes(array('want_id' => $wantId, 'user_have_id' => $userHaveId));
    }

    public function loadAllHaveNotWant() {
        $criteria = new CDbCriteria;
        $criteria->compare('t.action', StatCode::HOUSE_ACTION_PENDING);
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->addCondition("t.user_want_id is null");
        $criteria->addCondition("t.have_id is not null");

        return $this->findAll($criteria);
    }

    public function loadAllWantNotHave() {
        $criteria = new CDbCriteria;
        $criteria->compare('t.action', StatCode::HOUSE_ACTION_PENDING);
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->addCondition("t.user_have_id is null");
        $criteria->addCondition("t.want_id is not null");
        return $this->findAll($criteria);
    }

    public function loadAllPending() {
        $criteria = new CDbCriteria;
        $criteria->compare('t.action', StatCode::HOUSE_ACTION_PENDING);
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->addCondition("t.user_have_id is not null");
        $criteria->addCondition("t.user_want_id is not null");
        return $this->findAll($criteria);
    }

    public function loadWantNotUserHave($wantId) {
        $criteria = new CDbCriteria;
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->compare('t.want_id', $wantId);
        $criteria->addCondition("t.user_have_id is null");
        return $this->find($criteria);
    }

    public function loadHaveNotUserWant($haveId) {
        $criteria = new CDbCriteria;
        $criteria->compare('t.is_deleted', self::DB_ISNOT_DELETED);
        $criteria->compare('t.have_id', $haveId);
        $criteria->addCondition("t.user_want_id is null");
        return $this->find($criteria);
    }

    public function loadAllByWantId($wantId) {
        return $this->getAllByAttributes(array('want_id' => $wantId));
    }

    public function loadAllByHaveId($haveId) {
        return $this->getAllByAttributes(array('have_id' => $haveId));
    }

    public function loadAllByAdminId($adminId) {
        return $this->getAllByAttributes(array('admin_id' => $adminId));
    }

    public function getFloor() {
        if (strIsEmpty($this->expect_floor_low) || strIsEmpty($this->expect_floor_high)) {
            return '';
        }
        return $this->expect_floor_low . '-' . $this->expect_floor_high;
    }

}
