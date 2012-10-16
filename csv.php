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

$categories = loadCategories();
$maps = downloadMaps();
outputCSV($categories, $maps);

function loadCategories() {
   return array(
       "Undefined",
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
       "Other");
}

function downloadMaps() {
    $url = "http://tracker.ushahidi.com/list/?return_vars=id,url,name,description,category_id,latitude,longitude,category_id&limit=20000";
    if (isset($_GET["q"])) {
        $url = $url . "&q=" . urldecode($_GET["q"]);
    }
    if (isset($_GET["category_id"])) {
        $url = $url . "&category_id=" . urldecode($_GET["category_id"]);
    }
    $data = file_get_contents($url);
    $json = json_decode($data, true);
    return $json;
}

function downloadCategories($id, $url) {
    $json_file = dirname(__FILE__) . '/json/' . $id . ".json";
    if (file_exists($json_file) == false) {
        $url = $url . "/api?task=categories";
        $data = file_get_contents($url);
        if ($data != null) {
            $file = fopen($json_file, 'wb');
            fwrite($file, $data);
            fclose($file);
        }
    }
    else {
        $data = file_get_contents($json_file);
    }
    $array = array();
    $json = json_decode($data, true);
    $payload = $json['payload'];
    $categories = $payload['categories'];
    foreach ($categories as $item) {
        $category = $item['category'];
        if (isset($category['title'])) {
            array_push($array, $category['title']);
        }
        else if (isset($category['category_title'])) {
            array_push($array, $category['category_title']);
        }
    }
    return implode(",", $array);
}

function outputCSV($categories, $maps) {
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=maps.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $array = array(array("map","url","name","description","type","latitude","longitude"));
    foreach ($maps as $map_id => $map) {
        $line = array(
            $map_id,
            $map['url'],
            $map['name'],
            $map['description'],
            $categories[$map['category_id']],
            $map['latitude'],
            $map['longitude']
            //downloadCategories($map_id, $map['url'])
        );
        array_push($array, $line);
    }
    outputArray($array);
}

function outputArray($array) {
    $out_stream = fopen("php://output", "w");
    function __outputCSV(&$values, $key, $file_handler) {
        fputcsv($file_handler, $values);
    }
    array_walk($array, "__outputCSV", $out_stream);
    fclose($out_stream);
}

?>