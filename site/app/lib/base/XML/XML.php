<?php
/**
 * @class XML
 *
 * This is a helper class to manage XML files.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class XML
{

    /**
     * Write an XML file.
     */
    public static function writeXML($fileName, $content, $headers = '')
    {
        switch ($headers) {
            default:
            case 'rss':
                $contentXML = XML::formatRss($content);
                break;
            case 'sitemap':
                $contentXML = XML::formatSitemap($content);
                break;
        }
        if (!file_exists($fileName)) {
            touch($fileName);
        }
        $handle = fopen($fileName, "wb");
        $numbytes = fwrite($handle, $contentXML);
        fclose($handle);
    }

    /**
     * Convert a XML file into an array.
     */
    public static function toArray($xmlObject, &$array)
    {
        $children = $xmlObject->children();
        foreach ($children as $elementName => $node) {
            $nextIdx = count($array);
            $array[$nextIdx] = [];
            $array[$nextIdx]['@name'] = strtolower((string) $elementName);
            $array[$nextIdx]['@attributes'] = [];
            $attributes = $node->attributes();
            foreach ($attributes as $attributeName => $attributeValue) {
                $attribName = strtolower(trim((string) $attributeName));
                $attribVal = trim((string) $attributeValue);
                $array[$nextIdx]['@attributes'][$attribName] = $attribVal;
            }
            $text = (string) $node;
            $text = trim($text);
            if (strlen($text) > 0) {
                $array[$nextIdx]['@text'] = $text;
            }
            $array[$nextIdx]['@children'] = [];
            XML::toArray($node, $array[$nextIdx]['@children']);
        }
        return;
    }

    /**
     * Read the XML file containing the class.
     */
    public static function readClass($class)
    {
        $fileXML = '';
        $addLocation = $class . '/' . $class . '.xml';
        foreach (unserialize(ASTERION_OBJECT_LOCATIONS) as $location) {
            $fileXML = (is_file($location . $addLocation)) ? $location . $addLocation : $fileXML;
        }
        return simplexml_load_file($fileXML);
    }

    /**
     * Format a XML RSS file.
     */
    public static function formatRss($content, $options = [])
    {
        $title = (isset($options['title'])) ? $options['title'] : Params::param('title_page');
        $link = (isset($options['link'])) ? $options['link'] : ASTERION_LOCAL_URL;
        $description = (isset($options['description'])) ? $options['description'] : Params::param('meta_description');
        return '<?xml version="1.0" encoding="ISO-8859-1"?>
                    <rss version="2.0">
                    <channel>
                        <title>' . $title . '</title>
                        <link>' . $link . '</link>
                        <description>' . $description . '</description>
                        <language>' . Language::active() . '</language>
                        <lastBuildDate>' . date('l jS \of F Y h:i:s A') . '</lastBuildDate>
                        <administrator>' . Params::param('title_page') . '</administrator>
                        <image>
                            <title>' . Params::param('title_page') . '</title>
                            <url>' . ASTERION_LOGO . '</url>
                            <link>' . ASTERION_LOCAL_URL . '</link>
                            <description>' . Params::param('meta_description') . '</description>
                        </image>
                        ' . $content . '
                    </channel>
                    </rss>';
    }

    /**
     * Format a XML sitemap file.
     */
    public static function formatSitemap($content)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                    ' . $content . '
                </urlset>';
    }

}
