<?php


namespace AppBundle\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PersonPredictor
{
    const DIC = 'dopplerganger.service.person_predictor';

    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function predict($imageName)
    {
        return 'antonio';

        $process = new Process('sudo docker exec 5c8cf3e58925 /root/openface/demos/classifier.py --verbose infer /root/openface/docker_data/test-60/generated-embeddings/classifier.pkl /root/openface/docker_data/images_to_test/joana.png');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        //remove this shit
        $namePredicted = trim($this->getStringBetween($output, 'Predict ', 'with'));

        return $namePredicted;
    }

    public function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
