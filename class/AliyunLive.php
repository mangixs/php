<?php

class AliyunLive
{
    private $aliyunKey; //阿里云账号key
    private $aliyunSecret; //阿里云账号secret
    private $sourceUrl; //源域名
    private $vhostUrl; //播放主机域名
    private $authKey; //阿里云后台配置的鉴权
    private $second; //鉴权失效时间
    public function __construct($aliyunKey, $aliyunSecret, $sourceUrl, $vhostUrl, $authKey = '', $second = 1800)
    {
        $this->aliyunKey = $aliyunKey;
        $this->aliyunSecret = $aliyunSecret;
        $this->sourceUrl = $sourceUrl;
        $this->vhostUrl = $vhostUrl;
        $this->authKey = $authKey;
        $this->second = $second;
    }
    /**
     * 添加一条推流
     *
     * @param [type] $appName 应用名称
     * @param [type] $streamName 推流名称
     * @param [type] $startTime 开始时间
     * @param [type] $endTime 结束时间
     * @return void
     */
    public function AddLivePullStream($appName, $streamName, $startTime, $endTime)
    {
        vendor('aliyun-openapi-php-sdk.aliyun-php-sdk-core.Config');
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $this->aliyunKey, $this->aliyunSecret);
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new \live\Request\V20161101\AddLivePullStreamInfoConfigRequest();
        $request->setSourceUrl($this->sourceUrl); //用户的直播流所在的源站
        $request->setAppName($appName); //直播流所属应用名称。
        $request->setDomainName($this->vhostUrl); //您的拉流域名为用户的播放域名。
        $request->setEndTime($endTime);
        $request->setStartTime($startTime);
        $request->setStreamName($streamName);
        $response = $client->getAcsResponse($request);
        if (empty($response->Code)) {
            return ['result' => 'SUCCESS', 'data' => $response];
        } else {
            return ['result' => 'ERROR', 'data' => $response];
        }
    }
    /**
     * 删除拉流
     *
     * @param [type] $appName 应用名
     * @param [type] $streamName 拉流名
     * @return void
     */
    public function deleteStream($appName, $streamName)
    {
        vendor('aliyun-openapi-php-sdk.aliyun-php-sdk-core.Config');
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $this->aliyunKey, $this->aliyunSecret);
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new \live\Request\V20161101\DeleteLivePullStreamInfoConfigRequest();
        $request->setAppName($appName);
        $request->setDomainName($this->vhostUrl);
        $request->setStreamName($streamName);
        $response = $client->getAcsResponse($request);
        if (empty($response->Code)) {
            return ['result' => 'SUCCESS', 'data' => $response];
        } else {
            return ['result' => 'ERROR', 'data' => $response];
        }
    }
    /**
     * 生成推流地址
     * @param $[appName] 应用名
     * @param $[pushUrl] [<源域名>]
     * @param $[vhostName] [<播放域名>]
     * @param $[authKey] [<鉴权>]
     * @param $streamName 流名
     * @param $vhost 加速域名
     * @param $time 有效时间单位秒 zxcasdqwe zzxcasdas
     * @return $res key obs填写的流名 shorturl  obs填写的地址 url 完整的推流地址
     */
    public function getPushSteam($appName, $streamName, $type = 'rtmp')
    {
        $pushUrl = $this->sourceUrl;
        $vhostName = $this->vhostUrl;
        $authKey = $this->authKey;
        $time = $this->second;
        $time = time() + $time;
        $url = '';
        $res = [];
        if ($authKey) {
            $auth = md5('/' . $appName . '/' . $streamName . '-' . $time . '-0-0-' . $authKey);
            $url = $pushUrl . '/' . $appName . '/' . $streamName . '?vhost=' . $vhostName . '&auth_key=' . $time . '-0-0-' . $auth;
            $res['key'] = $streamName . '?vhost=' . $vhostName . '&auth_key=' . $time . '-0-0-' . $auth;
        } else {
            $url = $pushUrl . '/' . $appName . '/' . $streamName . '?vhost=' . $vhostName;
            $res['key'] = $streamName . '?vhost=' . $vhostName;
        }
        switch ($type) {
            case 'rtmp':
                $url = 'rtmp://' . $url;
                $res['shorturl'] = 'rtmp://' . $pushUrl . '/' . $appName . '/';
                break;
            case 'flv':
                $url = 'http://' . $url;
                $res['shorturl'] = 'http://' . $pushUrl . '/' . $appName . '/';
                break;
            case 'm3u8':
                $url = 'http://' . $url;
                $res['shorturl'] = 'http://' . $pushUrl . '/' . $appName . '/';
                break;
        }
        $res['url'] = $url;
        return $res;
    }

    /**
     * 生成拉流地址
     * @param $[appName] 应用名
     * @param $[vhostName] [<推流域名>]
     * @param $[authKey] [<鉴权>]
     * @param $streamName 流名
     * @param $vhost 加速域名
     * @param $type 视频格式 支持rtmp、flv、m3u8三种格式
     * @return 播放地址
     */
    public function getPullSteam($appName, $streamName, $type = 'rtmp', $quality = '')
    {
        $vhostName = $this->vhostUrl;
        $authKey = $this->authKey;
        $time = $this->second;
        $time = time() + $time;
        $url = '';
        if (strlen($quality) > 0) {
            $quality = '_' . $quality;
        }
        switch ($type) {
            case 'rtmp':
                $host = 'rtmp://' . $vhostName;
                $url = '/' . $appName . '/' . $streamName . $quality;
                break;
            case 'flv':
                $host = 'http://' . $vhostName;
                $url = '/' . $appName . '/' . $streamName . $quality . '.flv';
                break;
            case 'm3u8':
                $host = 'http://' . $vhostName;
                $url = '/' . $appName . '/' . $streamName . '.m3u8';
                break;
        }
        if ($authKey) {
            $auth = md5($url . '-' . $time . '-0-0-' . $authKey);
            $url = $host . $url . '?auth_key=' . $time . '-0-0-' . $auth;
        } else {
            $url = $host . $url;
        }
        return $url;
    }
}
