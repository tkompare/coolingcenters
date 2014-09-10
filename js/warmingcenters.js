/**
 * Created by tom on 7/21/14.
 */
/**
 * @classDestription - City of Chicago Warming Centers variables and functions.
 * @class - WarmingCenters
 */
var WarmingCenters;
WarmingCenters = (function ($) {
	var constructor = function () {

		this.Markers = [];
		this.Info = [];

		this.getCenters = function (WC, Map) {
			console.log('here');
			$.support.cors = true; //enables cross domain support
			$.ajax({
				type: "GET",
				url: 'http://data.cityofchicago.org/resource/h243-v2q5.json',
				dataType: "json",
				success: function (data) {
					console.log(data);
					var x = 0;
					for (var i in data) {
						console.log(data[i].location.latitude);
						WC.Markers[x] = new google.maps.Marker({
							position: new google.maps.LatLng(data[i].location.latitude, data[i].location.longitude),
							map: Map,
							clickable: true
						});
						WC.Info[x] = new google.maps.InfoWindow({
							content: '<b>Monday:</b> ' + data[i].monday_open + ' to ' + data[i].monday_close
						});

						google.maps.event.addListener(WC.Markers[x], 'click', function(innerKey) {
							return function() {
								WC.Info[innerKey].open(Map, WC.Markers[innerKey]);
							}
						}(x));
						x++;
					}
				}
			});
		}
	};
	return constructor;

})(jQuery);