<?php

class NWSCombined extends TkJSON
{
	/* ---- Properties ---- */
	private $temperatureApparent = null;

	private $weatherSummary = null;

	private $namePeriod0 = null;

	private $namePeriod1 = null;

	private $namePeriod2 = null;

	private $namePeriod3 = null;

	private $namePeriod4 = null;

	private $tempPeriod0 = null;

	private $tempPeriod1 = null;

	private $tempPeriod2 = null;

	private $tempPeriod3 = null;

	private $tempPeriod4 = null;

	private $weatherPeriod0 = null;

	private $weatherPeriod1 = null;

	private $weatherPeriod2 = null;

	private $weatherPeriod3 = null;

	private $weatherPeriod4 = null;



	public function __construct($RawNWSData)
	{
		foreach($RawNWSData as $datum)
		{
			if($datum['type'] == 'current observations')
			{
				// Set Apparent Temperature
				$this->temperatureApparent = (string)$datum->parameters->temperature->value;

				// Set Weather Conditions Summary
				foreach($datum->parameters->weather->{'weather-conditions'} as $condition)
				{
					if(isset($condition['weather-summary']))
					{
						$this->weatherSummary = (string)$condition['weather-summary'];
						break;
					}
				}
			}
			elseif($datum['type'] == 'forecast')
			{
				$this->weatherPeriod0 = (string)$datum->parameters->weather->{'weather-conditions'}[0]['weather-summary'];
				$this->weatherPeriod1 = (string)$datum->parameters->weather->{'weather-conditions'}[1]['weather-summary'];
				$this->weatherPeriod2 = (string)$datum->parameters->weather->{'weather-conditions'}[2]['weather-summary'];
				$this->weatherPeriod3 = (string)$datum->parameters->weather->{'weather-conditions'}[3]['weather-summary'];
				$this->weatherPeriod4 = (string)$datum->parameters->weather->{'weather-conditions'}[4]['weather-summary'];

				foreach($datum->{'time-layout'} as $timeLayout)
				{
					if((string)$timeLayout->{'layout-key'} == 'k-p12h-n14-1')
					{
						$this->namePeriod0 = (string)$timeLayout->{'start-valid-time'}[0]['period-name'];
						$this->namePeriod1 = (string)$timeLayout->{'start-valid-time'}[1]['period-name'];
						$this->namePeriod2 = (string)$timeLayout->{'start-valid-time'}[2]['period-name'];
						$this->namePeriod3 = (string)$timeLayout->{'start-valid-time'}[3]['period-name'];
						$this->namePeriod4 = (string)$timeLayout->{'start-valid-time'}[4]['period-name'];
						break;
					}
				}
				if(preg_match('/night/i',$this->namePeriod0))
				{
					foreach($datum->parameters->temperature as $temperature)
					{
						if((string)$temperature->name == 'Daily Minimum Temperature')
						{
							$this->tempPeriod0 = (string)$temperature->value[0];
							$this->tempPeriod2 = (string)$temperature->value[1];
							$this->tempPeriod4 = (string)$temperature->value[2];
						}
						elseif((string)$temperature->name == 'Daily Maximum Temperature')
						{
							$this->tempPeriod1 = (string)$temperature->value[0];
							$this->tempPeriod3 = (string)$temperature->value[1];
						}
					}
				}
				else//if($this->namePeriod0 == 'Today')
				{
					foreach($datum->parameters->temperature as $temperature)
					{
						if((string)$temperature->name == 'Daily Minimum Temperature')
						{
							$this->tempPeriod1 = (string)$temperature->value[0];
							$this->tempPeriod3 = (string)$temperature->value[1];
						}
						elseif((string)$temperature->name == 'Daily Maximum Temperature')
						{
							$this->tempPeriod0 = (string)$temperature->value[0];
							$this->tempPeriod2 = (string)$temperature->value[1];
							$this->tempPeriod3 = (string)$temperature->value[2];
						}
					}
				}
			}
		}

		//
	}

	public function jsonSerialize() {

		$return = array(
			'temperatureApparent' => $this->temperatureApparent,
			'weatherSummary' => $this->weatherSummary,
			'namePeriod0' => $this->namePeriod0,
			'tempPeriod0' => $this->tempPeriod0,
			'weatherPeriod0' => $this->weatherPeriod0,
			'namePeriod1' => $this->namePeriod1,
			'tempPeriod1' => $this->tempPeriod1,
			'weatherPeriod1' => $this->weatherPeriod1,
			'namePeriod2' => $this->namePeriod2,
			'tempPeriod2' => $this->tempPeriod2,
			'weatherPeriod2' => $this->weatherPeriod2,
			'namePeriod3' => $this->namePeriod3,
			'tempPeriod3' => $this->tempPeriod3,
			'weatherPeriod3' => $this->weatherPeriod3,
			'namePeriod4' => $this->namePeriod4,
			'tempPeriod4' => $this->tempPeriod4,
			'weatherPeriod4' => $this->weatherPeriod4
		);
		return($return);
	}

} 