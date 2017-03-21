   
    var marker;
    var infowindow;

    function initialize() {
      var latlng = new google.maps.LatLng(44.768366, 20.436385);
      var options = {
        zoom: 12,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }
      var map = new google.maps.Map(document.getElementById("map"), options);
	  
	  for(i=0;i<myJsarray.length;i++){
			//console.log("Initialize funkc "+myJsarray[i].name );
			//addMarker1(myJsarray[i].name,myJsarray[i].address,myJsarray[i].lat,myJsarray[i].lng,myJsarray[i].type);
			pos = new google.maps.LatLng(myJsarray[i].lat, myJsarray[i].lng);
			var content=myJsarray[i].name;
			console.log("pos "+pos);
			
			 marker=new google.maps.Marker({
			position:pos,
			map:map,
			content:content,
			title:content,
			animation: google.maps.Animation.DROP
		});
		
			// Marker click listener
    google.maps.event.addListener(marker, 'click', (function (marker, content) {
        return function () {
            console.log('Gmarker 1 gets pushed');
            infowindow.setContent(
			'<p class="kocka">'+ content+'</p>'
			);
		 
            infowindow.open(map, marker);
            map.panTo(this.getPosition());
             map.setZoom(20);
        }
		
    })(marker, content));
	  }
	  
      var html = "<table>" +
                 "<tr><td>Name:</td> <td><input type='text' id='name'/> </td> </tr>" +
                 "<tr><td>Description:</td> <td><input type='text' id='address'/></td> </tr>" +
                 "<tr><td>Type:</td> <td><select id='type' name='type'>" +
                 "<option value='ekocid' SELECTED>ekocid</option>" +
                 "<option value='deponija'>deponija</option>" +
                 "</select> </td></tr>" +
                 "<tr><td></td><td><input type='submit' value='Save & Close' onclick='saveData()'/></td></tr>";
    infowindow = new google.maps.InfoWindow({
     content: html
    });

    google.maps.event.addListener(map, "click", function(event) {
        marker = new google.maps.Marker({
          position: event.latLng,
          map: map
        });
        google.maps.event.addListener(marker, "click", function() {
          infowindow.open(map, marker);
        });
    });
    }

    function saveData() {
      var name = escape(document.getElementById("name").value);
      var address = escape(document.getElementById("address").value);
      var type = document.getElementById("type").value;
      var latlng = marker.getPosition();

      var url = "index.php?name=" + name + "&address=" + address +
                "&type=" + type + "&lat=" + latlng.lat() + "&lng=" + latlng.lng();
      downloadUrl(url, function(data, responseCode) {
        if (responseCode == 200 && data.length >= 1) {
          infowindow.close();
          document.getElementById("message").innerHTML = "Location added.";
        }
      });
	
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request.responseText, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}
	

	   function addMarker(lat, lng, info) {
                var pt = new google.maps.LatLng(lat, lng);
                bounds.extend(pt);
                var marker = new google.maps.Marker({
                    position: pt,
                    icon: icon,
                    map: map
                });
                var popup = new google.maps.InfoWindow({
                    content: info,
                    maxWidth: 300
                });
                google.maps.event.addListener(marker, "click", function() {
                    if (currentPopup != null) {
                        currentPopup.close();
                        currentPopup = null;
                    }
                    popup.open(map, marker);
                    currentPopup = popup;
                });
                google.maps.event.addListener(popup, "closeclick", function() {
                    map.panTo(center);
                    currentPopup = null;
                });
				
            }
			var myJsarray =<?php json_encode($user->getFromMap());?>;
			console.log(myJsarray[0].name);
			
			for(i=0;i<myJsarray.length;i++){
			console.log(myJsarray[i].name +" "+myJsarray[i].address);
			 
		}
			
 google.maps.event.addDomListener(window, 'load', initialize);
   