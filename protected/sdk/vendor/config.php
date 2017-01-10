<?php

class Config {

    const ACCESS_KEY = 'td6d3pspQec1dUQC_SVUkhyLlqRSYDFqIFHAh44A';
    const SECRET_KEY = '-PiFA4m_Xp96HmuNZlIPGVcwkiisaWqv2YE-3shP';
    const URL_TIME = 3600;

    //根据表名获取的对应的空间名
    public static function getBucketByTableName($tableName) {
        return 'xlhtest';
    }

    //根据空间名获取空间链接
    public static function getDomainByBucket($bucket) {
        return 'http://ojasdr1sv.bkt.clouddn.com';
    }

}
