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
	private $lat = NULL;

	/**
	 * @var float
	 */
	private $lng = NULL;

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
	private $url = NULL;

	/* ---- XML OBJECT ---- */

	/**
	 * @var object
	 */
	private $TheData = NULL;

	/* ---- PUBLIC FUNCTIONS ---- */

	/**
	 * @param $lat
	 * @param $lng
	 *
	 * @throws Exception
	 */
	public function __construct($lat, $lng)
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
			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			$resp = curl_exec($ch);
			$this->TheData = new SimpleXMLElement($resp);

			// check if the response is empty
			if(empty($this->TheData))
			{
				header($_SERVER['SERVER_PROTOCOL'].' 503 Service Unavailable: Empty response from the National Weather Service.', TRUE, 503);
				exit;
			}

			// check if the response is an error message
			if($this->TheData->h2 == 'ERROR')
			{
				header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error: Error response from the National Weather Service.', TRUE, 500);
				exit;
			}
		}
		else
		{
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request: Requires valid \'lat\' and \'lng\' GET parameters', TRUE, 400);
			exit;
		}
	}

	public function getTheData()
	{
		return $this->TheData;
	}

	public function getCombined()
	{
		return $this->TheData->data;
	}

	public function getCurrentConditions()
	{
		foreach($this->TheData->data as $datum)
		{
			if($datum['type'] == 'current observations')
			{
				return $datum;
			}
		}
	}

	/* ---- PRIVATE FUNCTIONS --- */

	/**
	 * @return bool
	 */
	private function isValidInput()
	{
		if(is_float($this->lat) && is_float($this->lng) && ($this->latMin <= $this->lat) && ($this->lat <= $this->latMax) && ($this->lngMin <= $this->lng) && ($this->lng <= $this->lngMax)
		)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	private function constructURL()
	{
		$this->url = $this->urlPrefix.$this->urlLat.$this->lat.$this->urlLng.$this->lng.$this->urlSuffix;
	}
} 