<?php
/**
 * Author: vita2333
 * Date: 2019/4/9
 */

namespace AdminLog;


class EnumLogType
{

    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    public static $name;

    public static $textMap = [
        self::TYPE_INSERT => '新增',
        self::TYPE_UPDATE => '修改',
        self::TYPE_DELETE => '删除',
    ];

    public static function getText($value)
    {
        return static::$textMap[$value];
    }

}