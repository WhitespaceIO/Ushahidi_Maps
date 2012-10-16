// ##########################################################################################
//
// Copyright (c) 2012, Whitespace Initiatives. All rights reserved.
//
// Redistribution and use in source and binary forms, with or without modification, are
// permitted provided that the following conditions are met:
//
// 1) Redistributions of source code must retain the above copyright notice, this list of
//    conditions and the following disclaimer.
// 2) Redistributions in binary form must reproduce the above copyright notice, this list
//    of conditions and the following disclaimer in the documentation
//    and/or other materials provided with the distribution.
// 3) Neither the name of the Whitespace Initiatives nor the names of its contributors may be
//    used to endorse or promote products derived from this software without specific prior
//    written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
// EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
// OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
// SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
// SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT
// OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
// HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
// TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
// EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
//
// ##########################################################################################

function loadMap() {
	if (null === map) {
		var center = new google.maps.LatLng(22.171127, -23.554688);
		var mapOptions = {
	  		zoom:3,
	  		center:center,
			panControl:false,
			zoomControl:false,
			scaleControl:false,
			rotateControl:false,
			mapTypeControl:false,
			overviewMapControl:false,
			streetViewControl:false,
			mapTypeId:google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById('map'), mapOptions);	
	}
    google.maps.event.addListener(map, 'click', function(event) {
        if (infoWindow) {
            infoWindow.close();
        }
    });
}

function loadSearch() {
    $('#search').keydown(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            searchMaps();
        }
    });
}

function showLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var latLong = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            if (marker) {
                marker.setMap(null);
            }
            marker = new google.maps.Marker({
                position:latLong,
                map:map,
                title:"My Location"
            });
        });
    }
}

function addMap(data) {
	if (data) {
		var latLong = new google.maps.LatLng(data.latitude, data.longitude);
		var icon = data.url.indexOf(".crowdmap.com") != -1 
			? new google.maps.MarkerImage("img/crowdmap.png") 
			: new google.maps.MarkerImage("img/ushahidi.png");
	    var marker = new google.maps.Marker({
			map:map,
			position:latLong,
			title:data.name,
			icon:icon
		});
		markers.push(marker);
		var html = $("<div></div>");
		if (data.ss1_t) {
			html.append($("<div></div>", {class:'thumbnail'}).append($("<img/>"), {src:ss1_t}));
		}
		html.append($("<h1></h1>", {text:data.name}));
		if (data.category_id) {
			html.append($("<h2></h2>", {text:categories[data.category_id]}));
		}
		if (data.description) {
			html.append($("<h3></h3>", {text:data.description}));
		}
		html.append($("<h3></h3>").append($("<a></a>", {title:data.name, href:data.url, target:"_blank", text:data.url})));
		google.maps.event.addListener(marker, 'click', function() {
		  	if (infoWindow) {
                infoWindow.close();
            }
            infoWindow = new google.maps.InfoWindow({content:html.html()});
            infoWindow.open(map, marker);
		});	
		if (data.category_id) {
			if (stats[data.category_id]) {
				stats[data.category_id] += 1;
			}
			else {
				stats[data.category_id] = 1;
			}
		}
	}
}

function removeMaps() {
	for (var i = 0; i < markers.length; i++ ) {
		markers[i].setMap(null);
	}
}

function searchMaps() {
    if (loading == false) {
        loading = true;
        $("#loading").fadeIn("slow");
        removeMaps();
        showLocation();
        stats = [];
        var query = "";
        if ($('#search').val()) {
            query += "&q=" + $('#search').val();
        }
        if ($('#categories').val() > 0) {
            query += "&category_id=" + $('#categories').val();
        }
        $("#json").attr("href", url + query);
        $("#csv").attr("href", "csv.php?type=csv" + query);
        $.ajax({
            type:"GET",
            url:url + query,
            dataType:"json"
        }).done(function(data) {
            $.each(data, function() {
                addMap(this);
            });
            loadNumbers();
            $("#loading").fadeOut('slow');
            loading = false;
        });
    }
}

function loadCategories() {
	$.each(categories, function(key, value) {   
	     $('#categories')
	         .append($("<option></option>")
	         .attr("value",key)
	         .text(value)); 
	});
	$('#categories').change(function() {
		searchMaps();
	});
}

function loadNumbers() {
	var total = 0;
	$.each(stats, function(key, value) {   
		if (key > 0 && typeof value !== "undefined") {
			total += value;
	        $('#categories').find('option[value="'+key+'"]').text(categories[key] + " (" + value + ")");
        }
        else {
            $('#categories').find('option[value="'+key+'"]').text(categories[key] + " (0)");
        }
    });
	$('#categories').find('option[value="0"]').text(categories[0] + " (" + total + ")");
}