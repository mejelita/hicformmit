<?php
/* Ubah url $googleSpreadsheetUrl menjadi url dari spreadsheet Anda sendiri */
$googleSpreadsheetUrl = "https://docs.google.com/spreadsheets/u/1/d/e/2PACX-1vRh6jk0b1a1P8vrFJRnsUAxdXS5N-_V757BamUDenW8KmHTiwS7O5N4HSp1WAQOuy5JWvPLXkpMT2cs/pub?gid=2021615297&single=true&output=csv"; 

$rowCount = 0;
$features = array();
$error = FALSE;
$output = array();

// attempt to set the socket timeout, if it fails then echo an error
if ( ! ini_set('default_socket_timeout', 15))
{
  $output = array('error' => 'Unable to Change PHP Socket Timeout');
  $error = TRUE;
} // end if, set socket timeout

// if the opening the CSV file handler does not fail
if ( !$error && (($handle = fopen($googleSpreadsheetUrl, "r")) !== FALSE) )
{
  // while CSV has data, read up to 10000 rows
  while (($csvRow = fgetcsv($handle, 10000, ",")) !== FALSE)
  {
    $rowCount++;

    if ($rowCount == 1) { continue; } // skip the first/header row of the CSV

    $features[] = array(
      'type'     => 'Feature',
      'geometry' => array(
        'type'   => 'Point',
        'coordinates' => array(
          (float) $csvRow[2], // longitude, casted to type float
          (float) $csvRow[1]  // latitude, casted to type float
        )
      ),
      'properties' => array(
        'nama' => $csvRow[3],
		'kategori' => $csvRow[4],
        'deskripsi' => $csvRow[5],
        'keterangan' => $csvRow[6],
		'foto' => $csvRow[7],
		'kontributor' => $csvRow[8],
      )
    );
  } // end while, loop through CSV data

  fclose($handle); // close the CSV file handler

  $output = array(
    'type' => 'FeatureCollection',
    'features' => $features
  );
}  // end if , read file handler opened

// else, file didn't open for reading
else
{
  $output = array('error' => 'Problem Reading Google CSV');
}  // end else, file open fail

// convert the PHP output array to JSON "pretty" format
$jsonOutput = json_encode($output, JSON_PRETTY_PRINT);

// render JSON and no cache headers
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');

echo $jsonOutput;
