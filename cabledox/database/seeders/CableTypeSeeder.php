<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CableTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$cableType = [
    		[
    			'cable_name'       => 'Power Cable',
    			'cable_identifier' => 'P',
	    		'status'           => 1,
	    	],
	    	[
    			'cable_name'       => 'Control Cable',
    			'cable_identifier' => 'C',
	    		'status'           => 1,
	    	],
	    	[
    			'cable_name'       => 'Instrument Cable',
    			'cable_identifier' => 'I',
	    		'status'           => 1,
	    	],
	    	[
    			'cable_name'       => 'Earth Cable',
    			'cable_identifier' => 'E',
	    		'status'           => 1,
	    	],
    	];

    	foreach ($cableType as $k => $type) {
    		DB::table('cable_types')->insert($type);
    	}
    }
}