<?php

namespace App\Models;

use CodeIgniter\Model;

class HotelModel extends Model
{
	protected $table = 'hotel';
	protected $allowedFields = [
		'hotel_id',
		'hotel_name',
		'price',
		'country_id',
		'city_id',
		'star',
		'image',
		'created_at'
	];
	protected $primaryKey = 'row_id';

	public function processArray(array $dataArray) : array
	{
		$result = [];
		$country = new CountryModel();
		$city = new CityModel();
		
		foreach ($dataArray as $row) {
			if (isset($result[$row->hotel_id]))
			{
				if ($result[$row->hotel_id]['price'] > ceil($row->price)) {
					$result[$row->hotel_id]['price'] = ceil($row->price);
				}

				if (empty($result[$row->hotel_id]['image']) && !empty($row->image)) {
					$result[$row->hotel_id]['image'] = $row->image;
				}
			}
			else
			{
				$result[$row->hotel_id] = [];
				foreach ($this->allowedFields as $field) {
					if ($field !== 'created_at') {
						$result[$row->hotel_id][$field] = $row->$field;
					}
				}

				if (!$country->find($row->country_id)) {
					$country->insert(['country_id' => $row->country_id, 'country_name' => $row->country]);
				}
				if (!$city->find($row->city_id)) {
					$city->insert(['city_id' => $row->city_id, 'city_name' => $row->city]);
				}
			}
		}

		return $result;
	}

	public function saveData(array $dataArray) : void
	{
		$hotel = new HotelModel();
		foreach($dataArray as $row) {
			$hotel->insert(array_merge($row, ['created_at' => date('Y-m-d H:i:s')]));
		}
	}

	public function deleteExpiredHotels() : \CodeIgniter\Database\BaseResult|bool
	{
		return $this->where('created_at <', date('Y-m-d H:i:s', strtotime('-20 minutes')))->delete();
	}
}

?>