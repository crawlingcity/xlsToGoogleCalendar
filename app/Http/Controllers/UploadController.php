<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Http\Request;
use PHPExcel_IOFactory;


define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
    Google_Service_Calendar::CALENDAR
)));

class UploadController extends Controller {
    
    
    
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    
    
    public function uploadSubmit(Request $request) {

        $inputFileName = $request->file('resume')->getPath() . '/' . $request->file('resume')->getFilename();
        
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    
    
        $sheetArray = $objPHPExcel->getSheet(0)->toArray();
    
        $flaviaSchedule = self::getSchedule($sheetArray);
    
        $scheduleTime = [
            'PA' => [
                'in' => '06:30:00',
                'out' => '15:00:00'
            ],
            'PA2' => [
                'in' => '08:00:00',
                'out' => '16:30:00'
            ],
            'CZ' => [
                'in' => '06:00:00',
                'out' => '12:00:00'
            ],
            'B' => [
                'in' => '15:00:00',
                'out' => '23:30:00'
            ],
            'I' => [
                'in' => '14:30:00',
                'out' => '23:30:00'
            ],
            'I2' => [
                'in' => '11:30:00',
                'out' => '20:00:00'
            ],
            'E' => [
                'in' => '09:00:00',
                'out' => '13:00:00'
            ],
            'E1' => [
                'in' => '09:00:00',
                'out' => '17:00:00'
            ],
            'X' => [
                'folga' => 'folga'
            ]
        ];
    
        foreach ($flaviaSchedule as &$day) {
            $day = $scheduleTime[$day];
        }
    
        echo "<pre>";
        print_r($flaviaSchedule);
        echo "</pre>";
        die();
        
        
        function getClient() {
            $client = new Google_Client();
            $client->setApplicationName(APPLICATION_NAME);
            $client->setScopes(SCOPES);
            $client->setAuthConfig(CLIENT_SECRET_PATH);
            $client->setAccessType('offline');
        
            // Load previously authorized credentials from a file.
            $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
            if (file_exists($credentialsPath)) {
                $accessToken = json_decode(file_get_contents($credentialsPath), true);
            } else {
                //        // Request authorization from the user.
                //        $authUrl = $client->createAuthUrl();
                //        printf("Open the following link in your browser:\n%s\n", $authUrl);
                //        print 'Enter verification code: ';
                //        $authCode = trim(fgets(STDIN));
            
                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode('4/YGLsQ9whi-0DL0veUehjs-0n7jhAauZAW4tkA8B_ugU#');
            
                // Store the credentials to disk.
                if (!file_exists(dirname($credentialsPath))) {
                    mkdir(dirname($credentialsPath), 0700, true);
                }
                file_put_contents($credentialsPath, json_encode($accessToken));
                //        printf("Credentials saved to %s\n", $credentialsPath);
            }
            $client->setAccessToken($accessToken);
        
            // Refresh the token if it's expired.
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
            }
        
            return $client;
        }
    
        /**
         * Expands the home directory alias '~' to the full path.
         *
         * @param string $path the path to expand.
         *
         * @return string the expanded path.
         */
        function expandHomeDirectory($path) {
            $homeDirectory = getenv('HOME');
            if (empty($homeDirectory)) {
                $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
            }
        
            return str_replace('~', realpath($homeDirectory), $path);
        }
    
        // Get the API client and construct the service object.
        $client = getClient();
        $service = new Google_Service_Calendar($client);
    
        $flaviaCalendar = '78g4cguo3c57oisvdm3desj83k@group.calendar.google.com';
    
    
        foreach ($flaviaSchedule as $key => $schedule) {
        
            $day = &$key + 1;
            if (array_key_exists('folga', $schedule)) {
                continue;
            }
        
            $event = new Google_Service_Calendar_Event(array(
                'summary' => 'Dia' . $day,
                'location' => 'Exe Almada Porto, Rua do Almada 361, 4050-032 Porto, Portugal',
                'description' => 'mais um dia de trabalho',
                'start' => array(
                    'dateTime' => '2017-10-' . $day . 'T' . $schedule['in'],
                    'timeZone' => 'Europe/Lisbon',
                ),
                'end' => array(
                    'dateTime' => '2017-10-' . $day . 'T' . $schedule['out'],
                    'timeZone' => 'Europe/Lisbon',
                ),
                //        'recurrence' => array(
                //            'RRULE:FREQ=DAILY;COUNT=2'
                //        ),
                //        'attendees' => array(
                //            array('email' => 'lpage@example.com'),
                //            array('email' => 'sbrin@example.com'),
                //        ),
                //        'reminders' => array(
                //            'useDefault' => FALSE,
                //            'overrides' => array(
                //                array('method' => 'email', 'minutes' => 24 * 60),
                //                array('method' => 'popup', 'minutes' => 10),
                //            ),
            ));
        
        
            //    $event = $service->events->insert($flaviaCalendar, $event);
            //    printf('Event created: %s\n', $event->htmlLink);
        }
        
        
        if ($request) {
            return (response()->json($request));
        }
        return response()->json(array('files' => $request), 200);
        
    }
    
    public static function getSchedule($sheetArray) {
        
        foreach ($sheetArray as $array) {
            if ($array[0] == 'flávia') {
                array_shift($array);
                $result = $array;
            }
        }
        
        $result = $result ?? [];
        
        return $result;
    }
    
}
