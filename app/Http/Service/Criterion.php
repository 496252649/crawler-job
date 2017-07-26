<?php
namespace App\Http\Service;

interface Criterion{

    /**
     * 声明必须抓取的标题
     * @param $html
     * @return mixed
     */
    public static function matchTitle($html);

    /**
     * 声明必须抓取的单价格
     * @param $html
     * @return mixed
     */
    public static function matchOnePrice($html);

    /**
     * 声明匹配多价格的信息
     * @param $html
     * @return mixed
     */
    public static function matchMultiPrice($html);

    /**
     * 获取库存
     * @param $html
     * @return mixed
     */
    public static function matchStock($html);

}