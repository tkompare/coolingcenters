<?php

class NWSCurrentConditions extends TkJSON
{
	/* ---- Properties ---- */
	private $temperatureApparent = null;

	private $weatherSummary = null;

	public function __construct($RawNWSCurrentConditionsData)
	{
		// Set Apparent Temperature
		$this->temperatureApparent = (string)$RawNWSCurrentConditionsData->parameters->temperature->value;

		// Set Weather Conditions Summary
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