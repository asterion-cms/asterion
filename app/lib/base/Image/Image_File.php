<?php
/**
* @class ImageFile
*
* This is a helper class to deal with the image files.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Image_File{

    /**
    * Save an image from a URL.
    */
    static public function saveImageUrl($url, $objectName, $uploadName) {
        return Image_File::saveFileImage($url, $uploadName, STOCK_FILE.$objectName, true);
    }

    /**
    * Save an image from an input file.
    */
    static public function saveImage($objectName, $name, $fileName) {
        if (isset($_FILES[$name]) && $_FILES[$name]['tmp_name']!='') {
            $fileImage = $_FILES[$name]['tmp_name'];
            return Image_File::saveFileImage($fileImage, $fileName, STOCK_FILE.$objectName);
        }
        return false;
    }

    /**
    * Save the image and create versions of itself.
    */
    static private function saveFileImage($fileImage, $fileName, $mainFolder, $copy=false) {
        $localFolder = Text::simpleUrlFileBase($fileName);
        $folder = $mainFolder.'/'.$localFolder;
        if (is_dir($folder)) {
            File::deleteDirectory($folder);
        }
        File::createDirectory($mainFolder);
        File::createDirectory($folder);
        $saveImage = true;
        if ($copy) {
            $fileDestination = $localFolder;
            $destination = $folder."/".$fileDestination.'.'.strtolower(substr($fileImage, -3));
            if (!@copy(str_replace(STOCK_URL, STOCK_FILE, $fileImage), $destination)) {
                $saveImage = false;
            }
        } else {
            $tmpImage = new Image($fileImage);
            $destination = $folder."/".$localFolder.'.'.$tmpImage->getExtension();
            if (!@move_uploaded_file($fileImage, $destination)) {
                $saveImage = false;
            }
        }
        if ($saveImage) {
            @chmod($destination, 0777);
            $image = new Image($destination);
            if ($image->toJpg()) {
                if (SAVE_IMAGE_HUGE) {
                    $fileHuge = $folder."/".$image->getFileName()."_huge.jpg";
                    $image->resize($fileHuge, WIDTH_HUGE, HEIGHT_MAX_HUGE, $image->get('mime'));
                    @chmod($fileHuge, 0777);
                }
                if (SAVE_IMAGE_WEB) {
                    $fileWeb = $folder."/".$image->getFileName()."_web.jpg";
                    $image->resize($fileWeb, WIDTH_WEB, HEIGHT_MAX_WEB, $image->get('mime'));
                    @chmod($fileWeb, 0777);
                }
                if (SAVE_IMAGE_SMALL) {
                    $fileSmall = $folder."/".$image->getFileName()."_small.jpg";
                    $image->resize($fileSmall, WIDTH_SMALL, HEIGHT_MAX_SMALL, $image->get('mime'));
                    @chmod($fileSmall, 0777);
                }
                if (SAVE_IMAGE_THUMB) {
                    $fileThumb = $folder."/".$image->getFileName()."_thumb.jpg";
                    $image->resize($fileThumb, WIDTH_THUMB, HEIGHT_MAX_THUMB, $image->get('mime'));
                    @chmod($fileThumb, 0777);
                }
                if (SAVE_IMAGE_SQUARE) {
                    $fileSquare = $folder."/".$image->getFileName()."_square.jpg";
                    $image->resizeSquare($fileSquare, WIDTH_SQUARE, $image->get('mime'));
                    @chmod($fileSquare, 0777);
                }
                if (!SAVE_IMAGE_ORIGINAL) {
                    @unlink($destination);
                }
                return true;
            }
        }
        return false;
    }

    /**
    * Delete an entire image folder.
    */
    public static function deleteImage($objectName, $name) {
        $directory = STOCK_FILE.$objectName.'/'.$name.'/';
        rrmdir($directory);
    }

}
?>