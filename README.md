## JordiCSV

This is a scalable, simple tool intended to perform CSV operations while interacting with a database. The main idea consists in loading a CSV file into memory -- in the form of an array -- for further processing. At this moment it is only implementing the dumping of a CSV file into a MySQL database.

### 1. Setting up the database

Please open the `src\config\prod.php` file and define the global constants of your choice as it is shown below:

```php
<?php
define('DB_SERVER', 'localhost');
define('DB_NAME', 'jordi_csv_prod');
define('DB_USER', 'jordi_csv');
define('DB_PASSWORD', 'fCVdsH_v5BpExPa43_fda');
```

The following MySQL script will help you create the `jordi_csv_prod` database as well as the user required for the program to run:

    CREATE DATABASE jordi_csv_prod;

    USE jordi_csv_prod;

    GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES, CREATE TEMPORARY TABLES
    ON jordi_csv_prod.* TO 'jordi_csv'@'localhost' IDENTIFIED BY 'fCVdsH_v5BpExPa43_fda';

### 2. Running the application

#### 2.1. Parameters

**file name**&nbsp;The CSV file name with the .csv extension. The file needs to be located in the `files` folder.

**table name**&nbsp;This is the name of the table where the data will be dumped. The program automatically creates the table for you.

**mode** `a` is for appending CSV rows to an existing table, and `w` is for writing CSV rows.

#### 2.2. Example

Just place your CSV file into the `files` folder and run the example as shown below:

    php example-01.php stock.csv foobar w

The program will generate this output:

    OK. The file has been successfully dumped into the database!
    However, the following invalid rows were found:
    P0017, CPU, Processing power,  ideal for multimedia, 4, 4.22
    Open the file with your favorite editor, fix the errors and try again.

Cool! The file has been successfully dumped into the database, but there's one line which is not valid:

    P0017,CPU,Processing power, ideal for multimedia,4,4.22,

Certainly, note how the last comma (,) needs to be removed at the end of the line:

    P0017,CPU,Processing power, ideal for multimedia,4,4.22

Anyway, the `foobar` table will be automatically created and it will look like this:

    mysql> select * from foobar;
    +--------------+---------------+--------------------------------------+-------+-------------+--------------+
    | product_code | product_name  | product_description                  | stock | cost_in_gbp | discontinued |
    +--------------+---------------+--------------------------------------+-------+-------------+--------------+
    | P0001        | TV            | 32? Tv                               | 10    | 399.99      | NULL         |
    | P0002        | Cd Player     | Nice CD player                       | 11    | 50.12       | yes          |
    | P0003        | VCR           | Top notch VCR                        | 12    | 39.33       | yes          |
    | P0004        | Bluray Player | Watch it in HD                       | 1     | 24.55       | NULL         |
    | P0005        | XBOX360       | Best.console.ever                    | 5     | 30.44       | NULL         |
    | P0006        | PS3           | Mind your details                    | 3     | 24.99       | NULL         |
    | P0007        | 24? Monitor   | Awesome                              | NULL  | 35.99       | NULL         |
    | P0008        | CPU           | Speedy                               | 12    | 25.43       | NULL         |
    | P0009        | Harddisk      | Great for storing data               | NULL  | 99.99       | NULL         |
    | P0010        | CD Bundle     | Lots of fun                          | NULL  | 10          | NULL         |
    | P0011        | Misc Cables   | error in export                      | NULL  | NULL        | NULL         |
    | P0012        | TV            | HD ready                             | 45    | 50.55       | NULL         |
    | P0013        | Cd Player     | Beats MP3                            | 34    | 27.99       | NULL         |
    | P0014        | VCR           | VHS rules                            | 3     | 23          | yes          |
    | P0015        | Bluray Player | Excellent picture                    | 32    | $4.33       | NULL         |
    | P0015        | Bluray Player | Excellent picture                    | 32    | 4.33        | NULL         |
    | P0016        | 24? Monitor   | Visual candy                         | 3     | 45          | NULL         |
    | P0018        | Harddisk      | More storage options                 | 34    | 50          | yes          |
    | P0019        | CD Bundle     | Store all your data. Very convenient | 23    | 3.44        | NULL         |
    | P0020        | Cd Player     | Play CD's                            | 56    | 30          | NULL         |
    | P0021        | VCR           | Watch all those retro videos         | 12    | 3.55        | yes          |
    | P0022        | Bluray Player | The future of home entertainment!    | 45    | 3           | NULL         |
    | P0023        | XBOX360       | Amazing                              | 23    | 50          | NULL         |
    | P0024        | PS3           | Just don't go online                 | 22    | 24.33       | yes          |
    | P0025        | TV            | Great for television                 | 21    | 40          | NULL         |
    | P0026        | Cd Player     | A personal favourite                 | NULL  | 34.55       | NULL         |
    | P0027        | VCR           | Plays videos                         | 34    | 1200.03     | yes          |
    | P0028        | Bluray Player | Plays bluray's                       | 32    | 1100.04     | yes          |
    +--------------+---------------+--------------------------------------+-------+-------------+--------------+
    28 rows in set (0.00 sec)

