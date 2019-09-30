
function createRealtimeLayer1(url, container) {
    return L.realtime(url, {
        interval: 3 * 1000,
        getFeatureId: function(f) {
            return f.id;
        },
        cache: true,
        container: container,
        onEachFeature(f, l) {
            l.bindPopup(function() {
                return '<h3>' + f.properties.name + '</h3>' +
                    '<p>' + new Date(f.properties.data) +
                    '<br/>Quality: <strong>' + f.properties.quality + '</strong></p>';
            });
        }
    });
}

function createRealtimeArea(url, container) {
    return L.realtime(url, {
    	 
        interval: 3 * 1000,
        getFeatureId: function(f) {
            return f.id;
        },

			style: function(f) {
				switch (f.properties.tipo) {
					case 'Danger': return {color: "#ff0000"};
					case 'Safety':   return {color: "#00ff00"};
				}
			},

        cache: true,
        container: container,
        onEachFeature(f, l) {
            l.bindPopup(function() {
                return '<h3> area di tipo ' + f.properties.tipo + '</h3>';
            });
        }
    });
}


function createRealtimeLayer(url) {
    return L.realtime(url, {
        interval: 3 * 1000,
        getFeatureId: function(f) {
            return f.id;
        },
        cache: true,
		//isruzioni per stile non funzionanti
		style: function (f) {
				return {color: '#'+ f.id + f.id + '0000' }
		},
		pointToLayer: function (f, latlng) {
			return L.circleMarker(latlng, {
				radius: 8,
				//fillColor: "#ff7800",
				fillColor: '#'+ f.id + f.id + '0000',
				color: "#000",
				weight: 1,
				opacity: 1,
				fillOpacity: 0.8
			});
		},
        onEachFeature(f, l) {
            l.bindPopup(function() {
                return '<h3>' + f.properties.ip + '</h3>' +
                    '<p>' + new Date(f.properties.data) +
                    '<br/>Quality: <strong>' + f.properties.quality + '</strong></p>';
            });
        }
    });
}
var map = L.map('map'),
    //clusterGroup = L.markerClusterGroup().addTo(map),
    //subgroup1 = L.featureGroup.subGroup(clusterGroup),
    //realtime1 = createRealtimeLayer1('http://demo.gter.it/demo_rfi/geojson.php', subgroup1).addTo(map);
	//realtime1 = createRealtimeLayer('https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/all_hour.geojson', subgroup1).addTo(map);
realtime = createRealtimeLayer1('http://demo.gter.it/demo_rfi/geojson.php').addTo(map);
realtime1 = createRealtimeArea('http://demo.gter.it/demo_rfi/aree_d.php').addTo(map);
/*var map = L.map('map'),
    realtime = L.realtime('./geojson.php', {
        interval: 3 * 1000
    }).addTo(map);
*/


L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
     maxZoom: 26,
  maxNativeZoom: 19
    
}).addTo(map);

L.control.scale({imperial: false}).addTo(map);



realtime.once('update', function() {
	if (realtime.getBounds().isValid()){
    	map.fitBounds(realtime.getBounds(), {maxZoom: 20});
   } else {
   	map.fitBounds(realtime1.getBounds(), {maxZoom: 20});
	}
});