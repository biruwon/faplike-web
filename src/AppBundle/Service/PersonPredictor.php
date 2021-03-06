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
        //@TODO remove hardcoded command and paths
        $dockerCall = 'docker exec openface /root/openface/demos/classifier.py --verbose infer /root/openface/web-data/classifier.pkl ';
        $dockerCall .= '/root/openface/web-data/uploads/images/';
        $dockerCall .= $imageName;

        $process = new Process($dockerCall);
        $process->run();

        // TODO: if else nightmare
        if (!$process->isSuccessful()) {
            $output = $process->getErrorOutput();

            if ($this->noFaceDetected($output)) {
                $namePredicted = 'none';
                $predictionConfidence = 0;
            } else {

                throw new ProcessFailedException($process);
            }

        } else {

            $output = $process->getOutput();
            //@TODO create a DTO
            $namePredicted = trim($this->getStringBetween($output, 'Predict ', 'with'));
            $predictionConfidence = trim($this->getStringBetween($output, 'with ', 'confidence'));
        }

        return [
            'name' => $namePredicted,
            'confidence' => $predictionConfidence
        ];
    }

    protected function noFaceDetected($output)
    {
        if (strpos($output, 'Unable to find a face') !== false) {
            return true;
        }

        return false;
    }

    protected function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
