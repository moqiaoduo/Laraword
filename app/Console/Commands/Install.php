<?php

namespace App\Console\Commands;

use App\Content;
use App\Meta;
use App\User;
use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraword:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '安装程序';

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
        $name=$this->ask("您的名字？ What's your name? ","Laraword");
        $email=$this->ask("您的电子邮箱？ What's your email? ","admin@laraword.cn");
        password:
        $password=$this->secret("您的密码？ What's your password? ");
        if(empty($password)) goto password;
        $category=new Meta;
        $category->name="默认分类";
        $category->slug="default";
        $category->type="category";
        $category->description="这是一个默认分类";
        $category->count=1;
        $category->save();
        $post=new Content;
        $post->title="您的第一篇文章";
        $post->slug="the-first-post";
        $post->content="这是您的第一篇博文";
        $post->uid=1;
        $post->save();
        $page=new Content;
        $page->title="您的第一个页面";
        $page->slug="the-first-page";
        $page->content="这是您的第一个页面";
        $page->uid=1;
        $page->type="page";
        $page->save();
        \DB::table('relationships')->insert(["cid"=>1,"mid"=>1]);
        $user=new User;
        $user->name=$name;
        $user->email=$email;
        $user->password=bcrypt($password);
        $user->is_admin=1;
        $user->save();
        recurse_copy(config_path('init_settings'),config_path('settings'));
        $this->comment('安装完成。 Install completely.');
    }
}
