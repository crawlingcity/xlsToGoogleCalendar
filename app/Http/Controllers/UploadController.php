<?php

namespace App\Http\Controllers;

use DateTime;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Http\Request;
use PHPExcel_IOFactory;

class UploadController extends Controller {
    
    
    public function deleteEvents() {
        /**
         * @var $event Google_Service_Calendar_Event
         */
        $flaviaCalendar = '78g4cguo3c57oisvdm3desj83k@group.calendar.google.com';
        $gCal = new GoogleCalendarController;
        $client = $gCal->getClient();
        $client->setAccessToken($_SESSION['access_token']);
        $service = new Google_Service_Calendar($client);
        $events = $service->events->listEvents($flaviaCalendar);
        $total = 0;
        foreach ($events->getItems() as $event) {
            $service->events->delete($flaviaCalendar,$event->getId());
            $total++;
            
        }
        die;
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadSubmit(Request $request) {
        session_start();
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
        
        $flaviaCalendar = '78g4cguo3c57oisvdm3desj83k@group.calendar.google.com';
        $gCal = new GoogleCalendarController;
        $client = $gCal->getClient();
        $client->setAccessToken($_SESSION['access_token']);
        $service = new Google_Service_Calendar($client);
        $dayToInsert = 1;
        foreach ($flaviaSchedule as $key => &$schedule) {
            
            if (isset($schedule)) {
                $schedule = $scheduleTime[$schedule];
            } else {
                continue;
            }
            
            
            if (array_key_exists('folga', $schedule)) {
                continue;
            }
            
            $event = new Google_Service_Calendar_Event(array(
                'summary' => 'H: ' . substr($schedule['in'], 0, 5) . '/' . substr($schedule['out'], 0, 5),
                'location' => 'Exe Almada Porto, Rua do Almada 361, 4050-032 Porto, Portugal',
                'description' => 'mais um dia de trabalho',
                'start' => array(
                    'dateTime' => '2017-11-' . $dayToInsert . 'T' . $schedule['in'],
                    'timeZone' => 'Europe/Lisbon',
                ),
                'end' => array(
                    'dateTime' => '2017-11-' . $dayToInsert . 'T' . $schedule['out'],
                    'timeZone' => 'Europe/Lisbon',
                ),
                //        'reminders' => array(
                //            'useDefault' => FALSE,
                //            'overrides' => array(
                //                array('method' => 'email', 'minutes' => 24 * 60),
                //                array('method' => 'popup', 'minutes' => 10),
                //            ),
            ));
            
            
            $service->events->insert($flaviaCalendar, $event);
            $dayToInsert++;
            //                printf('Event created: %s\n', $event->htmlLink);
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
