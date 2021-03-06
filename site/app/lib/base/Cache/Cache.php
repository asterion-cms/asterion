<?php
/**
 * @class Cache
 *
 * This is a helper class to manage the backups.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Cache
{

    /**
     * Show the intro screen of the cache page.
     */
    public static function intro()
    {
        $info = '';
        $filenames = array_merge(rglob(ASTERION_BASE_FILE . 'lib/*_Ui.php'), rglob(ASTERION_APP_FILE . 'lib/*_Ui.php'));
        sort($filenames);
        foreach ($filenames as $filename) {
            $className = str_replace('.php', '', basename($filename));
            $classObjectName = str_replace('_Ui', '', $className);
            $reflection = new ReflectionClass($className);
            $infoClass = '';
            $classMethods = get_class_methods($className);
            sort($classMethods);
            foreach ($classMethods as $classMethod) {
                $method = new ReflectionMethod($className, $classMethod);
                $documentation = $reflection->getMethod($classMethod)->getDocComment();
                preg_match_all('#@(.*?)\n#s', $documentation, $annotations);
                if (isset($annotations[0]) && isset($annotations[0][0])) {
                    if (trim($annotations[0][0]) == "@cache") {
                        $staticLabel = ($method->isStatic()) ? ' <span>(' . __('staticMethod') . ')</span>' : '';
                        $infoClass .= '<div class="block_cache_method">' . $classMethod . $staticLabel . '</div>';
                    }
                }
                unset($annotations);
            }
            $info .= ($infoClass != '') ? '<div class="block_cache">
                                            <a href="' . url('NavigationAdmin/cache-object/' . $classObjectName, true) . '">' . __('cache') . '</a>
                                            <div class="block_cache_title">' . $className . '</div>
                                            <div class="block_cache_methods">' . $infoClass . '</div>
                                        </div>' : '';
        }
        if ($info == '') {
            return '<div class="message message_error">' . __('noObjectsToCache') . '</div>';
        } else {
            return '<div class="buttonCards">
                        <div class="buttonCard">
                            <a href="' . url('NavigationAdmin/cache-all', true) . '">
                                <p><strong>' . __('cacheAll') . '</strong></p>
                                <p>' . __('objectsToCacheInfo') . '</p>
                            </a>
                        </div>
                    </div>
                    <h2>' . __('objectsToCache') . '</h2>
                    <div class="blocks_cache">' . $info . '</div>';
        }
    }

    /**
     * Function to cache one object.
     */
    public static function cacheObject($className)
    {
        $uiClassName = $className . '_Ui';
        $object = new $className;
        $ui = new $uiClassName($object);
        $reflection = new ReflectionClass($uiClassName);
        $classMethods = get_class_methods($uiClassName);
        $items = $object->readList();
        $cacheUrl = Params::param('cache-url');
        foreach ($classMethods as $classMethod) {
            $method = new ReflectionMethod($uiClassName, $classMethod);
            $documentation = $reflection->getMethod($classMethod)->getDocComment();
            preg_match_all('#@(.*?)\n#s', $documentation, $annotations);
            if (isset($annotations[0]) && isset($annotations[0][0])) {
                if (trim($annotations[0][0]) == "@cache") {
                    File::createDirectory(ASTERION_BASE_FILE . 'cache', false);
                    File::createDirectory(ASTERION_BASE_FILE . 'cache/' . $className, false);
                    if (!$method->isStatic()) {
                        foreach ($items as $item) {
                            $itemUi = new $uiClassName($item);
                            $file = ASTERION_BASE_FILE . 'cache/' . $className . '/' . $classMethod . '_' . $item->id() . '.htm';
                            $content = $itemUi->$classMethod();
                            $content = ($cacheUrl != '') ? str_replace(ASTERION_LOCAL_URL, $cacheUrl, $content) : $content;
                            $content = ($cacheUrl != '') ? str_replace(urlencode(ASTERION_LOCAL_URL), $cacheUrl, $content) : $content;
                            File::saveFile($file, $content);
                        }
                    } else {
                        $file = ASTERION_BASE_FILE . 'cache/' . $className . '/' . $classMethod . '.htm';
                        $content = $ui->$classMethod();
                        $content = ($cacheUrl != '') ? str_replace(ASTERION_LOCAL_URL, $cacheUrl, $content) : $content;
                        $content = ($cacheUrl != '') ? str_replace(urlencode(ASTERION_LOCAL_URL), urlencode($cacheUrl), $content) : $content;
                        File::saveFile($file, $content);
                    }
                }
            }
        }
    }

    /**
     * Function to cache all objects.
     */
    public static function cacheAll()
    {
        $filenames = array_merge(rglob(ASTERION_BASE_FILE . 'lib/*_Ui.php'), rglob(ASTERION_APP_FILE . 'lib/*_Ui.php'));
        foreach ($filenames as $filename) {
            $className = str_replace('_Ui.php', '', basename($filename));
            if ($className != 'Navigation' && $className != 'NavigationAdmin') {
                Cache::cacheObject($className);
            }
        }
        header('Location: ' . url('NavigationAdmin/cache/cachedAll', true));
        exit();
    }

}
