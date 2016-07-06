
<?php
echo "<br> Val1:-".$_POST['val1'];// form data
echo "<br> Val2:-".$_POST['val2'];// form data
echo "<br> Val3:-".$_POST['val3'];// form data
echo "<br> Val4:-".$_POST['val4'];// form data

require_once getcwd().'/google-api-php-client/src/Google/autoload.php'; // download the latest Google Client API
const CLIENT_APP_NAME = 'TESTAPP'; // App Name
const CLIENT_EMAIL ='<Your Google serivce account email id>'; // email id of the service account created
const CLIENT_KEY_PATH = '<Path to your .P12 file>'; // Path to the .P12 file
const CLIENT_KEY_PW = '<Password generated while creating .P12 key>'; // Note it down while creating the service account
$client = new Google_Client();
$client->setApplicationName(CLIENT_APP_NAME);

$client->setAssertionCredentials( new Google_Auth_AssertionCredentials(
CLIENT_EMAIL,
array('https://spreadsheets.google.com/feeds','https://www.googleapis.com/auth/drive','https://www.googleapis.com/auth/drive.apps.readonly',
'https://www.googleapis.com/auth/drive.file','https://www.googleapis.com/auth/drive.metadata.readonly','https://www.googleapis.com/auth/drive.readonly','https://www.googleapis.com/auth/spreadsheets'),
file_get_contents(CLIENT_KEY_PATH),
CLIENT_KEY_PW
));

    if ($client->getAuth()->isAccessTokenExpired()) {
        $client->getAuth()->refreshTokenWithAssertion($cred);
    }

$service = new Google_Service_Sheets($client);
$spreadsheetId='<Google Spreadsheet ID>'; // Google Spreadsheet ID
$SheetName='<Google Spreadsheet Name>'; // Google spreadsheet Name

// This function Reads the google spreadsheet and returns the row to which values are to be appended

function fetchRangeToBeUpdated($service,$spreadsheetId,$SheetName){
  $response = $service->spreadsheets_values->get($spreadsheetId,$SheetName);
  $values = $response->getValues();
  $c=count($values) + 1;

  return $SheetName.'!A'.$c; // follows A1 sheet Notation
}

$Range = fetchRangeToBeUpdated($service,$spreadsheetId,$SheetName);

$data = array(
  array($_POST['val1'],$_POST['val2'],$_POST['val3'],$_POST['val4'])
);
$body = new Google_Service_Sheets_ValueRange();
$body->setValues($data);
$opt= array("valueInputOption"=>"RAW");
$response = $service->spreadsheets_values->update($spreadsheetId,$Range,$body,$opt);

echo "<br> All the above values are updated to the excel sheet!";
  ?>
