var map = L.map('map'),
    //clusterGroup = L.markerClusterGroup().addTo(map),
    //subgroup1 = L.featureGroup.subGroup(clusterGroup),
    //realtime1 = createRealtimeLayer1('http://demo.gter.it/demo_rfi/geojson.php', subgroup1).addTo(map);
	//realtime1 = createRealtimeLayer('https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/all_hour.geojson', subgroup1).addTo(map);
realtime = createRealtimeLayer1('./geojson.php').addTo(map);
realtime1 = createRealtimeArea('./aree_d.php').addTo(map);
/*var map = L.map('map'),
    realtime = L.realtime('./geojson.php', {
        interval: 3 * 1000
    }).addTo(map);
*/