<?php

/**
 * Nip Framework.
 *
 * @category   Nip
 *
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @version    SVN: $Id: Image.php 193 2009-06-23 23:11:53Z victorstanciu $
 */
class Nip_File_Image extends Nip_File_Handler
{
    public $extensions = ['jpg', 'jpeg', 'gif', 'png'];
    public $quality = 90;
    public $type = 'jpg';
    public $max_width = false;
    public $errors = [];

    protected $_resource;
    protected $_file;
    protected $_upload;
    protected $_width;
    protected $_height;

    /**
     * @param array $upload
     */
    public function setResourceFromUpload($upload)
    {
        $this->_upload = $upload;
        $this->setResourceFromFile($upload['tmp_name']);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function setResourceFromFile($path)
    {
        $this->_file = $path;
        if (file_exists($path)) {
            $details = getimagesize($path);

            switch ($details['mime']) {
                case 'image/gif':
                    $this->type = 'gif';
                    if (imagetypes() & IMG_GIF) {
                        $this->_resource = imagecreatefromgif($path);
                    }
                    break;
                case 'image/jpeg':
                    $this->type = 'jpg';
                    if (imagetypes() & IMG_JPG) {
                        $this->_resource = imagecreatefromjpeg($path);
                    }
                    break;
                case 'image/png':
                    $this->type = 'png';
                    if (imagetypes() & IMG_PNG) {
                        $this->_resource = imagecreatefrompng($path);
                    }
                    break;
            }

            $this->getWidth();
            $this->getHeight();

            return true;
        } else {
            trigger_error("Cannot find file $path", E_USER_ERROR);
        }

        return false;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        if (!$this->_width && $this->_resource) {
            $this->setWidth(imagesx($this->_resource));
        }

        return $this->_width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->_width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        if (!$this->_height && $this->_resource) {
            $this->setHeight(imagesy($this->_resource));
        }

        return $this->_height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->_height = $height;
    }

    /**
     * @param string $name
     */
    public function setBaseName($name)
    {
        $name = $name.'.'.$this->type;
        $this->setName($name);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->url = dirname($this->url).'/'.$this->name;
        $this->path = dirname($this->path).'/'.$this->name;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (Nip_File_System::instance()->createDirectory(dirname($this->path))) {
            switch ($this->type) {
                case 'png':
                    if ($this->quality > 9) {
                        if ($this->quality < 100) {
                            $this->quality = $this->quality / 10;
                        } else {
                            $this->quality = 9;
                        }
                    }
                    $this->quality = abs($this->quality - 9);
                    $this->quality = 0;

                    $newImg = imagecreatetruecolor($this->_width, $this->_height);
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg, true);

                    imagecopyresampled($newImg, $this->_resource, 0, 0, 0, 0, $this->_width, $this->_height, $this->_width, $this->_height);

                    $return = imagepng($newImg, $this->path, $this->quality);
                    break;
                case 'jpg':
                default:
                    $return = imagejpeg($this->_resource, $this->path, $this->quality);
                    break;
            }

            if ($return) {
                chmod($this->path, 0777);

                return true;
            }
            $this->errors[] = 'Error saving file';
        } else {
            $this->errors[] = 'Error creating directory';
        }

        return false;
    }

    public function grayscaleFade()
    {
        $this->grayscaleFilter();
        imagefilter($this->_resource, IMG_FILTER_BRIGHTNESS, 50);
    }

    public function grayscaleFilter()
    {
        imagefilter($this->_resource, IMG_FILTER_GRAYSCALE);
    }

    public function resize($max_width = false, $max_height = false)
    {
        if (!$max_width) {
            if ($this->max_width) {
                $max_width = $this->max_width;
            } else {
                $max_width = $this->getWidth();
            }
        }

        if (!$max_height) {
            if ($this->max_height) {
                $max_height = $this->max_height;
            } else {
                $max_height = $this->getHeight();
            }
        }

        $ratio = $this->getRatio();
        $target_ratio = $max_width / $max_height;

        if ($ratio > $target_ratio) {
            $new_width = $max_width;
            $new_height = round($max_width / $ratio);
        } else {
            $new_height = $max_height;
            $new_width = round($max_height * $ratio);
        }

        $image = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        imagecopyresampled($image, $this->_resource, 0, 0, 0, 0, $new_width, $new_height, $this->getWidth(), $this->getHeight());

        $this->_width = $new_width;
        $this->_height = $new_height;
        $this->_resource = $image;

        return $this;
    }

    public function getRatio()
    {
        return $this->getWidth() / $this->getHeight();
    }

    public function cropToCenter($cWidth, $cHeight)
    {
        $this->resizeToLarge($cWidth, $cHeight);

        $width = $this->getWidth();
        $height = $this->getHeight();

        $x0 = round(abs(($width - $cWidth) / 2), 0);
        $y0 = round(abs(($height - $cHeight) / 2), 0);

        $this->crop($x0, $y0, $cWidth, $cHeight, $cWidth, $cHeight);
    }

