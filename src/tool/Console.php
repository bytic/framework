<?php

class Nip_Tool_Console
{

    public function askVariant($question, $options)
    {
        $num = 0;
        $responses = array();
        foreach ($options as $key => $value) {
            $num++;
            $question .= "\n" . $num . ') ' . $value;
            $responses[$num] = $key;
        }

        do {
            $response = $this->askQuestion($question);
        
            if (isset ($responses[$response])) {
                return $responses[$response];
            }
        
            $this->error("Invalid response");
        } while (1 == 1);
    }

    public function askConfirmation($question)
    {
		$question .= ' (Y|n) : ';
		$return = $this->askQuestion($question);
        return !$return || $return == 'Y';
    }

    public function askQuestion($question)
    {
		$this->output($question . "\n");
		$handle = fopen ("php://stdin","r");
		$line = fgets($handle);
		return trim($line);
    }

    public function error($error)
    {
        return $this->output("\033[31m" . 'ERROR: ' . $error . "\033[37m");
    }

    public function log($error)
    {
        return $this->output("\033[34m" . 'LOG: ' . $error . "\033[37m");
    }

    public function success($error)
    {
        return $this->output("\033[32m" . 'Success: ' . $error . "\033[37m");
    }

    public function output($output)
    {
        echo "\n";
        echo $output;
    }

}