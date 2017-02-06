<?php
/**
* @class File
*
* This is a helper class to deal with the files.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class File {

    /**
    * Upload a file using the field name.
    */
    static public function uploadUrl($url, $objectName, $uploadName) {
        if (url_exists($url)) {
            $mainFolder = STOCK_FILE.$objectName.'Files';
            File::createDirectory($mainFolder);
            $fileDestination = $mainFolder.'/'.$uploadName;
            if (copy($url, $fileDestination)) {
                @chmod($fileDestination, 0777);
                return true;
            }
        }
        return false;
    }    

    /**
    * Upload a file using the field name.
    */
    static public function upload($objectName, $name, $uploadName='') {
        if (isset($_FILES[$name]) && $_FILES[$name]['tmp_name']!='') {
            $mainFolder = STOCK_FILE.$objectName.'Files';
            File::createDirectory($mainFolder);
            $uploadName = ($uploadName!='') ? $uploadName : Text::simpleUrlFile($_FILES[$name]['name']);
            $fileOrigin = $_FILES[$name]['tmp_name'];
            $fileDestination = $mainFolder.'/'.$uploadName;
            return move_uploaded_file($fileOrigin, $fileDestination);
        }
        return false;
    }

    /**
    * Save content to a file.
    */
    static public function saveFile($file, $content, $tiny=false) {
        @touch($file);
        if (file_exists($file)) {
            if ($tiny==true) {
                $content = str_replace('\"','"',$content);
                $content = str_replace('&quot;','"',$content);
                $content = str_replace("\'","'",$content);
                $content = str_replace("\'","'",$content);
                $content = str_replace("&#39","SS",$content);
            }
            $fhandle = fopen($file,"w");
            fwrite($fhandle,$content);
            fclose($fhandle);
        }
    }
    
    /**
    * Copy an entire directory and its files.
    */
    static public function copyDirectory($source, $destination, $permissions=0755) {
        if (is_link($source)) {
            return symlink(readlink($source), $destination);
        }
        if (is_file($source)) {
            return copy($source, $destination);
        }
        if (!is_dir($destination)) {
            mkdir($destination, $permissions);
        }
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            File::copyDirectory("$source/$entry", "$destination/$entry");
        }
        $dir->close();
        return true;
    }

    /**
    * Change the permissions of an entire directory an its files.
    */
    static public function chmodDirectory($path, $fileMode, $dirMode) {
        if (is_dir($path) ) {
            if (!chmod($path, $dirMode)) {
                $dirMode_str=decoct($dirMode);
                return;
            }
            $directoryHead = opendir($path);
            while (($file = readdir($directoryHead)) !== false) {
                if($file != '.' && $file != '..') {
                    $fullPath = $path.'/'.$file;
                    File::chmodDirectory($fullPath, $fileMode, $dirMode);
                }
            }
            closedir($directoryHead);
        } else {
            if (is_link($path)) {
                return;
            }
            if (!chmod($path, $fileMode)) {
                $fileMode_str=decoct($fileMode);
                return;
            }
        }
    } 

    /**
    * Change headers and force a file download.
    */
    static public function download($file, $options=array()) {
        $content = (isset($options['content'])) ? $options['content'] : '';
        $contentType = (isset($options['contentType'])) ? $options['contentType'] : '';
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename='.File::basename($file));
        header('Content-Type: '.$contentType);
        header('Content-Transfer-Encoding: binary');
        if ($content!='') {
            echo $content;
        } else {
            readfile($file);
        }
    }
    
    /**
    * Get the basename of a file.
    */
    static public function basename($file) {
        $info = pathinfo($file);
        return (isset($info['basename'])) ? $info['basename'] : '';
    }

    /**
    * Get the basename of a file.
    */
    static public function filename($file) {
        $info = pathinfo($file);
        return (isset($info['filename'])) ? $info['filename'] : '';
    }

    /**
    * Create a directory in the server.
    */
    static public function createDirectory($dirname, $exception=true) {
        if (!@is_dir($dirname)) {
            if (!@mkdir($dirname)) {
                if ($exception && DEBUG) {
                    throw new Exception('Could not create folder '.$dirname);
                }
            }
        }
        @chmod($dirname, 0755);
    }

    /**
    * Delete a directory and all files and subdirectories in it.
    */
    static public function deleteDirectory($dirname) {
        if (is_dir($dirname)) {
            $handle = opendir($dirname);    
            if (!$handle) {                
                return false;
            }
            while($file = readdir($handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dirname."/".$file)) {
                        unlink($dirname."/".$file);
                    } else {                        
                        File::deleteDirectory($dirname.'/'.$file);                    
                    }
                }
            }
        }
        closedir($handle);
        rmdir($dirname);
        return true;
    }

    /**
    * Get the extension of an URL.
    */
    static public function urlExtension($url) {
        if (url_exists($url)) {
            $urlComponents = parse_url($url);
            $urlPath = $urlComponents['path'];
            return pathinfo($urlPath, PATHINFO_EXTENSION);
        }
    }

    /**
    * Get the extension of a file.
    */
    static public function fileExtension($filename) {
        $info = explode('.', $filename);
        return strtolower($info[count($info)-1]);
    }

    /**
    * Scan the website and return all the active objects.
    */
    static public function scanDirectoryObjects() {
        $objectNames = array();
        $objectNames = array_merge($objectNames, File::scanDirectoryObjectsApp());
        $objectNames = array_merge($objectNames, File::scanDirectoryObjectsBase());
        return $objectNames;
    }

    /**
    * Scan the website and return all the active objects from the application.
    */
    static public function scanDirectoryObjectsApp() {
        return File::scanDirectoryObjectsGeneric(APP_FILE.'lib');
    }

    /**
    * Scan the website and return all the active objects from the public website.
    */
    static public function scanDirectoryObjectsBase() {
        return File::scanDirectoryObjectsGeneric(BASE_FILE.'lib');
    }

    /**
    * Scan a directory and return all the active objects.
    */
    static public function scanDirectoryObjectsGeneric($directory) {
        $objectNames = File::scanDirectoryExtension($directory, 'xml');
        foreach ($objectNames as $key=>$objectName) {
            $objectNames[$key] = basename($objectName, '.xml');
        }
        sort($objectNames);
        return $objectNames;
    }

    /**
    * Scan the website and return all the the files with some extension.
    */
    static public function scanDirectoryExtension($directory, $extension) {
        $recursiveDirectory = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $recursiveIterator = new RecursiveIteratorIterator($recursiveDirectory);
        $files = array();
        foreach($recursiveIterator as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == $extension) {
                $files[] = (string)$file;
            }
        }
        return $files;
    }

}
?>