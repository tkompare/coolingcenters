<?php

class NWSCombined extends TkJSON
{
	/* ---- Properties ---- */

	// Current conditions
	private $temperatureApparent = NULL;
	private $weatherSummary = NULL;
	private $currentDay = NULL;


	//How many forecast periods?
	private $numPeriods = 10;

	// What forecast attributes are being collected from the NWS API?
	private $Name = array();
	private $DayName = array();
	private $Temperature = array();
	private $Weather = array();

	public function __construct($RawNWSData)
	{
		date_default_timezone_set('America/Chicago');

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

				// Set current day
				$this->currentDay = date('l|h:i A');
			}
			elseif($datum['type'] == 'forecast')
			{
				for($i = 0; $i < $this->numPeriods; $i++)
				{
					$this->Weather[$i] = (string)$datum->parameters->weather->{'weather-conditions'}[$i]['weather-summary'];
				}

				foreach($datum->{'time-layout'} as $timeLayout)
				{
					if((string)$timeLayout->{'layout-key'} == 'k-p12h-n15-1' || (string)$timeLayout->{'layout-key'} == 'k-p12h-n14-1' || (string)$timeLayout->{'layout-key'} == 'k-p12h-n13-1'
					)
					{
						for($i = 0; $i < $this->numPeriods; $i++)
						{
							$this->DayName[$i] = date('l|h:i A', strtotime($timeLayout->{'start-valid-time'}[$i]));
							$this->Name[$i] = (string)$timeLayout->{'start-valid-time'}[$i]['period-name'];
						}

						break;
					}
				}


				if(preg_match('/night/i', $this->Name[0])) // matches 'Tonight' and 'Overnight'
				{
					foreach($datum->parameters->temperature as $temperature)
					{
						if((string)$temperature->name == 'Daily Minimum Temperature')
						{
							$j = 0;
							for($i = 0; $i < $this->numPeriods; $i = $i+2)
							{
								$this->Temperature[$i] = (string)$temperature->value[$j];
								$j++;
							}
						}
						elseif((string)$temperature->name == 'Daily Maximum Temperature')
						{
							$j = 0;
							for($i = 1; $i < $this->numPeriods; $i = $i+2)
							{
								$this->Temperature[$i] = (string)$temperature->value[$j];
								$j++;
							}
						}
					}
				}
				else
				{
					foreach($datum->parameters->temperature as $temperature)
					{

						if((string)$temperature->name == 'Daily Minimum Temperature')
						{
							$j = 0;
							for($i = 1; $i < $this->numPeriods; $i = $i+2)
							{
								$this->Temperature[$i] = (string)$temperature->value[$j];
								$j++;
							}
						}
						elseif((string)$temperature->name == 'Daily Maximum Temperature')
						{
							$j = 0;
							for($i = 0; $i < $this->numPeriods; $i = $i+2)
							{
								$this->Temperature[$i] = (string)$temperature->value[$j];
								$j++;
							}
						}

					}
				}
			}
		}
	}

	public function jsonSerialize()
	{
		$return = array(
				'temperatureApparent' => $this->temperatureApparent,
				'weatherSummary'      => $this->weatherSummary,
				'currentDay'          => $this->currentDay,
		    'forecastPeriods'     => $this->numPeriods,
		    'Temperature'         => $this->Temperature,
		    'Weather'             => $this->Weather,
		    'Name'                => $this->Name,
		    'DayName'             => $this->DayName
		);

		return ($return);
	}

} 