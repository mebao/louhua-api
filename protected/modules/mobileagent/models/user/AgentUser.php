<?php

/**
 * This is the model class for table "agent_user".
 *
 * The followings are the available columns in table 'agent_user':
 * @property string $id
 * @property string $wechat_id
 * @property string $wechat_name
 * @property string $email
 * @property string $real_name
 * @property string $password
 * @property string $password_raw
 * @property string $cell
 * @property string $brokerage_name
 * @property string $office_telephone
 * @property string $reco_number
 * @property string $avatar_url
 * @property integer $user_role
 * @property string $subscribe
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AgentUser extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'agent_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, date_created', 'required'),
            array('user_role', 'numerical', 'integerOnly' => true),
            array('id', 'length', 'max' => 20),
            array('wechat_id, email, real_name, brokerage_name, avatar_url', 'length', 'max' => 200),
            array('wechat_name, password_raw, cell, office_telephone, reco_number, subscribe', 'length', 'max' => 50),
            array('password', 'length', 'max' => 64),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, wechat_id, wechat_name, email, real_name, password, password_raw, cell, brokerage_name, office_telephone, reco_number, avatar_url, user_role, subscribe, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'wechat_id' => 'Wechat',
            'wechat_name' => 'Wechat Name',
            'email' => 'Email',
            'real_name' => 'Real Name',
            'password' => 'Password',
            'password_raw' => 'Password Raw',
            'cell' => 'Cell',
            'brokerage_name' => 'Brokerage Name',
            'office_telephone' => 'Office Telephone',
            'reco_number' => 'Reco Number',
            'avatar_url' => '头像连接',
            'user_role' => '用户角色',
            'subscribe' => '是否订阅',
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('wechat_id', $this->wechat_id, true);
        $criteria->compare('wechat_name', $this->wechat_name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('real_name', $this->real_name, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('password_raw', $this->password_raw, true);
        $criteria->compare('cell', $this->cell, true);
        $criteria->compare('brokerage_name', $this->brokerage_name, true);
        $criteria->compare('office_telephone', $this->office_telephone, true);
        $criteria->compare('reco_number', $this->reco_number, true);
        $criteria->compare('avatar_url', $this->avatar_url, true);
        $criteria->compare('user_role', $this->user_role);
        $criteria->compare('subscribe', $this->subscribe, true);
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
     * @return AgentUser the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    private function createId() {
        if ($this->isNewRecord) {
            $num = $this->count() + 1;
            $this->id = str_pad($num, 7, "0", STR_PAD_LEFT);
        }
    }

    public function createNewModel() {
        $this->createId();
        $this->createPassword();
    }

    public function checkLoginPassword($passwordInput) {
        return ($this->password === $this->encryptPassword($passwordInput));
    }

    public function changePassword($passwordInput) {
        $this->password_raw = $passwordInput;
        $this->password = $this->encryptPassword($passwordInput);
        return $this->update(array('password', 'password_raw'));
    }

    public function setPassword($v) {
        $this->password = $v;
    }

    private function createPassword() {
        $this->setPassword($this->encryptPassword($this->password_raw));
    }

    public function encryptPassword($password) {
        return ($this->encrypt($password));
    }

    private function encrypt($value) {
        return hash('sha256', $value);
    }

    public function loadByEmailAndRole($email, $role) {
        return $this->getByAttributes(array("email" => $email, "user_role" => $role));
    }

}
