<?php

/**
 * This is the model class for table "wechat_account".
 *
 * The followings are the available columns in table 'wechat_account':
 * @property integer $id
 * @property string $wx_name
 * @property string $corp_id
 * @property string $corp_secret
 * @property string $access_token
 * @property string $token
 * @property string $encoding_key
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 * @property integer $is_deleted
 */
class WechatAccount extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'wechat_account';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('is_deleted', 'numerical', 'integerOnly' => true),
            array('wx_name, corp_id, token', 'length', 'max' => 50),
            array('corp_secret, access_token, encoding_key', 'length', 'max' => 300),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, wx_name, corp_id, corp_secret, access_token, token, encoding_key, date_created, date_updated, date_deleted, is_deleted', 'safe', 'on' => 'search'),
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
            'wx_name' => '微信号名称',
            'corp_id' => '开发者应用id',
            'corp_secret' => '开发者应用密钥',
            'access_token' => '开发者权限',
            'token' => '消息加解密权限',
            'encoding_key' => '消息加解密密钥',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
            'is_deleted' => 'Is Deleted',
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
        $criteria->compare('wx_name', $this->wx_name, true);
        $criteria->compare('corp_id', $this->corp_id, true);
        $criteria->compare('corp_secret', $this->corp_secret, true);
        $criteria->compare('access_token', $this->access_token, true);
        $criteria->compare('token', $this->token, true);
        $criteria->compare('encoding_key', $this->encoding_key, true);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('date_updated', $this->date_updated, true);
        $criteria->compare('date_deleted', $this->date_deleted, true);
        $criteria->compare('is_deleted', $this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return WechatAccount the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function loadByWxName($name) {
        return $this->getByAttributes(array('wx_name' => $name));
    }

}