In order to fix the CSV errors, create a new file named `\files\stock-fixed-errors.csv` with the following content:

    Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued
    P0017,CPU,Processing power, ideal for multimedia,4,4.22

Then, append the fixed CSV rows to the `foobar` table:

    php example-01.php stock-fixed-errors.csv foobar a

Now the program will respond like this:

    OK. The file has been successfully dumped into the database!

And this is how the `foobar` table will look like:

    mysql> select * from foobar;
    +--------------+---------------+--------------------------------------+-----------------------+-------------+--------------+
    | product_code | product_name  | product_description                  | stock                 | cost_in_gbp | discontinued |
    +--------------+---------------+--------------------------------------+-----------------------+-------------+--------------+
    | P0001        | TV            | 32? Tv                               | 10                    | 399.99      | NULL         |
    | P0002        | Cd Player     | Nice CD player                       | 11                    | 50.12       | yes          |
    | P0003        | VCR           | Top notch VCR                        | 12                    | 39.33       | yes          |
    | P0004        | Bluray Player | Watch it in HD                       | 1                     | 24.55       | NULL         |
    | P0005        | XBOX360       | Best.console.ever                    | 5                     | 30.44       | NULL         |
    | P0006        | PS3           | Mind your details                    | 3                     | 24.99       | NULL         |
    | P0007        | 24? Monitor   | Awesome                              | NULL                  | 35.99       | NULL         |
    | P0008        | CPU           | Speedy                               | 12                    | 25.43       | NULL         |
    | P0009        | Harddisk      | Great for storing data               | NULL                  | 99.99       | NULL         |
    | P0010        | CD Bundle     | Lots of fun                          | NULL                  | 10          | NULL         |
    | P0011        | Misc Cables   | error in export                      | NULL                  | NULL        | NULL         |
    | P0012        | TV            | HD ready                             | 45                    | 50.55       | NULL         |
    | P0013        | Cd Player     | Beats MP3                            | 34                    | 27.99       | NULL         |
    | P0014        | VCR           | VHS rules                            | 3                     | 23          | yes          |
    | P0015        | Bluray Player | Excellent picture                    | 32                    | $4.33       | NULL         |
    | P0015        | Bluray Player | Excellent picture                    | 32                    | 4.33        | NULL         |
    | P0016        | 24? Monitor   | Visual candy                         | 3                     | 45          | NULL         |
    | P0018        | Harddisk      | More storage options                 | 34                    | 50          | yes          |
    | P0019        | CD Bundle     | Store all your data. Very convenient | 23                    | 3.44        | NULL         |
    | P0020        | Cd Player     | Play CD's                            | 56                    | 30          | NULL         |
    | P0021        | VCR           | Watch all those retro videos         | 12                    | 3.55        | yes          |
    | P0022        | Bluray Player | The future of home entertainment!    | 45                    | 3           | NULL         |
    | P0023        | XBOX360       | Amazing                              | 23                    | 50          | NULL         |
    | P0024        | PS3           | Just don't go online                 | 22                    | 24.33       | yes          |
    | P0025        | TV            | Great for television                 | 21                    | 40          | NULL         |
    | P0026        | Cd Player     | A personal favourite                 | NULL                  | 34.55       | NULL         |
    | P0027        | VCR           | Plays videos                         | 34                    | 1200.03     | yes          |
    | P0028        | Bluray Player | Plays bluray's                       | 32                    | 1100.04     | yes          |
    | P0017        | CPU           | Processing power                     |  ideal for multimedia | 4           | 4.22         |
    +--------------+---------------+--------------------------------------+-----------------------+-------------+--------------+
    29 rows in set (0.00 sec)
