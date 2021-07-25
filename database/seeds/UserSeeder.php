<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'username' => 'admin',
            'password' => bcrypt(12345678),
            'level' => 'Admin',
            'id_posko' => 1
        ];

        $user = User::create($user);
        $user->createToken(env('PASSPORT_KEY_APP'))->accessToken;

    }
}
