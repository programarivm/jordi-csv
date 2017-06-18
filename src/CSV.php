<?php
namespace JordiCSV;

use ICanBoogie\Inflector;

/**
* CSV class.
*
* A simple CSV class intended to perform CSV operations interacting with a MySQL
* database. At this moment only dumps the data into the database.
*/
class CSV
{
    /**
     * Max size of the table's columns.
     */
    const COLUMN_SIZE = 256;

    /**
    * Database object.
    *
    * @var \CSVDump\DB Database handler.
    */
    private $db;

    /**
     * The table name.
     *
     * @var string
     */
    private $tablename;

    /**
     * The CSV representation in memory as an array.
     *
     * @var array
     */
    private $csv;

    /**
     * The CSV rows that are not correct.
     *
     * @var array
     */
    private $errors;

    /**
     * Constructor
     *
     * @param JordiCSV\DB $db
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->csv = [
            'header' => [],
            'data' => []
        ];
        $this->errors = [];
    }

    /**
     * Gets the CSV data from memory.
     *
     * @return array
     */
    public function getCsv()
    {
        return $this->csv;
    }

    /**
     * Loads the CSV into memory in the form of an array.
     *
     * @param  string $file
     *
     * @return JordiCSV\CSV
     */
    public function load($file=null)
    {
        $this->validateFile($file) ? $this->file = $file : false;

        $inflector = Inflector::get();

        if (($handle = fopen($this->file, 'r')) !== FALSE)
        {
            $i = 0;

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE)
            {
                if ($i == 0)
                {
                    $this->csv['header'] = $row;

                    foreach($row as $key => $val)
                    {
                        $this->csv['header'][$key] = str_replace(' ', '_', $inflector->underscore($val));
                    }
                }
                else
                {
                    $this->csv['data'][] = $row;
                }

                $i++;
            }
        }

        return $this;
    }

    /**
     * Dumps the CSV data into the given table.
     *
     * @param string    $tablename
     * @param string    $mode a (append) or w (write)
     *
     * @return mixed    boolean|array false if the dump can't be performed;
     *                  otherwise it returns an array of errors containing
     *                  the CSV rows that couldn't be inserted into the database
     */
    public function dump($tablename=null, $mode=null)
    {
        if ($this->validateTable($tablename) && $this->validateMode($mode))
        {
            $this->tablename = $tablename;
            $this->createTable($mode);
            $errors = $this->insertRows();
            return $errors;
        }
        else
        {
            return false;
        }
    }

    /**
     * Creates a new table.
     *
     * @param string $mode
     *
     * @return mixed boolean|mysqli_result
     */
    private function createTable($mode)
    {
        $result = $this->db->query("SHOW TABLES LIKE '" . $this->tablename . "'");

        if ($result->num_rows == 0) // the table doesn't exist
        {
            $sql = 'CREATE TABLE ' . $this->tablename . ' (';

            foreach($this->csv['header'] as $key => $val)
            {
                $sql .= $val . ' varchar(' . self::COLUMN_SIZE . '), ';
            }

            $sql = rtrim($sql, ', ') . ')';
            $result = $this->db->query($sql);

            if($result == false)
            {
                throw new \InvalidArgumentException("The table {$this->tablename} could not be created, please try another name.");
            }

            return $result;
        }
        else if ($mode == 'w') // the table already exists in writing mode
        {
            return $this->db->query("DELETE FROM " . $this->tablename);
        }
        else if ($mode == 'a') // the table already exists in append mode
        {
            return true;
        }
    }

    /**
     * Tries to insert the CSV rows into the database; the invalid ones are
     * stored into the $this->errors array.
     *
     * @return array
     */
    private function insertRows()
    {
        $sqlInsert = 'INSERT INTO ' . $this->tablename . ' (';

        foreach($this->csv['header'] as $key => $value)
        {
            $sqlInsert .= "$value, ";
        }

        $sqlInsert = rtrim($sqlInsert, ', ') . ') VALUES (';

        foreach($this->csv['data'] as $row)
        {
            $query = $sqlInsert;

            foreach($row as $key => $value)
            {
                $value = $this->db->escape($value);
                !empty($value) ? $query .= "'$value', " : $query .= 'null, ';
            }

            $query = rtrim($query, ', ') . ')';
            $this->db->query($query) ? false : $this->errors[] = $row;
        }

        return $this->errors;
    }

    /**
     * Validates the given file.
     *
     * @param string $file
     *
     * @return boolean
     */
    private function validateFile($file=null)
    {
        if(empty($file))
        {
            throw new \InvalidArgumentException('A file needs to be specified.');
        }

        if(!file_exists($file))
        {
            throw new \InvalidArgumentException('The file does not exist in the jordi-csv\files folder.');
        }

        if(!mb_check_encoding(file_get_contents($file), 'UTF-8'))
        {
            throw new \InvalidArgumentException("The file has to be encoded in UTF-8 format.");
        }

        return true;
    }

    /**
     * Validates the given table name.
     *
     * @param string $tablename
     *
     * @return boolean
     */
    private function validateTable($tablename=null)
    {
        if(empty($tablename))
        {
            throw new \InvalidArgumentException('A table name needs to be specified.');
        }

        return true;
    }

    /**
     * Validates the given mode.
     *
     * @param string $mode
     *
     * @return boolean
     */
    private function validateMode($mode=null)
    {
        if($mode !== 'a' && $mode !== 'w')
        {
            throw new \InvalidArgumentException('Please specify the mode parameter: a (append) or w (write).');
        }

        return true;
    }

}
