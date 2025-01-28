<?php

namespace App\Models;

use CodeIgniter\Model;

class CityModel extends Model
{
	protected $table = 'city';
	protected $allowedFields = [
		'city_id',
		'city_name'
	];
	protected $primaryKey = 'city_id';
}

?>