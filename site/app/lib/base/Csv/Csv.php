<?php
/**
 * @class Csv
 *
 * This is a helper class to manage comma-separated files (CSV) in an easier way.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Csv
{

    /**
     * This function converts a CSV file into a set of arrays.
     * $fileName : The basename of the CSV file in the "data" folder.
     */
    public static function toArrays($fileName)
    {
        $csv = Csv::loadData($fileName);
        $arrays = [];
        $header = true;
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $csv) as $line) {
            if ($header) {
                $headers = explode(';', $line);
                $header = false;
            } else {
                $info = explode(';', $line);
                $arrayIns = [];
                $arrayInsCounter = 0;
                foreach ($headers as $headersItem) {
                    if (isset($info[$arrayInsCounter])) {
                        $arrayIns[$headersItem] = $info[$arrayInsCounter];
                        $arrayInsCounter++;
                    }
                }
                array_push($arrays, $arrayIns);
            }
        }
        return $arrays;
    }

    /**
     * Loads the initial CSV data of a content object.
     * $fileName : The basename of the CSV file in the "data" folder.
     */
    public static function loadData($fileName)
    {
        $file = ASTERION_APP_FILE . 'data/' . $fileName . '.csv';
        if (is_file($file)) {
            return file_get_contents($file);
        }
    }

}
