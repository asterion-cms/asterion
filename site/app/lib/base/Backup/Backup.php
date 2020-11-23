<?php
/**
 * @class Backup
 *
 * This is a helper class to manage the backups.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Backup
{

    /**
     * Show the intro screen of the backup page.
     */
    public static function intro()
    {
        $info = '';
        $objectNames = File::scanDirectoryObjects();
        sort($objectNames);
        foreach ($objectNames as $objectName) {
            $object = new $objectName();
            $query = 'SELECT COUNT(*) as numResults FROM ' . $object->tableName;
            $results = Db::returnSingle($query);
            $linkReset = url('NavigationAdmin/reset-object/' . $objectName, true);
            $linkBackupJson = url('NavigationAdmin/backup-json/' . $objectName, true);
            $linkBackupSql = url('NavigationAdmin/backup-sql/' . $objectName, true);
            $info .= '<div class="block_cacheItem">
                        <div>
                            ' . $objectName . ' <span>(' . $results['numResults'] . ' ' . __('results') . ')</span>
                            <a href="' . $linkReset . '" class="reset_object">' . __('reset') . '</a>
                            <a href="' . $linkBackupJson . '" target="_blank">' . __('backupJson') . '</a>
                            <a href="' . $linkBackupSql . '" target="_blank">' . __('backupSql') . '</a>
                        </div>
                    </div>';
        }
        return '<h2>' . __('exportInformation') . '</h2>
                <div class="buttonCards buttonCards2">
                    <div class="buttonCard">
                        <a href="' . url('NavigationAdmin/backup-sql', true) . '" target="_blank">
                            <p><strong>' . __('sqlFormat') . '</strong></p>
                            <p>' . __('sqlFormatInfo') . '</p>
                        </a>
                    </div>
                    <div class="buttonCard">
                        <a href="' . url('NavigationAdmin/backup-json', true) . '">
                            <p><strong>' . __('jsonFormat') . '</strong></p>
                            <p>' . __('jsonFormatInfo') . '</p>
                        </a>
                    </div>
                </div>
                <h2>' . __('reset_objects') . '</h2>
                <div class="block_cacheItems">
                    ' . $info . '
                </div>';
    }

    /**
     * Backup the information of an object in JSON format.
     */
    public static function backupJson($className = '')
    {
        if ($className == '') {
            $objectNames = File::scanDirectoryObjects();
            File::createDirectory(ASTERION_BASE_FILE . 'data', false);
            File::createDirectory(ASTERION_BASE_FILE . 'data/backup', false);
            foreach ($objectNames as $objectName) {
                $object = new $objectName();
                $fileJson = ASTERION_BASE_FILE . 'data/backup/' . $objectName . '.json';
                $query = 'SELECT * FROM ' . Db::prefixTable($objectName);
                $items = Db::returnAll($query);
                File::saveFile($fileJson, json_encode($items, JSON_PRETTY_PRINT));
            }
            $zipname = 'backup.zip';
            $files = ['readme.txt', 'test.html', 'image.gif'];
            $zip = new ZipArchive;
            $zip->open($zipname, ZipArchive::CREATE);
            foreach (glob(ASTERION_BASE_FILE . 'data/backup/*.json') as $file) {
                $zip->addFile($file, 'backup/' . basename($file));
            }
            $zip->close();
            header('Content-disposition: attachment; filename=' . $zipname);
            header('Content-Length: ' . filesize($zipname));
            readfile($zipname);
            @unlink($zipname);
        } else {
            if (Db::tableExists($className)) {
                $query = 'SELECT * FROM ' . Db::prefixTable($className);
                $items = Db::returnAll($query);
                File::download($className . '.json', ['content' => json_encode($items, JSON_PRETTY_PRINT), 'contentType' => 'application/json']);
            }
        }
    }

    /**
     * Backup the information of an object in SQL format.
     */
    public static function backupSql($className = '')
    {
        if ($className == '') {
            $content = shell_exec('mysqldump --user=' . ASTERION_DB_USER . ' --password=' . ASTERION_DB_PASSWORD . ' --host=' . ASTERION_DB_SERVER . ' --port=' . ASTERION_DB_PORT . ' ' . ASTERION_DB_NAME);
            File::download('backup.sql', ['content' => $content, 'contentType' => 'text/plain']);
        } else {
            if (Db::tableExists($className)) {
                $content = shell_exec('mysqldump --user=' . ASTERION_DB_USER . ' --password=' . ASTERION_DB_PASSWORD . ' --host=' . ASTERION_DB_SERVER . ' --port=' . ASTERION_DB_PORT . ' ' . ASTERION_DB_NAME . ' ' . Db::prefixTable($className));
                File::download($className . '.sql', ['content' => $content, 'contentType' => 'text/plain']);
            }
        }
    }

}