    /**
     * @param bool|int $max_width
     * @param bool|int $max_height
     *
     * @return $this
     */
    public function resizeToLarge($max_width = false, $max_height = false)
    {
        if (!$max_width) {
            $max_width = $this->getWidth();
        }

        if (!$max_height) {
            $max_height = $this->getHeight();
        }

        $sourceRatio = $this->getRatio();
        $target_ratio = $max_width / $max_height;

        if ($sourceRatio > $target_ratio) {
            $new_height = $max_height;
            $new_width = (int) ($max_height * $sourceRatio);
        } else {
            $new_width = $max_width;
            $new_height = (int) ($max_width / $sourceRatio);
        }

        $image = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        imagecopyresampled($image, $this->_resource, 0, 0, 0, 0, $new_width, $new_height, $this->getWidth(), $this->getHeight());

        $this->_width = $new_width;
        $this->_height = $new_height;
        $this->_resource = $image;

        return $this;
    }

    /**
     * @param $x
     * @param $y
     * @param $dwidth
     * @param $dheight
     * @param $swidth
     * @param $sheight
     */
    public function crop($x, $y, $dwidth, $dheight, $swidth, $sheight)
    {
        $image = imagecreatetruecolor($dwidth, $dheight);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        imagecopyresampled($image, $this->_resource,
            0, 0,
            $x, $y,
            $dwidth, $dheight,
            $swidth, $sheight);

        $this->_width = $dwidth;
        $this->_height = $dheight;
        $this->_resource = $image;
    }

    /**
     * @param int   $amount
     * @param float $radius
     * @param int   $threshold
     *
     * @return $this
     */
    public function unsharpMask($amount = 80, $radius = 0.5, $threshold = 3)
    {
        $img = &$this->_resource;

        if ($amount > 500) {
            $amount = 500;
        }
        $amount = $amount * 0.016;
        if ($radius > 50) {
            $radius = 50;
        }
        $radius = $radius * 2;
        if ($threshold > 255) {
            $threshold = 255;
        }

        $radius = abs(round($radius));
        if ($radius == 0) {
            return;
        }

        $w = $this->_width;
        $h = $this->_height;

        $imgCanvas = imagecreatetruecolor($w, $h);
        $imgBlur = imagecreatetruecolor($w, $h);

        if (function_exists('imageconvolution')) {
            $matrix = [[1, 2, 1], [2, 4, 2], [1, 2, 1]];
            imagecopy($imgBlur, $img, 0, 0, 0, 0, $w, $h);
            imageconvolution($imgBlur, $matrix, 16, 0);
        } else {
            for ($i = 0; $i < $radius; $i++) {
                imagecopy($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h);
                imagecopymerge($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50);
                imagecopymerge($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50);
                imagecopy($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

                imagecopymerge($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333);
                imagecopymerge($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25);
            }
        }

        if ($threshold > 0) {
            for ($x = 0; $x < $w - 1; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    $rgbOrig = imagecolorat($img, $x, $y);
                    $rOrig = (($rgbOrig >> 16) & 0xFF);
                    $gOrig = (($rgbOrig >> 8) & 0xFF);
                    $bOrig = ($rgbOrig & 0xFF);

                    $rgbBlur = imagecolorat($imgBlur, $x, $y);

                    $rBlur = (($rgbBlur >> 16) & 0xFF);
                    $gBlur = (($rgbBlur >> 8) & 0xFF);
                    $bBlur = ($rgbBlur & 0xFF);

                    $rNew = (abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig;
                    $gNew = (abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig;
                    $bNew = (abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig;

                    if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
                        $pixCol = imagecolorallocate($img, $rNew, $gNew, $bNew);
                        imagesetpixel($img, $x, $y, $pixCol);
                    }
                }
            }
        } else {
            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    $rgbOrig = imagecolorat($img, $x, $y);
                    $rOrig = (($rgbOrig >> 16) & 0xFF);
                    $gOrig = (($rgbOrig >> 8) & 0xFF);
                    $bOrig = ($rgbOrig & 0xFF);

                    $rgbBlur = imagecolorat($imgBlur, $x, $y);

                    $rBlur = (($rgbBlur >> 16) & 0xFF);
                    $gBlur = (($rgbBlur >> 8) & 0xFF);
                    $bBlur = ($rgbBlur & 0xFF);

                    $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
                    if ($rNew > 255) {
                        $rNew = 255;
                    } elseif ($rNew < 0) {
                        $rNew = 0;
                    }
                    $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
                    if ($gNew > 255) {
                        $gNew = 255;
                    } elseif ($gNew < 0) {
                        $gNew = 0;
                    }
                    $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
                    if ($bNew > 255) {
                        $bNew = 255;
                    } elseif ($bNew < 0) {
                        $bNew = 0;
                    }
                    $rgbNew = ($rNew << 16) + ($gNew << 8) + $bNew;
                    imagesetpixel($img, $x, $y, $rgbNew);
                }
            }
        }

        imagedestroy($imgCanvas);
        imagedestroy($imgBlur);

        return $this;
    }

    /**
     * @param Nip_File_Image $image
     *
     * @return $this
     */
    public function copyResource(self $image)
    {
        $this->_width = $image->getWidth();
        $this->_height = $image->getHeight();
        $this->_resource = $image->getResource();

        return $this;
    }

    public function getResource()
    {
        return $this->_resource;
    }

    public function setResource($gdImage)
    {
        $this->_resource = $gdImage;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return Nip_File_System::instance()->getExtension($this->path);
    }
}
