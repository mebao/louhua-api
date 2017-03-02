<?php

/**
 * Created by PhpStorm.
 * User: myzd-changbiyu
 * Date: 2017/1/10
 * Time: 11:11
 */
require_once dirname(__FILE__) . '/../sdk/phpexcel/PHPExcel.php';
require_once dirname(__FILE__) . '/../sdk/phpexcel/PHPExcel/IOFactory.php';

class ExeclManage {

    /**
     * 存储上传的文件
     */
    public function saveFile($file) {
        $filename = explode(".", $file['name']); //把上传的文件名以“.”好为准做一个数组。
        $filename[0] = time(); //文件名替换
        $name = implode(".", $filename); //上传后的文件名
        $fileurl = realpath(dirname(__FILE__) . '/../../upload') . DIRECTORY_SEPARATOR . $name;
        //文件存储+重命名
        $result = move_uploaded_file($file['tmp_name'], $fileurl);
        if ($result) {
            return $fileurl;
        } else {
            return "";
        }
    }

    /* 导出excel函数 */

    public function exportadmins($data, $name = 'adminlist') {
        if (arrayNotEmpty($data) === false) {
            exit;
        }
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new PHPExcel();
        /* 以下是一些设置 ，什么作者  标题啊之类的 */
        $objPHPExcel->getProperties()->setCreator("xlh")
                ->setLastModifiedBy("xlh")
                ->setTitle("DATA EXCEL EXPROT")
                ->setSubject("DATA EXCEL EXPROT")
                ->setDescription("DATA")
                ->setKeywords("excel")
                ->setCategory("result file");
        //标题
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'User ID')
                ->setCellValue('B1', 'Wechat Name')
                ->setCellValue('C1', 'Wechat ID')
                ->setCellValue('D1', 'Admin Name')
                ->setCellValue('E1', 'Cell')
                ->setCellValue('F1', 'Brokerage Name')
                ->setCellValue('G1', 'Office Telephone');

        /* 以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改 */
        foreach ($data as $k => $v) {
            $num = $k + 2;
            $objPHPExcel->setActiveSheetIndex(0)
                    //Excel的第A列，uid是你查出数组的键值，下面以此类推
                    ->setCellValue('A' . $num, $v->userId)
                    ->setCellValue('B' . $num, $v->wechatName)
                    ->setCellValue('C' . $num, $v->wechatId)
                    ->setCellValue('D' . $num, $v->username)
                    ->setCellValue('E' . $num, $v->cell)
                    ->setCellValue('F' . $num, $v->brokerageName)
                    ->setCellValue('G' . $num, $v->officeTelephone);
        }
        $objPHPExcel->getActiveSheet()->setTitle('Admins');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getColumnDimension()->setWidth(30);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function exportagents($data, $name = 'agentlist') {
        if (arrayNotEmpty($data) === false) {
            exit;
        }
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new PHPExcel();
        /* 以下是一些设置 ，什么作者  标题啊之类的 */
        $objPHPExcel->getProperties()->setCreator("xlh")
                ->setLastModifiedBy("xlh")
                ->setTitle("DATA EXCEL EXPROT")
                ->setSubject("DATA EXCEL EXPROT")
                ->setDescription("DATA")
                ->setKeywords("excel")
                ->setCategory("result file");
        //标题
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'User ID')
                ->setCellValue('B1', 'Wechat Name')
                ->setCellValue('C1', 'Wechat ID')
                ->setCellValue('D1', 'Email')
                ->setCellValue('E1', 'Role')
                ->setCellValue('F1', 'Cell')
                ->setCellValue('G1', 'Brokerage Name')
                ->setCellValue('H1', 'Office Telephone');

