<?php

// GOOGLE SEARCH LUCKY IMAGER
// Directly serve the first image result of Google Image Search from given keyword search terms.
// Need to create a Programmable Search Engine Id. Results may differ from standard web search depending on your Programmable Search Engine settings.
// Use format: mysite.com/gluckyimg.php?q=apple (where 'apple' is your keywords)

$apik = "GOOGLE_API_KEY";
$csek = "PROGRAMMABLE_SEARCH_ENGINE_ID";

$query = isset($_GET["q"]) ? $_GET["q"] : NULL;
if(is_null($query))
      throw new Exception("Please enter the query you want to search for");

$url = "https://www.googleapis.com/customsearch/v1?key=" . $apik . "&cx=" . $csek . "&searchType=image&safe=medium&q=" . urlencode($query);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Blindly accept the certificate
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
// decode response
curl_setopt($ch, CURLOPT_ENCODING, true);
$results = curl_exec($ch);
curl_close($ch);

if($results === FALSE)
    throw new Exception("error, no content retrieved - FALSE " . $results);
if(!$results)
    throw new Exception("error, no content retrieved - NOT " . $results);

$json_results = json_decode($results, true);

if($json_results["searchInformation"][totalResults] === 0)
    throw new Exception("error, ZERO results returned: " . $results);

header("Content-Type: " . $json_results["items"][0]["mime"] );
header("Content-Disposition: filename=image" . $json_results["items"][0]["image"]["byteSize"] . ".jpg");
header("Accept-Ranges: bytes");
header("Content-Length: " . $json_results["items"][0]["image"]["byteSize"] );
header("Last-Modified: " . date(DATE_RFC2822));
readfile( $json_results["items"][0]["link"] );
exit();

// DEBUGGING
//echo "Query: " . $query;
//echo "<br>Image: " . $json_results["items"][0]["link"];
//echo "<br>";

//echo $results;

?>
