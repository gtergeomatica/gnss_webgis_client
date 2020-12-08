var stile_pc = {
		    radius: 6,
		    fillColor: "#fc9d03",
		    color: "#000",
		    weight: 0.5,
		    opacity: 1,
		    fillOpacity: 0.8
};


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
                return '<h3>' + f.properties.name + '</h3>';
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





function createRealtimeLayer2(url, container) {
    return L.realtime(url, {
    	 
        interval: 3 * 1000,
        getFeatureId: function(f) {
            return f.cod;
        },
		  style:function (f, latlng) {
		        return L.circleMarker(latlng, {
		    radius: 6,
		    fillColor: "#fc9d03",
		    color: "#000",
		    weight: 0.5,
		    opacity: 1,
		    fillOpacity: 0.8
			});
		    },
        cache: true,
        container: container
    });
}



/*
var stile_pc = {
		    radius: 6,
		    fillColor: "#fc9d03",
		    color: "#000",
		    weight: 0.5,
		    opacity: 1,
		    fillOpacity: 0.8
		};
		
		var layer_punti = L.geoJson(punti, {
		    pointToLayer: function (feature, latlng) {
		        //return createSquare(latlng, stile_sopralluogo); // rettangolo (vedi funzione definita sopra)
		        return L.circleMarker(latlng, stile_pc);
		        //return L.marker(latlng, {icon: icon_rischio});
		    }
		});
*/

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