        /* 以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改 */
        foreach ($data as $k => $v) {
            $num = $k + 2;
            $objPHPExcel->setActiveSheetIndex(0)
                    //Excel的第A列，uid是你查出数组的键值，下面以此类推
                    ->setCellValue('A' . $num, $v->userId)
                    ->setCellValue('B' . $num, $v->wechatName)
                    ->setCellValue('C' . $num, $v->wechatId)
                    ->setCellValue('D' . $num, $v->username)
                    ->setCellValue('E' . $num, $v->roleText)
                    ->setCellValue('F' . $num, $v->cell)
                    ->setCellValue('G' . $num, $v->brokerageName)
                    ->setCellValue('H' . $num, $v->officeTelephone);
        }
        $objPHPExcel->getActiveSheet()->setTitle('Agents');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function exporthouse($data, $name = 'housedata') {
        if (arrayNotEmpty($data) === false) {
            exit;
        }
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new PHPExcel();
        /* 以下是一些设置 ，什么作者  标题啊之类的 */
        $objPHPExcel->getProperties()->setCreator("xlh")
                ->setLastModifiedBy("xlh")
                ->setTitle("DATA EXCEL EXPROT")
                ->setSubject("DATA EXCEL EXPROT")
                ->setDescription("DATA")
                ->setKeywords("excel")
                ->setCategory("result file");
        //标题
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'Project Name')
                ->setCellValue('C1', 'Have Agent')
                ->setCellValue('D1', 'Want Agent')
                ->setCellValue('E1', 'Unit Type')
                ->setCellValue('F1', 'Floor Low')
                ->setCellValue('G1', 'Floor High')
                ->setCellValue('H1', 'Price')
                ->setCellValue('I1', 'Exposure')
                ->setCellValue('J1', 'Action')
                ->setCellValue('K1', 'Uint Status')
                ->setCellValue('L1', 'Time')
                ->setCellValue('M1', 'Coop')
                ->setCellValue('N1', 'Post Type');

        /* 以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改 */
        foreach ($data as $k => $v) {
            $num = $k + 2;
            $objPHPExcel->setActiveSheetIndex(0)
                    //Excel的第A列，uid是你查出数组的键值，下面以此类推
                    ->setCellValue('A' . $num, $v->id)
                    ->setCellValue('B' . $num, $v->projectName)
                    ->setCellValue('C' . $num, $v->userHaveName)
                    ->setCellValue('D' . $num, $v->userWantName)
                    ->setCellValue('E' . $num, $v->unitType)
                    ->setCellValue('F' . $num, $v->floorLow)
                    ->setCellValue('G' . $num, $v->floorHigh)
                    ->setCellValue('H' . $num, $v->price)
                    ->setCellValue('I' . $num, $v->exposure)
                    ->setCellValue('J' . $num, $v->action)
                    ->setCellValue('K' . $num, $v->unitStatus)
                    ->setCellValue('L' . $num, $v->time)
                    ->setCellValue('M' . $num, $v->coop)
                    ->setCellValue('N' . $num, $v->postType);
        }
        $objPHPExcel->getActiveSheet()->setTitle('House');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function exportTemplet($name, $data) {
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new PHPExcel();
        /* 以下是一些设置 ，什么作者  标题啊之类的 */
        $objPHPExcel->getProperties()->setCreator("xlh")
                ->setLastModifiedBy("xlh")
                ->setTitle("DATA EXCEL EXPROT")
                ->setSubject("DATA EXCEL EXPROT")
                ->setDescription("DATA")
                ->setKeywords("excel")
                ->setCategory("result file");
        //标题
        $title = 65;
        foreach ($data as $key => $value) {
            $v = chr($title) . '1';
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($v, $key);
            $title++;
        }
        $title = 65;
        foreach ($data as $key => $value) {
            $v = chr($title) . '2';
            $input = 'input';
            if ($key === 'Role') {
                $input = 'Bay Street Agent/Other Brokerage Agent';
            }
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($v, $input);
            $title++;
        }
        $objPHPExcel->getActiveSheet()->setTitle($name);
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function read($filename, $encode = 'utf-8') {
        $objReader = IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] = (string) $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }

