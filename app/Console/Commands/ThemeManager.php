<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ThemeManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:install {package}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '安装主题包';

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
        $file=$this->argument('package');
        $this->comment(installTheme($file));
        if($this->confirm('Would you like to delete zip file? 是否删除主题安装包？(Y/N)')) @unlink(storage_path('app/theme/'.$file));
    }
}
