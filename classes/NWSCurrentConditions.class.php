<?php

class NWSCurrentConditions extends TkJSON
{
	/* ---- Properties ---- */
	public $humidityRelative = null;

	public $temperatureApparent = null;

	public $temperatureDewPoint = null;

	public $weatherSummary = null;

	public function __construct($RawNWSCurrentConditionsData)
	{
		$this->temperatureApparent = (string)$RawNWSCurrentConditionsData->parameters->temperature->value;

		// Weather Conditions Summary
		foreach($RawNWSCurrentConditionsData->parameters->weather->{'weather-conditions'} as $condition)
		{
			if(isset($condition['weather-summary']))
			{
				$this->weatherSummary = (string)$condition['weather-summary'];
				break;
			}
		}

		//
	}

	public function jsonSerialize() {

		$return = array(
			'temperatureApparent' => $this->temperatureApparent,
			'weatherSummary' => $this->weatherSummary
		);
		return($return);
	}

} 