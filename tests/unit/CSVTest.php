<?php
use JordiCSV\DB;
use JordiCSV\CSV;

require_once __DIR__ . '/../../src/config/dev.php';

class CSVTest extends \Codeception\TestCase\Test
{

    protected $csv;

    public function  _before()
    {
        $this->csv = new CSV(DB::getInstance());
    }

    public function testInstantiate()
    {
        $this->assertInstanceOf('\JordiCSV\CSV', $this->csv);
    }

    public function testLoadThrowsExceptionEmptyFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->csv->load(null, 'table');
    }

    public function testLoadThrowsExceptionFileDoesntExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->csv->load(__DIR__ . '/../_data/non-existent.csv', 'table');
    }

    public function testGetCsv()
    {
        $csv = $this->csv->load(__DIR__ . '/../_data/stock-without-errors.csv')->getCsv();

        $csvHeader = [
            'product_code',
            'product_name',
            'product_description',
            'stock',
            'cost_in_gbp',
            'discontinued'
        ];

        $csvData = [
            [
                'P0001',
                'TV',
                '32” Tv',
                '10',
                '399.99',
                null
            ],
            [
                'P0002',
                'Cd Player',
                'Nice CD player',
                '11',
                '50.12',
                'yes'
            ]
        ];

        $this->assertEquals($csv['header'], $csvHeader);
        $this->assertEquals($csv['data'], $csvData);

    }

    public function testDumpThrowsExceptionEmptyTable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->csv->dump();
    }

    public function testDumpThrowsExceptionTableName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $errors = $this->csv->load(__DIR__ . '/../_data/stock-without-errors.csv')->dump('key', 'w');
    }

    public function testDumpThrowsExceptionMode()
    {
        $this->expectException(\InvalidArgumentException::class);
        $errors = $this->csv->load(__DIR__ . '/../_data/stock-without-errors.csv')->dump('foobar', 'foo');
    }

    public function testDumpWithoutCSVErrors()
    {
        $errors = $this->csv->load(__DIR__ . '/../_data/stock-without-errors.csv')->dump('foobar', 'a');
        $this->assertEquals([], $errors);
    }

    public function testDumpWithOneCSVError()
    {
        $errors = $this->csv->load(__DIR__ . '/../_data/stock-with-one-error.csv')->dump('foobar', 'a');
        $this->assertEquals(1, count($errors));
    }

}
