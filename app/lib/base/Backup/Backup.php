<?php
/**
* @class Backup
*
* This is a helper class to manage the backups.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Backup {
    
    /**
    * Show the intro screen of the backup page.
    */
    static public function intro() {
        $info = '';
        $objectNames = File::scanDirectoryObjects();
        sort($objectNames);
        foreach ($objectNames as $objectName) {
            $object = new $objectName();
            $query = 'SELECT COUNT(*) as numResults FROM '.Db::prefixTable($objectName);
            $results = Db::returnSingle($query);
            $linkReset = url('NavigationAdmin/reset-object/'.$objectName, true);
            $linkBackupJson = url('NavigationAdmin/backup-json/'.$objectName, true);
            $linkBackupSql = url('NavigationAdmin/backup-sql/'.$objectName, true);
            $info .= '<div class="blockCacheItem">
                        <div>
                            '.$objectName.' <span>('.$results['numResults'].' '.__('results').')</span>
                            <a href="'.$linkReset.'" class="resetObject">'.__('reset').'</a>
                            <a href="'.$linkBackupJson.'" target="_blank">'.__('backupJson').'</a>
                            <a href="'.$linkBackupSql.'" target="_blank">'.__('backupSql').'</a>
                        </div>
                    </div>';
        }
        return '<h2>'.__('exportInformation').'</h2>
	            <div class="buttonCards buttonCards2">
	                <div class="buttonCard">
	                    <a href="'.url('NavigationAdmin/backup-sql', true).'" target="_blank">
	                        <p><strong>'.__('sqlFormat').'</strong></p>
	                        <p>'.__('sqlFormatInfo').'</p>
	                    </a>
	                </div>
	                <div class="buttonCard">
	                    <a href="'.url('NavigationAdmin/backup-json', true).'">
	                        <p><strong>'.__('jsonFormat').'</strong></p>
	                        <p>'.__('jsonFormatInfo').'</p>
	                    </a>
	                </div>
	            </div>
	            <h2>'.__('resetObjects').'</h2>
	            <div class="blockCacheItems">
	                '.$info.'
	            </div>';
    }

    /**
    * Backup the information of an object in JSON format.
    */
    static public function backupJson($className='') {
    	if ($className=='') {
    		$objectNames = File::scanDirectoryObjects();
            File::createDirectory(BASE_FILE.'data');
            File::createDirectory(BASE_FILE.'data/backup');
            foreach ($objectNames as $objectName) {
                $object = new $objectName();
                $fileJson = BASE_FILE.'data/backup/'.$objectName.'.json';
                $query = 'SELECT * FROM '.Db::prefixTable($objectName);
                $items = Db::returnAll($query);
                File::saveFile($fileJson, json_encode($items, JSON_PRETTY_PRINT));
            }
            $zipname = 'backup.zip';
            $files = array('readme.txt', 'test.html', 'image.gif');
            $zip = new ZipArchive;
            $zip->open($zipname, ZipArchive::CREATE);
            foreach (glob(BASE_FILE.'data/backup/*.json') as $file) {
                $zip->addFile($file, 'backup/'.basename($file));
            }
            $zip->close();
            header('Content-disposition: attachment; filename='.$zipname);
            header('Content-Length: ' . filesize($zipname));
            readfile($zipname);
            @unlink($zipname);
    	} else {
	    	if (Db::tableExists($className)) {
	            $query = 'SELECT * FROM '.Db::prefixTable($className);
	            $items = Db::returnAll($query);
	            File::download($className.'.json', array('content'=>json_encode($items, JSON_PRETTY_PRINT), 'contentType'=>'application/json'));
	        }
    	}
    }

    /**
    * Backup the information of an object in SQL format.
    */
    static public function backupSql($className='') {
    	if ($className=='') {
    		$content = shell_exec('mysqldump --user='.DB_USER.' --password='.DB_PASSWORD.' --host='.DB_SERVER.' --port='.DB_PORT.' '.DB_NAME);
			File::download('backup.sql', array('content'=>$content, 'contentType'=>'text/plain'));
    	} else {
			if (Db::tableExists($className)) {
			    $content = shell_exec('mysqldump --user='.DB_USER.' --password='.DB_PASSWORD.' --host='.DB_SERVER.' --port='.DB_PORT.' '.DB_NAME.' '.Db::prefixTable($className));
			    File::download($className.'.sql', array('content'=>$content, 'contentType'=>'text/plain'));
			}
    	}
    }

}
?>