    public function insertAdmin($excelData) {
        $count = count($excelData);
        $status = true;
        for ($i = 2; $i <= $count; $i++) {
            $model = $excelData[$i];
            if (count($model) !== 6) {
                $status = false;
                break;
            }
            $user = new User();
            $user->username = trim($model[0]);
            $user->real_name = trim($model[0]);
            $user->wechat_id = trim($model[1]);
            $user->wechat_name = trim($model[2]);
            $user->cell = trim($model[3]);
            $user->brokerage_name = trim($model[4]);
            $user->office_telephone = trim($model[5]);
            $user->user_role = StatCode::ROLE_ADMIN;
            $user->date_verified = date('Y-m-d H:i:s');
            $user->AddTemple();
            if ($user->save() === false) {
                $status = false;
                break;
            }
        }
        return $status;
    }

    public function insertAgent($excelData) {
        $count = count($excelData);
        $status = true;
        for ($i = 2; $i <= $count; $i++) {
            $model = $excelData[$i];
            if (count($model) !== 9) {
                $status = false;
                break;
            }
            $user = new User();
            $user->username = trim($model[0]);
            $user->real_name = trim($model[1]);
            $user->wechat_id = trim($model[2]);
            $user->wechat_name = trim($model[3]);
            $user->cell = trim($model[4]);
            $user->brokerage_name = trim($model[5]);
            $user->office_telephone = trim($model[6]);
            $role = $model[7];
            if (strIsEmpty($role, true) === false && stripos($role, 'other')) {
                $role = StatCode::ROLE_OTHER;
            } else {
                $role = StatCode::ROLE_USER;
            }
            $user->user_role = $role;
            $user->wx_userid = trim($model[8]);
            $user->date_verified = date('Y-m-d H:i:s');
            $user->AddTemple();
            if ($user->save() === false) {
                $status = false;
                break;
            }
        }
        return $status;
    }

    public function importAdmin($file) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'import faild!';
        $trans = Yii::app()->db->beginTransaction();
        try {
            $url = $this->saveFile($file);
            if (strIsEmpty($url) === false) {
                $excelData = $this->read($url);
                if ($this->insertAdmin($excelData)) {
                    $std->status = 'ok';
                    $std->errorCode = 200;
                    $std->errorMsg = 'success';
                    $trans->commit();
                } else {
                    throw new Exception("db save failed");
                }
            } else {
                throw new Exception("file save failed");
            }
        } catch (Exception $ex) {
            $trans->rollback();
            Yii::log($ex->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
        }
        return $std;
    }

    public function importAgents($file) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'import faild!';
        $trans = Yii::app()->db->beginTransaction();
        try {
            $url = $this->saveFile($file);
            if (strIsEmpty($url) === false) {
                $excelData = $this->read($url);
                if ($this->insertAgent($excelData)) {
                    $std->status = 'ok';
                    $std->errorCode = 200;
                    $std->errorMsg = 'success';
                    $trans->commit();
                } else {
                    $std->errorMsg = 'data format error';
                    throw new Exception("db save failed");
                }
            } else {
                throw new Exception("file save failed");
            }
        } catch (Exception $ex) {
            $trans->rollback();
            Yii::log($ex->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
        }
        return $std;
    }

