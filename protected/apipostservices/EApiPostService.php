<?php

abstract class EApiPostService {

    const RESPONSE_NO = 'no';
    const RESPONSE_OK = 'ok';   //200
    const RESPONSE_NO_DATA = 'No data'; //400
    const RESPONSE_NOT_FOUND = 'Not found'; //404
    const RESPONSE_VALIDATION_ERRORS = 'Validation errors'; //400
    const RESPONSE_INVALID_PARAMETERS = 'Invalid parameters'; //

    // public $post;  // from $_POST.

    public $requestData;
    public $formModel;  // EFormModel
    //  public $model;      // EActiveRecord
    public $errors;     // array
    protected $results;
    public $output; // used for output data.

    public function __construct($requestData = null, $param2 = null, $param3 = null, $param4 = null) {
        $this->setRequestData($requestData);
        $this->results = new stdClass();
        $this->output = new stdClass();
    }

    public function run() {
        try {
            $this->validateRequestData();
            if ($this->hasErrors()) {
                throw new CHttpException('参数错误');
            }
            $this->doPostAction();
            $this->createOutput();
        } catch (CDbException $cdbex) {
            $this->output->status = self::RESPONSE_NO;
            $this->output->errorCode = '502';
            $this->output->errorMsg = '参数错误';
        } catch (CHttpException $cex) {
            $this->output->status = self::RESPONSE_NO;
            $this->output->errorCode = '400';
            $this->output->errorMsg = $this->getFirstErrors();
        } catch (CException $cex) {
            $this->output->status = self::RESPONSE_NO;
            $this->output->errorCode = '500';
            $this->output->errorMsg = $cex->getMessage();
        }
        return $this->output;
    }

    protected abstract function validateRequestData();


    // protected abstract function parepareData();

    /**
     * @abstract method.     
     */
    protected abstract function doPostAction();

    /**
     * @abstract method     .
     */
    protected abstract function createOutput();

    public function hasErrors() {
        return arrayNotEmpty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getFirstErrors() {
        $ret = array();
        $errorList = $this->getErrors();
        if (emptyArray($errorList) === false) {
            foreach ($errorList as $errors) {
                return $errors;
            }
        }
    }

    public function setRequestData($v) {
        $this->requestData = $v;
    }

}
