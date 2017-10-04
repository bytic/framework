<?php
class Nip_Helper_Async extends Nip\Helpers\AbstractHelper
{
    public function sendMessage($message, $type = 'success', $format ='json')
    {
        $data = array(
            'type' => $type,
            'message' => $message,
            );
        return $this->$format($data);
    }
    
    public function json($data)
    {
        header("Content-type: text/x-json");
        echo(is_string($data) ? $data : json_encode($data));
        exit();
    }

    public function txt($data)
    {
        header("Content-type: text/plain");
        echo($data);
        exit();
    }

    public function html($data)
    {
        header("Content-type: text/html");
        echo($data);
        exit();
    }
}
