<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default user
        DB::table('users')->insert([
            'full_name' => "Concepteur de l'application",
            'email' => 'Concepteur@app.com',
            'login' => 'Concepteur',
            'contact' => '00000000',
            'role' => 'Concepteur',
            'password' => bcrypt('P@ssword@123456'),
            'created_at' => now()
          ]);
          // My user
          DB::table('users')->insert([
            'full_name' => "Alexandre TAHI",
            'email' => 'alexandretahi7@gmail.com',
            'login' => 'Alex',
            'contact' => '00000000',
            'role' => 'Concepteur',
            'password' => bcrypt('Alex'),
            'created_at' => now()
          ]);
          // aLFRED KOUANSAN
          DB::table('users')->insert([
            'full_name' => "Alfred KOUANSAN",
            'email' => 'alfredkouansan@groupsmarty.com',
            'login' => 'AlfredKGS',
            'contact' => '00000000',
            'role' => 'Concepteur',
            'password' => bcrypt('GroupSmarty0110'),
            'created_at' => now()
          ]);
          // Account 1 for test
          DB::table('users')->insert([
            'full_name' => "Beta testeur one",
            'email' => 'alexandre.tahi18@inphb.ci',
            'login' => 'betaone',
            'contact' => '00000000',
            'role' => 'Testeur',
            'password' => bcrypt('1234'),
            'created_at' => now()
          ]);
          // Account 1 for test
          DB::table('users')->insert([
            'full_name' => "Beta testeur two",
            'email' => 'alexandre.tahi19@inphb.ci',
            'login' => 'betatwo',
            'contact' => '00000000',
            'role' => 'Testeur',
            'password' => bcrypt('1234'),
            'created_at' => now()
          ]);
    }
}
