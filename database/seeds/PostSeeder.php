<?php

use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            "title"=>str_random(),
            "content"=>str_random(300),
            "created_at"=>now(),
            "updated_at"=>now()
        ]);
    }
}
