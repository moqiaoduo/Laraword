<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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
        $ext=substr(strrchr($file, '.'), 1);
        if(!Storage::disk('theme')->exists($file)) die("文件不存在，无法继续。\nFile doesn't exist, please try another file. \n");
        if($ext!=='zip') die("文件类型不符，无法继续。\nFile extension isn't zip, please try another file. \n");
        $zip = new \ZipArchive;
        $res = $zip->open(storage_path('app/theme/'.$file));
        $toDir = storage_path('app/theme/tmp');
        @mkdir($toDir);
        $s = $zip->extractTo($toDir);
        print_r(scandir($toDir));
        echo "\n";
    }
}
