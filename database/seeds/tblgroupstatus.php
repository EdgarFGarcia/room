<?php

use Illuminate\Database\Seeder;

class tblgroupstatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('tblroomgroupstatus')->insert([
          [
            'room_status_id' => '1',
            'available_status_id' => '2',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
          [
            'room_status_id' => '1',
            'available_status_id' => '13',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
          [
            'room_status_id' => '1',
            'available_status_id' => '14',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
        ]);
    }
}
