<?php

use Illuminate\Database\Seeder;
use App\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
               'name'=>'Admin',
               'email'=>'weijunlam1@gmail.com',
               'position'=>'admin',
               'status'=>'Active',
               'email_verified_at'=>'2020-06-30 00:00:00.000000',
               'password'=> bcrypt('password'),
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
