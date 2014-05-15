/**
 * Created by tom on 5/6/14.
 */
/**
 * @classDestription - FindMe variables and functions.
 * @class - FindMe
 */
var FindMe = (function($) {
    var constructor = function(){
        this.AddressMarker = null;

        // Can we geolocate?
        this.geolocate = navigator.geolocation;

        /**
         * Set the address for a latlng
         */
        this.codeLatLng = function(Latlng)
        {
            var Geocoder = new google.maps.Geocoder();
            Geocoder.geocode(
                {'latLng': Latlng},
                function(Results,Status)
                {
                    if (Status == google.maps.GeocoderStatus.OK)
                    {
                        if (Results[0])
                        {
                            var formattedAddress = Results[0].formatted_address.split(',');
                            $('#nav-address').text(formattedAddress[0]);
                        }
                        else
                        {
                            alert('We\'re sorry. We could not find an address for this location.');
                        }
                    }
                    else
                    {
                        alert('We\'re sorry. We could not find an address for this location.');
                    }
                }
            );
        };

        // Put a Pan/Zoom control on the map
        this.setFindMeControl = function(controlDiv,Map,FMe)
        {
            // Set CSS styles for the DIV containing the control
            // Setting padding to 5 px will offset the control
            // from the edge of the map.
            controlDiv.style.padding = '1em';
            // Set CSS for the control border.
            var controlUI = document.createElement('div');
            controlUI.style.backgroundColor = '#333';
            //controlUI.style.color = 'white';
            controlUI.style.borderStyle = 'solid';
            controlUI.style.borderWidth = '0px';
            controlUI.style.cursor = 'pointer';
            controlUI.style.textAlign = 'center';
            controlUI.style.borderRadius = '6px';
            controlUI.title = 'Click to find your location.';
            controlDiv.appendChild(controlUI);
            // Set CSS for the control interior.
            var controlText = document.createElement('div');
            controlText.style.fontFamily = '"Helvetica Neue",Helvetica,Arial,sans-serif';
            controlText.style.fontSize = '12px';
            controlText.style.color = '#fff';
            controlText.style.paddingLeft = '.5em';
            controlText.style.paddingRight = '.5em';
            controlText.style.paddingTop = '.3em';
            controlText.style.paddingBottom = '.3em';
            controlText.innerHTML = 'Find Me';
            controlUI.appendChild(controlText);
            // Setup the click event listeners.
            google.maps.event.addDomListener(controlUI, 'click', function() {
                navigator.geolocation.getCurrentPosition(
                    // Success
                    function(position)
                    {
                        //_gaq.push(['_trackEvent', 'GPS', 'Success']);
                        var Latlng = new google.maps.LatLng(
                            position.coords.latitude,
                            position.coords.longitude
                        );
                        Map.setCenter(Latlng);
                        Map.setZoom(15);
                        // Make a map marker if none exists yet
                        if(FMe.AddressMarker === null)
                        {
                            FMe.AddressMarker = new google.maps.Marker({
                                position:Latlng,
                                map: Map,
                                clickable:false
                            });
                        }
                        else
                        {
                            // Move the marker to the new location
                            FMe.AddressMarker.setPosition(Latlng);
                            // If the marker is hidden, unhide it
                            if(FMe.AddressMarker.getMap() === null)
                            {
                                FMe.AddressMarker.setMap(Map);
                            }
                        }
                        FMe.codeLatLng(Latlng);
                        var Weather = new NWS(position.coords.latitude,position.coords.longitude,'temperature');
                        Weather.getWeather();
                    },
                    // Failure
                    function()
                    {
                        alert('We\'re sorry. We could not find you.');
                    },
                    {
                        timeout:5000,
                        enableHighAccuracy:true
                    }
                );
            });
        };
    };
    return constructor;
})(jQuery);
