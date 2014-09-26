/**
 * Created by tom on 5/7/14.
 */
/**
 * @classDestription - National Weather Service variables and functions.
 * @class - NWS
 */
var NWS;
NWS = (function ($) {

	'use strict';

	var constructor = function (lat, lng, weatherUrl) {

		this.setTableRowClass = function (domId, temperature) {
			if (parseInt(temperature) > 85) {
				$('#' + domId).addClass('danger');
			}
			else if (parseInt(temperature) < 32) {
				$('#' + domId).addClass('info');
			}
		}

		this.fixTwelveHour = function (time) {
			if (time.toLowerCase() == '12:00pm' || time.toLowerCase() == '12:00 pm') {
				return '12:00';
			}
			else if (time.toLowerCase() == '12:00am' || time.toLowerCase() == '12:00 am') {
				return '0:00';
			}
			return time;
		}

		this.setMapIcons = function (Centers, theDay) {
			var self = this;
			var dayTime = theDay.split('|');
			var j = 0;
			var openCount = 0;
			for (var i in Centers.CenterData) {
				var dynamicOpen = dayTime[0].toLowerCase() + '_open';
				var dynamicClose = dayTime[0].toLowerCase() + '_close';
				if (
						Centers.CenterData[j][dynamicOpen] === 'CLOSED'
								|| Centers.CenterData[j][dynamicClose] === 'CLOSED'
								|| Centers.CenterData[j][dynamicOpen] == ''
								|| Centers.CenterData[j][dynamicClose] == ''
						) {
					Centers.Markers[j].setIcon('img/grey.png');
				}
				else {
					// Figure out open date of today
					var tempTimeOpen = self.fixTwelveHour(Centers.CenterData[i][dynamicOpen]);
					var openDT = Date.parse(dayTime[0] + ' ' + tempTimeOpen).add(7).days();

					// Figure out close date of today
					var tempTimeClose = self.fixTwelveHour(Centers.CenterData[i][dynamicClose]);
					var closeDT = Date.parse(dayTime[0] + ' ' + tempTimeClose).add(7).days();

					// Date to compare
					var thisTime = dayTime[1];

					// fix the time to compare to more reasonable interpretation of NWS's daytime and evening/nighttime.
					if (thisTime == '06:00 AM') {
						thisTime = '1:00 PM';
					}
					else if (thisTime == '06:00 PM') {
						thisTime = '11:00 PM';
					}
					var whatToParse = dayTime[0] + ' ' + self.fixTwelveHour(thisTime);
					var thisDate = Date.parse(whatToParse).add(7).days();

					if (thisDate.between(openDT, closeDT) == true) {
						Centers.Markers[j].setIcon('img/red.png');
						openCount++;
					}
					else {
						Centers.Markers[j].setIcon('img/grey.png');
					}
				}

				// Iterator
				j++;
			}
			if (openCount == 0) {
				alert('Sorry, we could not find an open warming/cooling center for this forecast period. Please call 311 for assistance. Emergency shelter may be available.');
			}
		}

		this.getWeather = function (Centers) {
			var self = this;

			$.ajax({
				type: "GET",
				url: weatherUrl + '?lat=' + lat + '&lng=' + lng,
				dataType: "json",
				success: function (data) {
					$('#tempCurrent').text(data.temperatureApparent).append('&deg;');
					$('#weatherCurrent').text(data.weatherSummary);
					$('#now').click(function () {
						self.setMapIcons(Centers, data.currentDay);
						$('#help-p').text('Centers Open Right Now Are Red.');
					});

					for(var i = 0; i < data.forecastPeriods; i++)
					{
						$('#namePeriod'+ i.toString()).text(data.Name[i]);
						$('#tempPeriod'+ i.toString()).html(data.Temperature[i]).append('&deg;');
						$('#weatherPeriod'+ i.toString()).text(data.Weather[i]);
						self.setTableRowClass('period'+i,data.Temperature[i]);
						$('#period'+i.toString()).click(function() {
							var matched = $(this).attr('id').match(/[0-9]+/);
							self.setMapIcons(Centers, data.DayName[matched]);
							$('#help-p').text('Centers Open '+data.Name[matched]+' Are Red.');
							console.log(data);
						});

					}
					$('#forecast-fetch').addClass('hidden');
					$('#forecast').removeClass('hidden');
				}
			});
		}
	};
	return constructor;

})(jQuery);