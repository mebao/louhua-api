<?php

/**
 * This is the model class for table "admin_message".
 *
 * The followings are the available columns in table 'admin_message':
 * @property integer $id
 * @property integer $conversation_id
 * @property integer $is_admin
 * @property string $message
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 * @property integer $is_deleted
 */
class AdminMessage extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'admin_message';
    }

    const IS_ADMIN = 1;
    const ISNOT_ADMIN = 0;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('conversation_id, is_admin, is_deleted', 'numerical', 'integerOnly' => true),
            array('message', 'length', 'max' => 2000),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, conversation_id, is_admin, message, date_created, date_updated, date_deleted, is_deleted', 'safe', 'on' => 'search'),
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
            'conversation_id' => 'Conversation',
            'is_admin' => 'Is Admin',
            'message' => 'Message',
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
        $criteria->compare('conversation_id', $this->conversation_id);
        $criteria->compare('is_admin', $this->is_admin);
        $criteria->compare('message', $this->message, true);
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
     * @return AdminMessage the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
