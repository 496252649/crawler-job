<?php

namespace App\Http\Service;

class tuniuService extends BaseService implements Criterion
{
    /**
     * 匹配目标产品标题
     */
    public static function matchTitle($html) {
        $pattern = '/<h1 class="tp_name">(.*)<\/h1>/';
        preg_match($pattern, $html, $matches);
        if (empty($matches['1'])) {
            return 'Failed to match title';
        } else {
            return trim(strip_tags($matches['1']));
        }
    }

    /**
     * 获取单价格
     * @param $html
     */
    public static function matchOnePrice($html) {
        $pattern = '/<span class="tpp_pri">(.*)<\/span>/';
        preg_match($pattern, $html, $matches);
        if (empty($matches)) {
            return '-1';
        }

        if (is_numeric($matches['1'])) {
            return $matches['1'];
        } elseif (!empty($matches['1'])) {
            return trim(strip_tags($matches['1']));
        }
    }

    /**
     * 获取多价格
     * @param $html
     * @return bool|string
     */
    public static function matchMultiPrice($html) {
        //先匹配多价格的table
        $pattern = '/<li id="tier_list">[\s\S]*?<\/li>/';
        preg_match($pattern, $html, $li);
        if (empty($li['0'])) {
            return '';
        }
        //价格
        $pattern = '/<span class="price">.*<\/span>/';
        preg_match($pattern, $li['0'], $priceSpan);
        if (empty($priceSpan['0'])) {
            return '';
        }
        $pattern = '/\d+\.\d{0,2}/';
        preg_match($pattern, $priceSpan['0'], $price);

        //匹配数量
        $pattern = '/\d+/';
        preg_match($pattern, $li['0'], $num);

        return json_encode([$num[0] => $price[0]]);
    }

    public static function matchStock($html) {
        // TODO: Implement matchStock() method.
    }


}