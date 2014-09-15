/**
 * Created by tom on 5/6/14.
 */
(function ($, google) {

	// Give me all of the executable code.
	$(document).ready(function () {

		// What url do I get the weather data from?
		var weatherUrl = 'service/weather.php';

		// Where is the center of the map?
		var lat = 41.8764219;
		var lng = -87.6281078;

		var GooglMapDefaultLatLng = new google.maps.LatLng(lat, lng);

		var GoogleMap = new google.maps.Map(document.getElementById('map'),
				{
					disableDefaultUI: true,
					center: GooglMapDefaultLatLng,
					styles: 	[
							{
						featureType: "administrative",
						stylers: [{ saturation: -87 }]
					},
				{
					featureType: "landscape",
							stylers: [{ saturation: -87 }]
				},
				{
					featureType: "poi",
							stylers: [{ saturation: -87 }]
				},
				{
					featureType: "road",
							stylers: [{ saturation: -87 }]
				},
				{
					featureType: "water",
							stylers: [{ saturation: -87 }]
				},
				{
					featureType: "road.arterial",
							elementType: "geometry",
						stylers: [{ lightness: 85 }]
				},
				{
					featureType: "water",
							stylers: [{ lightness: -20 }]
				},
				{
					featureType: "transit.station.rail",
							stylers: [{ saturation: 85 }]
				}],
					zoom: 12
				}
		);

		var DefaultCenters = new Centers();
		DefaultCenters.makeMarkers(GoogleMap);
		DefaultCenters.makeInfoWindows();
		DefaultCenters.addClickListeners(GoogleMap);

		var DefaultNWS = new NWS(lat, lng, weatherUrl);
		DefaultNWS.getWeather(DefaultCenters);

		var FMe = new FindMe();

		if (FMe.geolocate) {
			var FMeDiv = document.createElement('div');
			FMe.setFindMeControl(FMeDiv, GoogleMap, FMe);
			FMeDiv.index = 1;
			GoogleMap.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(FMeDiv);
		}
		else {
			alert('We\'re sorry. It seems we cannot attempt to use your device to geolocate.');
		}

	});

})(jQuery, google);