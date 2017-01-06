<?php

class StatCode {

    const ROLE_USER = 3;    // 普通用户
    const ROLE_ADMIN = 2; // 管理员
    const ROLE_SUPERADMIN = 1; // 超管
    const ERROR_UNKNOWN = '未知';
    const UNIT_TYPE_STUDIO = "studio";
    const UNIT_TYPE_ONE = "1";
    const UNIT_TYPE_ONEANDONE = "1+1";
    const UNIT_TYPE_TWO = "2";
    const UNIT_TYPE_TWOANDONE = "2+1";
    const UNIT_TYPE_THREE = "3";

    public static function loadOptionsUnitType() {
        return array(
            self::UNIT_TYPE_STUDIO => "studio",
            self::UNIT_TYPE_ONE => "1 Bed",
            self::UNIT_TYPE_ONEANDONE => "1+1 Bed",
            self::UNIT_TYPE_TWO => "2 Bed",
            self::UNIT_TYPE_TWOANDONE => "2+1 Bed",
            self::UNIT_TYPE_THREE => "3 Bed"
        );
    }

    const EXPOSURE_EAST = "East";
    const EXPOSURE_SOUTH = "South";
    const EXPOSURE_WEST = "West";
    const EXPOSURE_NORTH = "North";
    const EXPOSURE_SE = "Southeast";
    const EXPOSURE_NE = "Northeast";
    const EXPOSURE_SW = "Southwest";
    const EXPOSURE_NW = "Northwest";

    public static function loadOptionsExposure() {
        return array(
            "E" => self::EXPOSURE_EAST,
            "S" => self::EXPOSURE_SOUTH,
            "W" => self::EXPOSURE_WEST,
            "N" => self::EXPOSURE_NORTH,
            "SE" => self::EXPOSURE_SE,
            "NE" => self::EXPOSURE_NE,
            "SW" => self::EXPOSURE_SW,
            "NW" => self::EXPOSURE_NW
        );
    }

    const HOUSE_ACTION_PENDING = "Pending";
    const HOUSE_ACTION_PROCESS = "In process";
    const HOUSE_ACTION_DONE = "Deal Done";
    const HOUSE_ACTION_FAILED = "Deal Failed";
    const UNIT_STATUS_PENDING = "Pending";
    const UNIT_STATUS_MATCHED = "Matched";

}
