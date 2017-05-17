<?php


namespace AppBundle\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PersonPredictor
{
    const DIC = 'faplike.service.person_predictor';

    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function predict($imageName)
    {
        //@TODO how to call docker dinamically
        $dockerCall = 'docker exec 5bf0cc3a12ee /root/openface/demos/classifier.py --verbose infer /root/openface/web-data/classifier-1576-limit-20.pkl ';
        $dockerCall .= '/root/openface/web-data/uploads/images/';
        $dockerCall .= $imageName;

        $process = new Process($dockerCall);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        //@TODO remove this shit and create a DTO
        $namePredicted = trim($this->getStringBetween($output, 'Predict ', 'with'));
        $predictionConfidence = trim($this->getStringBetween($output, 'with ', 'confidence'));

        return [
            'name' => $namePredicted,
            'confidence' => $predictionConfidence
        ];
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
