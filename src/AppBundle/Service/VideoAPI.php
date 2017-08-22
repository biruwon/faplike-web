<?php


namespace AppBundle\Service;

use GuzzleHttp\Client;

class VideoAPI
{
    const DIC = 'faplike.service.video_api';

    private $endpoint;
    private $id;

    public function __construct($endpoint, $id)
    {
        $this->endpoint = $endpoint;
        $this->id = $id;
    }

    public function search($name)
    {
        $client = new Client(['base_uri' => $this->endpoint]);
        $response = $client->request('GET', 'search', [
            'query' => ['id' => $this->id, 'search' => $name, 'thumbsize' => 'medium']
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
