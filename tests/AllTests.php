<?php
require_once dirname(__FILE__) . '/bootstrap.php';

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();
        $files = self::getFiles(dirname(__FILE__));
        foreach ($files as $file) {
            require_once $file;
            $suite->addTestSuite(basename($file, '.php'));
        }
        return $suite;
    }

    protected static function getFiles($dir)
    {
        $files = array();
        foreach (glob("$dir/*") as $file) {
            if (is_dir($file)) {
                $files += self::getFiles($file);
                continue;
            }
            if (preg_match('/Test\.php$/', $file)) {
                $files[] = $file;
            }
        }
        return $files;
    }
}
