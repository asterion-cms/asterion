<?php
/**
 * @class Image
 *
 * This is a helper class to deal with the images.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Image
{

    public $url;
    public $mime;
    public $width;
    public $height;

    /**
     * The constructor of the object.
     */
    public function __construct($file)
    {
        if (is_file($file)) {
            $info = getimagesize($file);
            $this->url = $file;
            $this->width = $info[0];
            $this->height = $info[1];
            $this->mime = $info['mime'];
        }
    }

    /**
     * Return an information of the image.
     */
    public function get($item)
    {
        return (isset($this->$item)) ? $this->$item : false;
    }

    /**
     * Return the public url of the image.
     */
    public function getUrl()
    {
        return str_replace(ASTERION_LOCAL_FILE, ASTERION_LOCAL_URL, $this->get('url'));
    }

    /**
     * Return the image extension.
     */
    public function getExtension()
    {
        switch ($this->mime) {
            case 'image/jpg':
            case 'image/jpeg':
                return 'jpg';
                break;
            case 'image/gif':
                return 'gif';
                break;
            case 'image/png':
                return 'png';
                break;
        }
    }

    /**
     * Return the type of the image.
     */
    public static function getType($mime)
    {
        $type = explode('/', $mime);
        $type = $type[1];
        if ($type == 'jpg') {$type = 'jpeg';}
        if ($type != '' && $type != 'jpeg' && $type != 'png' && $type != 'gif') {
            throw new Exception('Cannot resize image. Mime:' . $mime);
        }
        return $type;
    }

    /**
     * Return the file name of the image.
     */
    public function getFileName()
    {
        $file = explode('.', basename($this->url));
        return $file[0];
    }

    /**
     * Convert an image to JPG.
     */
    public function toJpg()
    {
        if ($this->getExtension() != 'jpg') {
            $extension = $this->getExtension();
            if ($extension != '') {
                $function = 'imagecreatefrom' . $extension;
                $image = $function($this->get('url'));
                $fileDestinationArray = explode('.', $this->get('url'));
                $fileDestination = $fileDestinationArray[0] . '.jpg';
                imagejpeg($image, $fileDestination, 100);
                imagedestroy($image);
                unlink($this->get('url'));
                $this->url = $fileDestination;
                $this->mime = 'image/jpeg';
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Resize an image.
     */
    public function resize($fileDestination, $newWidth, $maxHeight, $mime)
    {
        $fileOrigin = $this->get('url');
        $type = $this->getType($mime);
        $function = 'imagecreatefrom' . $type;
        $image = $function($fileOrigin);
        $widthImage = imagesx($image);
        $heightImage = imagesy($image);
        if ($widthImage < $newWidth) {
            if (!copy($fileOrigin, $fileDestination) && ASTERION_DEBUG) {
                throw new Exception('Cannot copy from ' . $fileOrigin . ' to ' . $fileDestination);
            }
        } else {
            $newHeight = ceil(($newWidth * $heightImage) / $widthImage);
            if ($newHeight > $maxHeight) {
                $newHeight = $maxHeight;
                $newWidth = ceil(($newHeight * $widthImage) / $heightImage);
            }
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $widthImage, $heightImage);
            $function = 'image' . $type;
            $function($newImage, $fileDestination, 100);
            imagedestroy($newImage);
            imagedestroy($image);
        }
    }

    /**
     * Convert an image into grayscale.
     */
    public function grayscale($fileDestination)
    {
        $fileOrigin = $this->get('url');
        $type = $this->getType($mime);
        $function = "imagecreatefrom" . $type;
        $image = $function($fileOrigin);
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        for ($i = 0; $i < $imageWidth; $i++) {
            for ($j = 0; $j < $imageHeight; $j++) {
                $rgb = imagecolorat($image, $i, $j);
                $rr = ($rgb >> 16) & 0xFF;
                $gg = ($rgb >> 8) & 0xFF;
                $bb = $rgb & 0xFF;
                $g = round(($rr + $gg + $bb) / 3);
                $val = imagecolorallocate($image, $g, $g, $g);
                imagesetpixel($image, $i, $j, $val);
            }
        }
        imagejpeg($image, $fileDestination, 100);
    }

    /**
     * Resize an image an cut the borders to create a perfect square.
     */
    public function resizeSquare($fileDestination, $newSide, $mime)
    {
        $fileOrigin = $this->get('url');
        $type = $this->getType($mime);
        $function = "imagecreatefrom" . $type;
        $image = $function($fileOrigin);
        $widthImage = imagesx($image);
        $heightImage = imagesy($image);
        if ($widthImage > $heightImage) {
            $relation = $heightImage / $widthImage;
            $newWidth = intval($newSide / $relation);
            $newHeight = $newSide;
            $left = intval(($newWidth - $newSide) / 2);
            $top = 0;
        } else {
            $relation = $widthImage / $heightImage;
            $newWidth = $newSide;
            $newHeight = intval($newSide / $relation);
            $left = 0;
            $top = intval(($newHeight - $newSide) / 2);
        }
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $widthImage, $heightImage);
        $squareImage = imagecreatetruecolor($newSide, $newSide);
        imagecopyresized($squareImage, $newImage, 0, 0, $left, $top, $newSide, $newSide, $newSide, $newSide);
        $function = "image" . $type;
        $function($squareImage, $fileDestination, 100);
        imagedestroy($squareImage);
        imagedestroy($image);
    }

}
