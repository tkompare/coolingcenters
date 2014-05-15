/**
 * Created by tom on 5/7/14.
 */
/**
 * @classDestription - National Weather Service variables and functions.
 * @class - NWS
 */
var NWS = (function($){
    var constructor = function(lat,lng,domid){

        this.getWeather = function()
        {
            $.support.cors = true; //enables cross domain support
            $.ajax({
                type: "GET",
                url: '/ajax/weather.php?lat='+lat+'&lng='+lng,
                dataType: "json",
                success: function(data) {
                    $('#'+domid).html(data.temperatureApparent+'&deg;&nbsp;-&nbsp;'+data.weatherSummary);
                }
            });
        }
    };
    return constructor;

})(jQuery);