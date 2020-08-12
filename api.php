<?php
/*
 * @Description: 
 * @Author: Zhang Hao
 * @Date: 2020-07-06 13:57:35
 * @LastEditors: Zhang Hao
 * @LastEditTime: 2020-08-06 21:05:07
 */
header("Content-Type: text/html;charset=utf-8");

if (isset($_GET["url"])) {
    //设置移动端请求头
    $hdrs = array(
        'http' => array('header' =>
        "Referer: https://v.douyin.com/" .
            "User-Agent: Mozilla/5.0 (Linux; Android 8.0.0; Pixel 2 XL Build/OPD1.170816.004) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Mobile Safari/537.36\r\n"),
    );
    //设置请求头
    $context = stream_context_create($hdrs);
    //通过请求获得该视频的id
    file_get_contents($_GET["url"], 0, $context);
    $id = $http_response_header[6];
    $id = explode("video/", $id);
    $id = explode("/?", $id[1]);
    $id = $id[0];
    //通过接口获得视频的详细内容
    $url = "https://www.iesdouyin.com/web/api/v2/aweme/iteminfo/?item_ids=" . $id;
    $jsonData = file_get_contents($url, 0, $context);
    $jsonData = json_decode($jsonData);
    //获取到视频有水印的播放地址
    $url = $jsonData->item_list[0]->video->play_addr->url_list[0];
    $name= $jsonData->item_list[0]->desc;
    $videoId= $jsonData->item_list[0]->aweme_id;
    //获取到视频无水印的播放地址
    $url = str_replace("playwm", "play", $url);
    //获取真实的视频url
    file_get_contents($url, 0, $context);
    for ($i = 0; $i < sizeof($http_response_header); $i++) {
        $url = $http_response_header[$i];
        if (substr($url, 0, 8) == "location") {
            $url = $http_response_header[$i];
            break;
        }
    }

    $url = str_replace("location: ", "", $url);
    $path="/volume4/video/抖音/".($name!=''?$name:$videoId).".mp4";
    $video = file_get_contents($url);
    if(!file_exists($path)){
        file_put_contents($path, $video,FILE_USE_INCLUDE_PATH, $context);
    }
    echo $url;
}
