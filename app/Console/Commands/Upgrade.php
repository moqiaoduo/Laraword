<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class Upgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraword:upgrade {class} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '执行更新程序';

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
     * @return mixed
     */
    public function handle()
    {
        $class=$this->argument('class');

        $continue=true;

        if(!$this->option('force')){
            $continue=!DB::table('upgrade_record')->where('class',$class)->exists();
        }

        if($continue){
            $class="\App\Upgrade\\".$class;
            (new $class)->run();
            $this->info($class."的更新已经完成");
            DB::table('upgrade_record')->insert(["class"=>$class]);
        }else $this->error('您已执行过此更新，请不要重复执行。');
    }
}
