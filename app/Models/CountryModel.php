<?php

namespace App\Models;

use CodeIgniter\Model;

class CountryModel extends Model
{
	protected $table = 'country';
	protected $allowedFields = [
		'country_id',
		'country_name'
	];
	protected $primaryKey = 'country_id';
}

?>