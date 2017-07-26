<?php
namespace App\Http\Service\Common;

use App\Http\Service\BaseService;

class HttpService extends BaseService{

    /**
     * 初始化cURL
     * @param array $param 参数：['url' => '访问的地址', 'data' => 'POST提交的数据', 'proxy' => ['ip' => '代理的IP地址', 'port' => '代理的端口'] ]
     * @return mixed
     */
    public function init(array $param = array()) {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $param['url']); // 要访问的地址

        if (!empty($param['data'])) {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param['data']); // Post提交的数据包
        }

        self::setSetopt($curl, $param['url']);

        //是否走代理
        if (!empty($param['proxy']['ip']) && !empty($param['proxy']['port'])) {
            curl_setopt($curl, CURLOPT_PROXY, $param['proxy']['ip']);
            curl_setopt($curl, CURLOPT_PROXYPORT, $param['proxy']['port']);
        }


        $return = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话

        return $return;
    }

    /**
     * 多线程
     */
    public function multi(array $param = array()) {
        $mh = curl_multi_init();
        foreach ($param['url'] as $key => $value) {
            $conn[$key] = curl_init($value);
            self::setSetopt($conn[$key], $value);

            if (!empty($param['proxy'][$key]['ip']) && !empty($param['proxy'][$key]['port'])) {
                curl_setopt($conn[$key], CURLOPT_HTTPHEADER, array('HTTP_VIA:',"X-FORWARDED-FOR:{$param['proxy'][$key]['ip']}", "CLIENT-IP:{$param['proxy'][$key]['ip']}"));
                curl_setopt($conn[$key], CURLOPT_PROXY, $param['proxy'][$key]['ip']);
                curl_setopt($conn[$key], CURLOPT_PROXYPORT, $param['proxy'][$key]['port']);
            }
            curl_multi_add_handle($mh, $conn[$key]);
        }

        $active = false;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);


        while ($active && $mrc == CURLM_OK) {
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        $res = array();
        foreach ($param['url'] as $key => $value) {
            $res[$key] = curl_multi_getcontent($conn[$key]);
            curl_close($conn[$key]);
            curl_multi_remove_handle($mh, $conn[$key]);//释放资源
        }
        return $res;


    }

    /**
     * 设置curl信息
     * @param $curl curl的对象
     * @param $url 请求地址，用来单独区分开cookie名称
     */
    private function setSetopt($curl, $url) {
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在

//        curl_setopt($curl, CURLOPT_REFERER, 'http://www.amazon.com/s/ref=nb_sb_ss_i_2_6?url=search-alias%3Daps&field-keywords=fix+mobile&sprefix=fix+mobile%2Caps%2C355&rh=i%3Aaps%2Ck%3Afix+mobile');
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36'); // 模拟用户使用的浏览器

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环

        $name = md5($url);
        if(!is_dir('/tmp/crawler/cookie')){
            mkdir('/tmp/crawler/cookie');
        }
        curl_setopt($curl, CURLOPT_COOKIEJAR, "/tmp/crawler/cookie/{$name}_cookie.txt"); //保存
        curl_setopt($curl, CURLOPT_COOKIEFILE, "/tmp/crawler/cookie/{$name}cookie.txt"); //读取
    }

}
?>
