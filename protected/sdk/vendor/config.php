<?php

class Config {

    const ACCESS_KEY = 'tFXIrVdcf4L0_zBC4F33jbUth5m0qKjMsfSzaSxA';
    const SECRET_KEY = '9Q1h0PX5DRuHBtLuFMYYVU1DfVE_AVzLBUzzTYID';
    const URL_TIME = 3600;

    //根据表名获取的对应的空间名
    public static function getBucketByTableName($tableName = 'childCircle') {
        $list = array("childCircle" => "bcircle", "html" => "meb-cck", "image" => "meb-cck-tn", "music" => "meb-cck-tingting");
        return $list[$tableName];
    }

    //根据空间名获取空间链接
    public static function getDomainByBucket($bucket) {
        $list = array("bcircle" => "http://og03472zu.bkt.clouddn.com",
            "meb-cck" => "http://og03jczpt.bkt.clouddn.com",
            "meb-cck-tn" => "http://og03ontjz.bkt.clouddn.com",
            "meb-cck-tingting" => "http://og1zsd4qt.bkt.clouddn.com",
        );
        return $list[$bucket];
    }

}
