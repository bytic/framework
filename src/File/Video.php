<?php

class Nip_File_Video extends Nip_File_Handler
{
    public static $extensions = ['avi', 'mp4', 'mpg', 'mpeg', 'mkv', 'm4v'];
    protected $_ffmpeg_video;

    public function convert($params = [], $removeOriginal = true)
    {
        if (!$params['f'] || $params['f'] == $this->extension) {
            return;
        }

        $command = [];
        $command[] = '/usr/bin/ffmpeg';
        $command[] = '-i '.escapeshellarg($this->path);
        foreach ($params as $key => $value) {
            $command[] = "-$key ".escapeshellarg($value);
        }

        $path = dirname($this->path).DIRECTORY_SEPARATOR.pathinfo($this->path, PATHINFO_FILENAME).'.'.strtolower($params['f']);
        $command[] = $path;

        $command = implode(' ', $command)." && chmod 777 $path".($removeOriginal ? " && rm $this->path" : '');

        $process = new Process($command);
        $process->start();
    }

    public function saveRandomFrame($dir, $width = false, $height = false)
    {
        /* @var $frame ffmpeg_frame */
        $frame = $this->getRandomFrame();
        $image = new Image_VideoFrame();
        $image->setFFmpegFrame($frame);

        if (!$width) {
            $width = $frame->getWidth();
        }
        if (!$height) {
            $height = $frame->getHeight();
        }
        $image->cropToCenter($width, $height);
        $image->unsharpMask();

        $filename = explode('.', basename($this->path));
        array_pop($filename);
        $filename[] = 'jpg';
        $filename = implode('.', $filename);
        $image->path = $dir.'/'.$filename;
        $image->save();
    }

    public function getRandomFrame()
    {
        return $this->getFFmpegVideo()->getFrame(rand(1, $this->getFrameCount()));
    }

    protected function getFFmpegVideo()
    {
        if (!$this->_ffmpeg_video) {
            $this->_ffmpeg_video = new ffmpeg_movie($this->path);
        }

        return $this->_ffmpeg_video;
    }

    public function getFrameCount()
    {
        return $this->getFFmpegVideo()->getFrameCount();
    }
}
