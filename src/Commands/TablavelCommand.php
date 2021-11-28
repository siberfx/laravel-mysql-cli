<?php

namespace TheCodeRepublic\Tablavel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TablavelCommand extends Command
{
    protected $signature = 'tablavel';

    protected $description = 'Laravel Database Client CLI';

    const ERR_NO_ACTION = "No action selected.";
    const ERR_NO_TABLE = "No table selected. Cmd: /use {tablename}";

    public function __construct()
    {
        parent::__construct();
    }

    private $currentTable = "";

    private $cmds = [
        '/t' => [
            'description' => 'Show all tables',
            'action' => 'cmdShowTables'
        ],
        '/u' => [
            'description' => ' {tablename} - use a table',
            'action' => 'cmdUseTable'
        ],
        '/q' => [
            'description' => '/q quit',
            'action' => 'cmdQuit'
        ],
    ];

    private $tableCmds = [
        '/c' => [
            'description' => 'Show all columns',
            'action' => 'cmdShowColumns'
        ],
        '/l' => [
            'description' => 'Show last inserted id',
            'action' => 'cmdShowLastRecords'
        ],
        '/f' => [
            'description' => 'Show first row',
            'action' => 'cmdShowFirstRecords'
        ],
        '/b' => [
            'description' => 'Back to main menu',
        ],
    ];


    public function handle()
    {
        system('clear');
        $this->header();

        while (true) :

            $rawInput = explode(' ', $this->ask('Your command is my command'));
            $cmd = $rawInput[0];

            $par = "";
            $par = (isset($rawInput[1])) ? $rawInput[1] : "";

            if (array_key_exists($cmd, $this->cmds)) {
                $this->{$this->cmds[$cmd]['action']}($par);
            } else {
                $this->header();
                $this->error(self::ERR_NO_ACTION);
            }

        endwhile;
    }

    private function menu()
    {
        foreach ($this->cmds as $cmd => $cmdDetails) {
            $this->warn($cmd . " " . $cmdDetails['description']);
        }
    }

    private function tableMenu()
    {
        foreach ($this->tableCmds as $cmd => $cmdDetails) {
            $this->warn($cmd . " " . $cmdDetails['description']);
        }
    }

    private function header($showTableMenu = false)
    {
        $this->newLine();

        $this->info('+-------------------------------+');
        $this->info('|            Tablavel           | Database CLI Client for Laravel');
        $this->info('+-------------------------------+');
        $this->newLine();

        if ($showTableMenu) {
            $this->tableMenu();
        } else {
            $this->menu();
        }


        $this->newLine();
    }

    public function cmdShowTables()
    {
        system('clear');

        $tables = $this->getNamesTablesDB();

        $this->table(['tableName'], $tables);

        $this->newLine();

        $this->menu();

        $this->newLine();
    }

    public function cmdShowColumns()
    {
        system('clear');
        $columns = $this->getColumnsDB();

        $this->table(
            ['columnName', 'type', 'null', 'Key', 'Default', 'Extra'],
            $columns
        );
    }

    public function cmdUseTable($par)
    {

        system('clear');

        if (!$par) {
            $this->error(self::ERR_NO_TABLE);
        }

        $this->currentTable = $par;

        while (true) :
            $this->header(true);
            $cmd = $this->ask('Your command is my command');

            if ('/b' == $cmd) {
                system('clear');
                $this->header();
                break;
            }

            if (array_key_exists($cmd, $this->tableCmds)) {
                $this->{$this->tableCmds[$cmd]['action']}($par);
            } else {
                $this->error(self::ERR_NO_ACTION);
            }
        endwhile;
    }

    public function cmdShowLastRecords()
    {
        $className = 'App\\Models\\' . Str::studly(Str::singular($this->currentTable));

        if ( ! class_exists($className) ) {
            $tableName = $this->currentTable;
            throw new \Exception("Class $className of $tableName not found");
        }

        $records = $className::orderBy('id', 'DESC')->limit(10)->get();

        foreach($records as $record) {
            $result[] = $record->toArray();
        }

        $this->table(
            $this->getTableColumns($this->currentTable),
            $result
        );
    }

    public function cmdShowFirstRecords()
    {
        $className = 'App\\Models\\' . Str::studly(Str::singular($this->currentTable));

        if ( ! class_exists($className) ) {
            $tableName = $this->currentTable;
            throw new \Exception("Class $className of $tableName not found");
        }

        $records = $className::orderBy('id', 'ASC')->limit(10)->get();

        foreach($records as $record) {
            $result[] = $record->toArray();
        }

        $this->table(
            $this->getTableColumns($this->currentTable),
            $result
        );
    }

    public function getTableColumns($tableName)
    {
        $columnsQueryResult = DB::select('SHOW columns FROM ' . $tableName);

        foreach ($columnsQueryResult as $columnResult) {
            $columns[] = substr($columnResult->Field, 0, 1)  . "." . substr($columnResult->Field, -1);
        }
        return $columns;
    }

    /**
     * Connects to the active database in config and gets the tables
     * @return array
     */
    private function getNamesTablesDB(): array
    {

        $database = Config::get('database.connections.mysql.database');

        if ("" == $database) {
            throw new \Exception("You dont have a database in config [database][connections][mysql][database]");
        }

        $tablesQueryResult = DB::select('SHOW TABLES');

        if (!$tablesQueryResult) {
            throw new \Exception("You dont have tables in database [$database]");
        }

        $combine = "Tables_in_" . $database;


        foreach ($tablesQueryResult as $tableResult) {
            $tables[] = ['table name' => $tableResult->$combine];
        }

        return $tables;
    }

    private function getColumnsDB(): array
    {
        $database = Config::get('database.connections.mysql.database');

        if ("" == $database) {
            throw new \Exception("You dont have a database in config [database][connections][mysql][database]");
        }

        $columnsQueryResult = DB::select('SHOW columns FROM ' . $this->currentTable);

        if (!$columnsQueryResult) {
            throw new \Exception("You dont have tables in database [$database]");
        }

        foreach ($columnsQueryResult as $columnResult) {
            $columns[] = [
                'column name' => $columnResult->Field,
                'type'  => $columnResult->Type,
                'null' => $columnResult->Null,
                'Key' => $columnResult->Key,
                'Default' => $columnResult->Default,
                'Extra' => $columnResult->Extra,
            ];
        }
        return $columns;
    }


    private function cmdQuit($par = "")
    {
        system('clear');
        exit();
    }
}
