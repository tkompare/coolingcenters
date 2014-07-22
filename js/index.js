/**
 * Created by tom on 5/6/14.
 */
(function($,google){

    $(document).ready(function(){
        // Give me all of the executable code.
        var Chicago = new google.maps.LatLng(41.8764219,-87.6281078);

        var GoogleMap = new google.maps.Map(document.getElementById('map'),
            {
                center: Chicago,
                mapTypeControl: false,
                zoom: 15
            }
        );

        var DefaultNWS = new NWS(41.8764219,-87.6281078);
        DefaultNWS.getWeather();

	      var TheWarmingCenters = new WarmingCenters();
				TheWarmingCenters.getCenters(TheWarmingCenters,GoogleMap);

        var FMe = new FindMe();

        if(FMe.geolocate)
        {
            var FMeDiv = document.createElement('div');
            FMe.setFindMeControl(FMeDiv,GoogleMap,FMe);
            FMeDiv.index = 1;
            GoogleMap.controls[google.maps.ControlPosition.TOP_RIGHT].push(FMeDiv);
        }
        else
        {
            alert('We\'re sorry. It seems we cannot attempt to use your device to geolocate.');
        }

    });

})(jQuery,google);