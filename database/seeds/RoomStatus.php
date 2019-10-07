<?php

use Illuminate\Database\Seeder;

class RoomStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('roomstatus')->insert([
          [
        		'room_status' => 'Clean',
        		'color' => '#7dff44',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Occupied',
        		'color' => '#d60930',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Dirty',
        		'color' => '#009cff',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'On-Going RC With Waiting Guest',
        		'color' => '#ffea00',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'STL (Stop The Line)',
        		'color' => '#663300',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'On-Going General Cleaning',
        		'color' => '#ffea00',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'For Inspection',
        		'color' => '#551A8B',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Linen Setup',
        		'color' => '#e2e2e2',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Re-Clean',
        		'color' => '#009cff',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Cancel Negotiation',
        		'color' => '#808080',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Cancel Waiting Guest',
        		'color' => '#3cbb54',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Reserved',
        		'color' => '#FFA500',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'On-Going Inspection',
        		'color' => '#551A8B',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'For Preventive Maintenance',
        		'color' => '#e5e5e5',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Renovation',
        		'color' => '#000000',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Not Inspected Clean',
        		'color' => '#7dff44',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'SOA',
        		'color' => '#d60930',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'Cancelled Room',
        		'color' => '#551A8B',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'On-Going RC',
        		'color' => '#ffea00',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'On-Going Negotiation',
        		'color' => '#d60930',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
        	[
        		'room_status' => 'For General Cleaning',
        		'color' => '#009cff',
        		'created_at' => date('Y-m-d H:i:s'),
        		'updated_at' => date('Y-m-d H:i:s'),
        	],
            [
                'room_status' => 'On-Going Rectification',
                'color' => '#663300',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => 'Rectified',
                'color' => '##663300',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => 'On-Going Maintenance',
                'color' => '#e5e5e5',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => '24 Hours [Clean]',
                'color' => '#009cff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => '24 Hours [Dirty]',
                'color' => '#009cff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => 'Hotelified',
                'color' => '#7dff44',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => 'Inspected Clean',
                'color' => '#7dff44',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => 'Set Waiting Guest',
                'color' => '#fffff0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => 'By Passed Reservation',
                'color' => '#7dff44',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => 'Dirty With Linen',
                'color' => '#009cff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'room_status' => 'Dirty with Waiting Guest',
                'color' => '#009cff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
