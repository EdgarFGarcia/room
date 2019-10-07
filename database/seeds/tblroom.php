<?php

use Illuminate\Database\Seeder;

class tblroom extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('tblroom')->insert([
          [
            'room_no' => '228',
            'room_area_id' => '1',
            'room_status_id' => '17',
            'from_room_status_id' => '3',
            'room_type_id' => '3',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
          [
            'room_no' => '233',
            'room_area_id' => '2',
            'room_status_id' => '1',
            'from_room_status_id' => '17',
            'room_type_id' => '3',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
          [
            'room_no' => '234',
            'room_area_id' => '2',
            'room_status_id' => '17',
            'from_room_status_id' => '3',
            'room_type_id' => '3',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
          [
            'room_no' => '235',
            'room_area_id' => '2',
            'room_status_id' => '1',
            'from_room_status_id' => '3',
            'room_type_id' => '3',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
          [
            'room_no' => '236',
            'room_area_id' => '3',
            'room_status_id' => '17',
            'from_room_status_id' => '3',
            'room_type_id' => '3',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
          [
            'room_no' => '237',
            'room_area_id' => '3',
            'room_status_id' => '17',
            'from_room_status_id' => '3',
            'room_type_id' => '3',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ],
        ]);
    }
}
