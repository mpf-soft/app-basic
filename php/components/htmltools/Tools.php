<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 21.10.2014
 * Time: 17:22
 */

namespace app\components\htmltools;


use mpf\base\Object;
use mpf\loggers\DevLogger;

class Tools extends Object {

    public static function serveFile($file, $as) {
        DevLogger::$ignoreOutput = true;
        header('Expires: Mon, 1 Apr 1974 05:00:00 GMT');
        header('Pragma: no-cache');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Download');
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . trim(`stat -c%s "$file"`));
        header('Content-Disposition: attachment; filename="' . $as . '"');
        header('Content-Transfer-Encoding: binary');
        //@readfile( $file );

        flush();
        $fp = popen("tail -c " . trim(`stat -c%s "$file"`) . " " . $file . ' 2>&1', "r");
        while (!feof($fp)) {
            // send the current file part to the browser
            print fread($fp, 1024);
            // flush the content to the browser
            flush();
        }
        fclose($fp);
    }
} 