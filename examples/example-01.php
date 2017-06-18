<?php
use JordiCSV\DB;
use JordiCSV\CSV;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/prod.php';

try
{
    $csv = new CSV(DB::getInstance());

    $errors = $csv->load(__DIR__ . '/../files/' . $argv[1])->dump($argv[2], $argv[3]);

    echo 'OK. The file has been successfully dumped into the database!' . PHP_EOL;

    if(!empty($errors))
    {
        echo 'However, the following invalid rows were found: ' . PHP_EOL;
        foreach($errors as $error)
        {
            $line = implode(', ', $error);
            echo rtrim($line, ', ') . PHP_EOL;
        }
        echo 'Open the file with your favorite editor, fix the errors and try again. ' . PHP_EOL;
    }

}
catch (\Exception $e)
{
  echo $e->getMessage();
}