    private function insertHave($excelData) {
        $count = count($excelData);
        $status = true;
        for ($i = 2; $i <= $count; $i++) {
            $model = $excelData[$i];
            if (count($model) !== 14) {
                $status = false;
                break;
            }
            $have = new UserHave();
            $have->project_id = trim($model[0]);
            $have->project_name = trim($model[1]);
            $have->user_id = trim($model[2]);
            $have->unit_type = trim($model[6]);
            $have->floor_low = trim($model[7]);
            $have->floor_high = trim($model[8]);
            $have->price = trim($model[9]);
            $have->exposure = trim($model[10]);
            $have->coop = trim($model[11]);
            if ($model[13] == 'Matched') {
                $have->is_show = StatCode::POST_NOTSHOW;
            }
            if ($have->save()) {
                $house = new HousingResources();
                $house->have_id = $have->id;
                $house->project_id = $have->project_id;
                $house->project_name = $have->project_name;
                $house->unit_type = $have->unit_type;
                $house->price = $have->price;
                $house->coop = $have->coop;
                $house->exposure = $have->exposure;
                $house->expect_floor_low = $have->floor_low;
                $house->expect_floor_high = $have->floor_high;
                $house->user_have_id = trim($model[2]);
                $house->user_have_name = trim($model[3]);
                $house->user_want_id = trim($model[4]);
                $house->user_want_name = trim($model[5]);
                $house->action = trim($model[12]);
                $house->unit_status = trim($model[13]);
                $house->save();
            } else {
                $status = false;
                break;
            }
        }
        return $status;
    }

    public function importhavehouse($file) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'import faild!';
        $trans = Yii::app()->db->beginTransaction();
        try {
            $url = $this->saveFile($file);
            if (strIsEmpty($url) === false) {
                $excelData = $this->read($url);
                if ($this->insertHave($excelData)) {
                    $std->status = 'ok';
                    $std->errorCode = 200;
                    $std->errorMsg = 'success';
                    $trans->commit();
                } else {
                    $std->errorMsg = 'data format error';
                    throw new Exception("db save failed");
                }
            } else {
                throw new Exception("file save failed");
            }
        } catch (Exception $ex) {
            $trans->rollback();
            Yii::log($ex->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
        }
        return $std;
    }

    private function insertWant($excelData) {
        $count = count($excelData);
        $status = true;
        for ($i = 2; $i <= $count; $i++) {
            $model = $excelData[$i];
            if (count($model) !== 13) {
                $status = false;
                break;
            }
            $want = new UserWant();
            $want->project_id = trim($model[0]);
            $want->project_name = trim($model[1]);
            $want->user_id = trim($model[2]);
            $want->unit_type = trim($model[6]);
            $want->expect_floor_low = trim($model[7]);
            $want->expect_floor_high = trim($model[8]);
            $want->price = trim($model[9]);
            $want->exposure = trim($model[10]);
            if ($model[12] == 'Matched') {
                $want->is_show = StatCode::POST_NOTSHOW;
            }
            if ($want->save()) {
                $house = new HousingResources();
                $house->have_id = $want->id;
                $house->project_id = $want->project_id;
                $house->project_name = $want->project_name;
                $house->unit_type = $want->unit_type;
                $house->price = $want->price;
                $house->coop = $want->coop;
                $house->exposure = $want->exposure;
                $house->expect_floor_low = $want->expect_floor_low;
                $house->expect_floor_high = $want->expect_floor_high;
                $house->user_have_id = trim($model[2]);
                $house->user_have_name = trim($model[3]);
                $house->user_want_id = trim($model[4]);
                $house->user_want_name = trim($model[5]);
                $house->action = trim($model[11]);
                $house->unit_status = trim($model[12]);
                $house->save();
            } else {
                $status = false;
                break;
            }
        }
        return $status;
    }

    public function importwanthouse($file) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'import faild!';
        $trans = Yii::app()->db->beginTransaction();
        try {
            $url = $this->saveFile($file);
            if (strIsEmpty($url) === false) {
                $excelData = $this->read($url);
                if ($this->insertWant($excelData)) {
                    $std->status = 'ok';
                    $std->errorCode = 200;
                    $std->errorMsg = 'success';
                    $trans->commit();
                } else {
                    $std->errorMsg = 'data format error';
                    throw new Exception("db save failed");
                }
            } else {
                throw new Exception("file save failed");
            }
        } catch (Exception $ex) {
            $trans->rollback();
            Yii::log($ex->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
        }
        return $std;
    }

}
