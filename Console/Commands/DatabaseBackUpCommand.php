<?php

declare(strict_types=1);

/**
 * @see https://github.com/paulvl/backup/blob/master/src/Console/Commands/MysqlDump.php
 */

namespace Modules\Xot\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Safe\exec;

use Webmozart\Assert\Assert;

class DatabaseBackUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump your Mysql database to a file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $filename = 'backup-'.Carbon::now()->format('Y-m-d').'.gz';
        $backup_path = storage_path('app/backup/'.$filename);
        Assert::string($backup_path = Str::replace(['/', '\\'], [\DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR], $backup_path), 'wip');

        $command = 'mysqldump --user='.env('DB_USERNAME').' --password='.env('DB_PASSWORD').' --host='.env('DB_HOST').' '.env('DB_DATABASE').'  | gzip > '.$backup_path;

        $returnVar = null;
        $output = null;
        // echo $command;
        exec($command, $output, $returnVar);
    }
}
