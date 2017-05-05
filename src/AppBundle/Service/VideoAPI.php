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
            'query' => ['id' => '44bc40f3bc04f65b7a35', 'search' => $name, 'thumbsize' => 'medium']
        ]);

        $embedVideosInformation = array_slice(json_decode($response->getBody(), true)['videos'], 0, 2);
        $embedIds = [];
        foreach ($embedVideosInformation as $videoInfo) {
            $embedIds[] = $videoInfo['video_id'];
        }
        
        return $embedIds;
    }
}
