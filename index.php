<?php
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
?>
<html>
<head>
    <title>Map of Ushahidi Maps</title>
	<link rel="stylesheet" href="css/stylesheet.css" media="screen,projection"/>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyD4sXMVqjnt2Vcby12Gf7wnVdJy8Jqi74Q&libraries=geometry&sensor=true"></script>
    <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="js/map.js"></script>
    <script type = "text/javascript">
        var map = null;
        var marker = null;
		var markers = [];
        var infoWindow = null;
		var stats = [];
        var loading = false;
		var url = "http://tracker.ushahidi.com/list/?return_vars=url,name,description,category_id,latitude,longitude,category_id&limit=20000";
		var categories = ["All Maps",
						  "Arts & Entertainment",
						  "Science & Technology",
						  "Academic",
						  "Recreation",
						  "Business & Finance",
						  "News",
						  "Computers",
						  "Just For Fun",
						  "Politics",
						  "Government",
						  "Historical",
						  "Health",
						  "Other"];
		$(document).ready(function() {
			loadMap();
			loadSearch();
            loadCategories();
			searchMaps();
        });
    </script>
</head>
<body>
<div id="map"></div>
<input id="search" type="text" name="s" value="<?php echo $_GET['s'] ?>" class="box shadow" placeholder="Search public maps..." >
<select id="categories"></select>
<div id="download">
    <a id="api" href="https://wiki.ushahidi.com/display/WIKI/Deployment+Search" title="Search API" target="_blank"><img src="img/html.png" alt="API"></a>
    <a id="json" href="http://tracker.ushahidi.com/list/?limit=20000" title="Download JSON" target="_blank"><img src="img/json.png" alt="JSON"></a>
    <a id="csv" href="csv.php" title="Download CSV" target="_blank"><img src="img/csv.png" alt="CSV"></a>
</div>
<img id="loading" src="img/loading.gif">
</body>
</html>