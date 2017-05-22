<?php


namespace AppBundle\Service;

use GuzzleHttp\Client;

class VideoAPI
{
    const DIC = 'faplike.service.video_api';

    public function search($name)
    {
        $client = new Client(['base_uri' => 'http://www.pornhub.com/webmasters/']);
        $response = $client->request('GET', 'search', [
            'query' => ['id' => '10050107', 'search' => $name, 'thumbsize' => 'medium']
        ]);

        $videos = array_slice(json_decode($response->getBody(), true)['videos'], 0, 2);
        $videoInfoList = [];
        foreach ($videos as $videoInfo) {
            $videoInfoList[] = [
                'videoId' => $videoInfo['video_id'],
                'url' => $videoInfo['url'],
                'default_thumb' => $videoInfo['default_thumb'],
            ];
        }
        
        return $videoInfoList;
    }
}
