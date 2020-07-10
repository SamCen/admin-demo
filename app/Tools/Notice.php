<?php
/**
 * Author sam
 * DateTime 2019-09-26 18:25
 * Description:
 */

namespace App\Tools;


use App\Exceptions\GeneralException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Notice
{
    private $wx_uri;
    private $wx_url;

    /**
     * Notice constructor.
     * @param array $config
     * @throws GeneralException
     */
    public function __construct(array $config)
    {
        if(empty($config['wx_url']) || empty($config['wx_uri'])){
            throw new GeneralException('notice config error');
        }
        $this->wx_uri = $config['wx_uri'];
        $this->wx_url = $config['wx_url'];
    }

    /**
     * Author sam
     * DateTime 2019-09-26 18:40
     * Description:发送消息到企业微信
     * @param $msg
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function wxSend($msg)
    {
        $postData = [
            'msgtype'=>'text',
            'text'=>[
                'content'=>$msg,
                'mentioned_list'=>'@all',
            ],
        ];
        $postData = json_encode($postData,JSON_UNESCAPED_UNICODE);
        $client = new Client(['base_uri'=>$this->wx_uri,'time_out'=>5.0]);
        $client->request('POST',$this->wx_url,['headers'=>['Content-Type'=>'applications/json;charset=utf-8'],'body'=>$postData,'verify'=>false]);
    }
}
