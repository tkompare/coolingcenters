<?php
/**
 * Class NWSData
 */
class NWSData
{

	/* ---- INPUT PARAMETERS ---- */

	/**
	 * @var float
	 */
	private $lat = null;

	/**
	 * @var float
	 */
	private $lng = null;

	/* ---- BOUNDARIES OF LAT LNG ---- */
	/**
	 * @var float
	 */
	private $latMin = 20.19;
	/**
	 * @var float
	 */
	private $latMax = 50.11;
	/**
	 * @var float
	 */
	private $lngMin = -130.11;
	/**
	 * @var float
	 */
	private $lngMax = -60.87;

	/* ---- URL PROPERTIES ---- */

	/**
	 * @var string
	 */
	private $urlPrefix = 'http://forecast.weather.gov/MapClick.php?';

	/**
	 * @var string
	 */
	private $urlLat = 'lat=';

	/**
	 * @var string
	 */
	private $urlLng = '&lon=';

	/**
	 * @var string
	 */
	private $urlSuffix = '&unit=0&lg=english&FcstType=dwml';

	/**
	 * @var string
	 */
	private $url = null;

	/* ---- XML OBJECT ---- */

	/**
	 * @var object
	 */
	private $TheData = null;

	/* ---- PUBLIC FUNCTIONS ---- */

	/**
	 * @param $lat
	 * @param $lng
	 * @throws Exception
	 */
	public function __construct($lat,$lng)
	{
		// Place the parameters into properties
		$this->lat = floatval($lat);
		$this->lng = floatval($lng);

		// Check parameters for validity
		$isValidInput = $this->isValidInput();

		if($isValidInput)
		{
			// Piece together the URL
			$this->constructURL();

			// Make the call to NWS for the data
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $this->url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$resp = curl_exec($ch);
			$this->TheData = new SimpleXMLElement($resp);

			// check if the response is empty
			if (empty($this->TheData))
			{
				throw new \Exception('Empty response from the National Weather Service.');
			}

			// check if the response is an error message
			if ($this->TheData->h2 == 'ERROR')
			{
				throw new \Exception('Error response from the National Weather Service.');
			}
		}
		else
		{
			return false;
		}
	}

	public function getTheData()
	{
		return $this->TheData;
	}

	public function getCurrentConditions()
	{
		//print_r($this->TheData->data);

		foreach($this->TheData->data as $datum)
		{
			if($datum['type'] == 'current observations')
			{
				return $datum;
			}
		}

		//return $this->TheData->data[1]->parameters;
	}

	public function getCurrentTemperature()
	{
		return $this->TheData->data[1]->parameters->temperature->value;
	}

	/* ---- PRIVATE FUNCTIONS --- */

	/**
	 * @return bool
	 */
	private function isValidInput()
	{
		if(
				is_float($this->lat)
				&& is_float($this->lng)
				&& ($this->latMin <= $this->lat)
				&& ($this->lat <= $this->latMax)
				&& ($this->lngMin <= $this->lng)
				&& ($this->lng <= $this->lngMax)
		)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function constructURL()
	{
		$this->url = $this->urlPrefix . $this->urlLat . $this->lat . $this->urlLng . $this->lng . $this->urlSuffix;
	}
} 