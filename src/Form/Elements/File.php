<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Nip_Form_Element_File extends Nip_Form_Element_Input_Abstract
{
    protected $fileData = null;
    protected $fileObject = null;

    public function init()
    {
        parent::init();
        $this->setAttrib('type', 'file');
        $this->getForm()->setAttrib('enctype', 'multipart/form-data');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @param $request
     * @return void
     */
    public function getDataFromRequest($request)
    {
        $object = $this->getFileObject();
        $this->setValue($object);
    }

    /**
     * @return mixed
     */
    public function getFileObject()
    {
        if ($this->fileObject === null) {
            $this->fileObject = $this->generateObjectFromData($this->getFileData());
        }

        return $this->fileObject;
    }

    /**
     * @param $data
     * @return bool|UploadedFile
     */
    protected function generateObjectFromData($data)
    {
        if (!is_array($data)) {
            return false;
        }

        $keys = array_keys($data);
        if (count(array_diff($keys, ['error', 'name', 'size', 'tmp_name', 'type'])) > 0) {
            return false;
        }

        if (UPLOAD_ERR_NO_FILE == $data['error']) {
            return false;
        }

        $file = new UploadedFile($data['tmp_name'], $data['name'], $data['type'], $data['size'],
            $data['error']);

        return $file;
    }

    /**
     * @return array|null
     */
    public function getFileData()
    {
        if (!$this->fileData) {
            $name = $this->getName();
            $name = str_replace(']', '', $name);
            $parts = explode('[', $name);

            if (count($parts) > 1) {
                if ($_FILES[$parts[0]]) {
                    $fileData = [];
                    foreach ($_FILES[$parts[0]] as $key => $data) {
                        $fileData[$key] = $data[$parts[1]];
                    }
                    $this->fileData = $fileData;
                } else {
                    $this->fileData = null;
                }
            } else {
                $this->fileData = $_FILES[$name];
            }
        }

        return $this->fileData;
    }
}
