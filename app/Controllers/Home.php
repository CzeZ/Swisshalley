<?php

namespace App\Controllers;

use Config\App;
use App\Models\CountryModel;
use App\Models\CityModel;
use App\Models\HotelModel;
use CodeIgniter\HTTP\Response as Response;
use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
	use ResponseTrait;

    public function index(): string
    {
		$this->deleteExpiredHotels();
		$session = session();
		$session->remove('filter');

		$filter = $this->request->getPost();

		if (!empty($filter)) {
			$session->set('filter', $filter);
		}

		$page = $this->request->getGet('page');
		if (!empty($page)) {
			$session->set('page', $page);
		} else {
			$session->set('page', 1);
		}

        return view('index');
    }

	public function getHotels() : \CodeIgniter\HTTP\ResponseInterface
	{
		$hotels = $this->getData();
		$countries = $this->getCountries();
		$cities = $this->getCities();

		return $this->respond(['hotels' => $hotels['data'], 'hotelsPager' => $hotels['pager'], 'countries' => $countries, 'cities' => $cities], 200);
	}

	private function getData() : array
	{
		$hotelModel = new HotelModel();

		if ($hotelModel->countAllResults() === 0) {
			$data = $this->getHotelDataFromAPI();

			$hotels = $this->transformDataToArray($data);

			$this->saveDataToDb($hotels);
		}

		$session = session();
		
		if ($session->has('filter')) {
			$filterSession = $session->get('filter');
			$filter = [];
			foreach ($filterSession as $key => $value) {
				if (!empty($value) && !in_array($key, ['type', 'direction'])) {
					$filter[$key] = $value; 
				}
			}

			$result = $hotelModel->where($filter)->orderBy($filterSession['type'], ($filterSession['direction'] ?? 'asc'))->paginate(ROW_PER_PAGE, 'default',  $session->get('page'));
		} else {
			$result = $hotelModel->paginate(ROW_PER_PAGE, 'default', $session->get('page'));
		}

		$pager = $hotelModel->pager;
		$pager->setPath('/');

		return ['data' => $result, 'pager' => $pager->links()];
	}

	private function getHotelDataFromAPI() : Response
	{
		$options = [
			'baseURI' => HOTEL_DATA_URL,
			'timeout' => 6,
		];

		$client = new \CodeIgniter\HTTP\CURLRequest(
			config(App::class),
			new \CodeIgniter\HTTP\URI(),
			new Response(config(App::class)),
			$options
		);

		$client = service('curlrequest');

		$response = $client->request('GET', HOTEL_DATA_URL, [
			'headers' => [
				'X-API-KEY' => HOTEL_DATA_API_KEY,
			]
		]);

		return $response;
	}

	private function transformDataToArray(Response $data) : array
	{
		$dataArray = json_decode($data->getBody())->data->hotels;

		$hotel = new HotelModel();
		$result = $hotel->processArray($dataArray);

		return $result;
	}

	private function saveDataToDb(array $dataArray) : void
	{
		$hotel = new HotelModel();
		$hotel->saveData($dataArray);
	}

	private function getCountries() : array
	{
		$countryModel = new CountryModel();
		$countries = $countryModel->findAll();

		$result = [];
		foreach ($countries as $country) {
			$result[$country['country_id']] = $country['country_name'];
		}

		return $result;
	}

	private function getCities() : array
	{
		$cityModel = new CityModel();
		$cities = $cityModel->findAll();

		$result = [];
		foreach ($cities as $city) {
			$result[$city['city_id']] = $city['city_name'];
		}

		return $result;
	}

	public function deleteExpiredHotels() : void
	{
		$hotel = new HotelModel();
		$hotel->deleteExpiredHotels();
	}
}
