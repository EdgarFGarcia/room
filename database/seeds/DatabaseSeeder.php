<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(StatusTableSeeder::class);
        $this->call(Roles::class);
        // $this->call(roomarea::class);
        // $this->call(RoomStatus::class);
        // $this->call(roomtype::class);
        // $this->call(source::class);
        // $this->call(tblgrproomtype::class);
        // $this->call(tblroom::class);
        // $this->call(tblgroupstatus::class);
    }
}
