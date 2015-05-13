<!doctype html>
<html>
<head>
  <title>Postcode Finder</title>
      <meta charset="utf-8" />
      <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/
bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/
bootstrap-theme.min.css">

<script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcznnMCsCMQI3NZb_velORbTJhIxLVJi8&libraries=places&signed_in=true">
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> 

</head>


<style>

   html, body {
   	  height: 100%;
   }

   #main_container {
   	  background-image: url(gmap_background2.jpg);
   	  height: 100%;
   	  width: 100%;
   	  background-size: cover;
   	  background-position: center;
   }

   .center {
      text-align: center;
   }

   .black {
      color: black;
   }

   .white {
      color: white;
   }

   #info_div {
   	  padding-top: 50px;
   }

   button {
       margin-top: 20px;
       margin-bottom: 20px;
   }

   .form-group {
     margin-top: 20px;
   }

   .alert {
    margin-top: 27px;
    display: none;
   }

   #map-canvas {
     margin-top: 10px;
     height: 45%;
     width: 100%;
     display: none;
   }



</style>

<script>
// add autocomplete feature
var autocomplete;

function initialize() {
   // Create the autocomplete object, restricting the search
  // to geographical location types.
  autocomplete = new google.maps.places.Autocomplete(
      /** @type {HTMLInputElement} */(document.getElementById('input_address')),
      { types: ['geocode'] });

}

// [START region_geolocation]
// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = new google.maps.LatLng(
          position.coords.latitude, position.coords.longitude);
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}


</script>

<body onload="initialize()">

   <div class="container" id="main_container">

    <div class="row">

     <div class="col-md-6 col-md-offset-3 center white" id="info_div">
       <h1 class="center">Postcode Finder</h1>
       </p class="lead center">please enter address below to find the postcode</p>

       <from>
         <div class="form-group">
            <input type="text" class="form-control" name="address" id="input_address" placeholder="Please enter the address" onFocus="geolocate()"></input>
         </div>	

         <button id="findmypostcode" class="btn btn-success btn-lg">Find The Postcode</button>
         <!--div id="map_icon" class=" btn btn-success btn-block btn-sm">show the location of the address on google map</div-->

       </from>	



     </div>

       

    </div>

       <div id="success" class="alert alert-success col-md-6 col-md-offset-3">yeah!!</div>
       <div id="danger" class="alert alert-danger col-md-6 col-md-offset-3">Please enter a valid address</div>
       <!--div id="map_icon" class=" btn btn-success btn-block btn-sm">show the location of the address on google map</div-->
       <div class="center" id="map-canvas"></div>

   </div>


<!-- including jquery-->
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<script>

   $("#findmypostcode").click(function(event) {
        //alert("working");
        var checker = 0;
        $(".alert").hide();
        $("#map-canvas").slideUp();
        event.preventDefault();

        $.ajax({
            type: "GET",
            url: "https://maps.googleapis.com/maps/api/geocode/xml?address=" + encodeURIComponent($('#input_address').val()) + "&key=AIzaSyDcznnMCsCMQI3NZb_velORbTJhIxLVJi8",
            dataType: "xml",
            success:processXML,
            error: error
       });

       function error() {
           alert("connection to google map database loss");
       }

       function processXML(xml) {
           $(xml).find("address_component").each(function() {
              //alert($(this).text());
             if ($(this).find("type").text() == "postal_code") {
                 // find the postal code !!
                 $("#success").html("The postcode you looking for is " + ($(this).find("long_name").text())).fadeIn();
                 $("#map_icon").fadeIn();

                  //get latitude and logtitude
                  var latitude;
                  var longitude;

                 $(xml).find("location").each(function() {
                    latitude = $(this).find("lat").text();
                    longitude = $(this).find("lng").text();
                 })

                  // show the address on google map
                  $("#map-canvas").slideDown(function() {
                    //alert("show map haha");
                    var myLatlng = new google.maps.LatLng(latitude,longitude);
                    var mapOptions = {
                         zoom: 15,
                         center: myLatlng
                    }
                    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                    var marker = new google.maps.Marker({
                         position: myLatlng,
                         map: map,
                         title: $('#input_address').val()
                    });

                    var infowindow = new google.maps.InfoWindow({
                        content: "this is the location of the place you searched."
                    });

                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.open(map,marker);
                    });
                  
                  });

                  checker = 1;
             }

           });

            if (checker == 0) {
               $("#danger").fadeIn();
            }

       }



   });



</script>


</body>
</html>




