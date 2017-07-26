<?php

namespace App\Console\Commands;

use App\Http\Service\tuniuService;
use Illuminate\Console\Command;
use App\Http\Service\Common\HttpService;
use DB;

class Crawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:crawler {--site=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = $this->option("site");
        if (empty($site) ) {
            echo "Nothing";exit;
        }
        $siteClass = "App\\Http\\Service\\".$site."Service";
        $list = $this->getSite($site);
        if (empty($list)) {
            echo "Not-List";exit;
        }
        // 获取代理ip
        $proxy = [
            /*['proxy_id'=>1,'proxy_ip'=>'110.72.32.146','proxy_port'=>8123],
            ['proxy_id'=>2,'proxy_ip'=>'182.88.82.59','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],
            ['proxy_id'=>3,'proxy_ip'=>'171.38.212.72','proxy_port'=>8123],*/
        ];
        //获取数据
        $param = [];
        $i = 0;
        $p = 0;
        foreach ($list as $key => $value) {
            $value = (array)$value;
            // 每10条执行一次写入
            if ($key % 10 == 0 && $key > 0) {
                $param[$i]['url'][$key] = $value["product_url"];
                $param[$i]['proxy'][$key] = isset($proxy[$p]['proxy_id']) ? ['id' => $proxy[$p]['proxy_id'], 'ip' => $proxy[$p]['proxy_ip'] , 'port' => $proxy[$p]['proxy_port']] : array();
                $param[$i]['product_id'][$key] = $value['product_id'];
                $curl = HttpService::getInstance()->multi([
                    'url' => $param[$i]['url'],
                    'proxy' => $param[$i]['proxy']
                ]);

                foreach ($curl as $ck => $cv) {
                    //var_dump($siteClass::getInstance()->matchMultiPrice($cv));exit;
                    if($siteClass::getInstance()->matchOnePrice($cv) == '-1'){
                        //$this->db('proxy')->where('proxy_id = :proxy_id')->update(['proxy_use' => '1', 'noset' => ['proxy_id' => $param[$i]['proxy'][$ck]['id'] ]]);
                        continue;
                    }

                    $stock = $siteClass::getInstance()->matchStock($cv);
                    $this->recordPrice(
                        $param[$i]['product_id'][$ck],
                        $site,
                        $siteClass::getInstance()->matchOnePrice($cv),
                        $siteClass::getInstance()->matchTitle($cv),
                        $siteClass::getInstance()->matchMultiPrice($cv),
                        empty($stock) ? '' : $stock
                    );
                }
                $p = 0;
                $i++;
            } else {
                $param[$i]['url'][$key] = $value["product_url"];
                $param[$i]['proxy'][$key] = isset($proxy[$p]['proxy_id']) ? ['id' => $proxy[$p]['proxy_id'], 'ip' => $proxy[$p]['proxy_ip'] , 'port' => $proxy[$p]['proxy_port']] : array();
                $param[$i]['product_id'][$key] = $value['product_id'];
            }
            $p++;
        }

    }

    /**
     * 依据提交过来的网站名称，查找需要比价的网站信息
     * 程序都是抓取一天过期时间的信息
     * @param $site
     */
    private function getSite($site) {
        $list = DB::table('crawler_url')->where(['product_site'=>$site])->get()->toArray();
        return $list;

    }

    /**
     * 记录产品价格
     * @param $pid 产品ID
     * @param $site 抓取的网站名称
     * @param $onePrice 得到的单价格
     * @param $title 得到的产品标题
     * @param $multiPrice 得到的多价格
     */
    private function recordPrice($pid, $site, $onePrice, $title, $multiPrice, $stock) {
        //写入信息
        DB::table('crawler_info')->insertGetId(['product_id'=>$pid,'product_site'=>$site,'product_price'=>$onePrice,'product_name'=>$title,'created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")]);
        //更新是否执行过 crawler_url表

    }
}
