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

}
