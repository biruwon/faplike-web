<?php


namespace AppBundle\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PersonPredictor
{
    const DIC = 'doppelganger.service.person_predictor';

    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function predict($imageName)
    {
        //@TODO how to call docker dinamically
        $dockerCall = 'sudo docker exec 2de4dfbb5fc2 /root/openface/demos/classifier.py --verbose infer /root/openface/web-data/classifier.pkl ';
        $dockerCall .= '/root/openface/web-data/uploads/images/';
        $dockerCall .= $imageName;

        $process = new Process($dockerCall);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        //@TODO remove this shit
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
