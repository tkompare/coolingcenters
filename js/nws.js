/**
 * Created by tom on 5/7/14.
 */
/**
 * @classDestription - National Weather Service variables and functions.
 * @class - NWS
 */
var NWS = (function($){
    var constructor = function(lat,lng){

        this.warnLevels = [
            {
                description: 'Very Hot',
                max: 150,
                min: 95,
                backgroundColor: 'dd0a00',
                textColor: 'ffffff'
            },
            {
                description: 'Hot',
                max: 94,
                min: 85,
                backgroundColor: 'd8dd00',
                textColor: '000000'
            },
            {
                description: '',
                max: 84,
                min: 32,
                backgroundColor: 'dddddd',
                textColor: '000000'
            },
            {
                description: 'Cold',
                max: 31,
                min: 0,
                backgroundColor: '00ddd7',
                textColor: '000000'
            },
            {
                description: 'Very Cold',
                max: -1,
                min: -100,
                backgroundColor: '000add',
                textColor: 'ffffff'
            }
        ];

        this.getWeather = function()
        {
            $.support.cors = true; //enables cross domain support
            $.ajax({
                type: "GET",
                url: '/ajax/weather.php?lat='+lat+'&lng='+lng,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $('#tempCurrent').text(data.temperatureApparent).append('&deg;');
                    $('#weatherCurrent').text(data.weatherSummary);
                    $('#namePeriod0').text(data.namePeriod0);
                    $('#tempPeriod0').html(data.tempPeriod0).append('&deg;');
                    $('#weatherPeriod0').text(data.weatherPeriod0);
                    $('#namePeriod1').text(data.namePeriod1);
                    $('#tempPeriod1').text(data.tempPeriod1).append('&deg;');
                    $('#weatherPeriod1').text(data.weatherPeriod1);
                    $('#namePeriod2').text(data.namePeriod2);
                    $('#tempPeriod2').text(data.tempPeriod2).append('&deg;');
                    $('#weatherPeriod2').text(data.weatherPeriod2);
                    $('#namePeriod3').text(data.namePeriod3);
                    $('#tempPeriod3').text(data.tempPeriod3).append('&deg;');
                    $('#weatherPeriod3').text(data.weatherPeriod3);
                    $('#namePeriod4').text(data.namePeriod4);
                    $('#tempPeriod4').text(data.tempPeriod4).append('&deg;');
                    $('#weatherPeriod4').text(data.weatherPeriod4);
                    if(parseInt(data.temperatureApparent) > 50)
                    {
                        $('#find-shelter').text('Find A Cooling Center');
                    }
                    else
                    {
                        $('#find-shelter').text('Find A Warming Center');
                    }
                    $('#outlook').removeClass('hidden');
                }
            });
        }
    };
    return constructor;

})(jQuery);