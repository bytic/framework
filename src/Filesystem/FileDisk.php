<?php

namespace Nip\Filesystem;

use League\Flysystem\Filesystem as Flysystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FlysystemAdapter
 * @package Nip\Filesystem
 */
class FileDisk extends Flysystem
{

    /**
     * Store the uploaded file on the disk with a given name.
     *
     * @param  string $path
     * @param  UploadedFile $file
     * @param  string $name
     * @param  array $options
     * @return string|false
     */
    public function putFileAs($path, $file, $name, $options = [])
    {
        $stream = fopen($file->getRealPath(), 'r+');

        // Next, we will format the path of the file and store the file using a stream since
        // they provide better performance than alternatives. Once we write the file this
        // stream will get closed automatically by us so the developer doesn't have to.
        $result = $this->put(
            $path = trim($path . '/' . $name, '/'),
            $stream,
            $options
        );

        if (is_resource($stream)) {
            fclose($stream);
        }
        return $result ? $path : false;
    }
}
