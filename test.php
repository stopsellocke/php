<?php
require_once('classes/core/Autoloader.class.php');

$asProps = [
  'DTSTART:20140822T183000Z',
  'DTSTAMP:20140822T183000Z',
  'DTEND:20140822T183000Z',
  'CREATED:20140822T183000Z',
];

$sTest = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//calovo//Calendar Publishing 1.14
METHOD:PUBLISH
CALSCALE:GREGORIAN
BEGIN:VEVENT
DTSTART:20140822T183000Z
DTEND:20140822T203000Z
DTSTAMP:20161217T213002Z
UID:mpnipt1js6g2s99mm62tnh78jc@google.com
CREATED:20140625T012734Z
DESCRIPTION:1. Bundesliga\, 1. Spieltag
LAST-MODIFIED:20151013T102206Z
LOCATION:Allianz-Arena\, MÃ¼nchen
SEQUENCE:5
STATUS:CONFIRMED
SUMMARY:Bayern MÃ¼nchen - VfL Wolfsburg (2:1)
TRANSP:TRANSPARENT
END:VEVENT
BEGIN:VEVENT
UID:58259aabd6409@2.calovo
DTSTART;TZID=Europe/Berlin:20161218T153000
SEQUENCE:86
TRANSP:TRANSPARENT
STATUS:CONFIRMED
DTEND;TZID=Europe/Berlin;VALUE=DATE:20170416
URL:http://i.cal.to/r/1R2y
LOCATION:Jonathan-Heimes-Stadion am Böllenfalltor
SUMMARY:SV Darmstadt 98 - FC Bayern München
DESCRIPTION:Bundesliga\, 15. Spieltag
DTSTAMP:20161218T131712Z
CREATED:20161111T101715Z
LAST-MODIFIED:20161218T131712Z
END:VEVENT
END:VCALENDAR";
try {
  $oCalendar = new \ics\Calendar();
  $oCalendar->fromICS($sTest);
  echo($oCalendar->toICS()."\n");
} catch(Exception $e) {
  var_dump($e);
}


//$oTest = new DateTimeItem('DTSTART:20140822T183000Z');
//echo($oTest);

/*
class VEvent {
  private $oStart;

  public function setStart(DateTime $oDate) {
    $this->oStart = $oDate;
  }

    BEGIN:VEVENT
*    DTSTART:20140822T183000Z
*    DTEND:20140822T203000Z
*    DTSTAMP:20161217T213002Z
    UID:mpnipt1js6g2s99mm62tnh78jc@google.com
    CREATED:20140625T012734Z
    DESCRIPTION:1. Bundesliga\, 1. Spieltag
    LAST-MODIFIED:20151013T102206Z
    LOCATION:Allianz-Arena\, MÃ¼nchen
    SEQUENCE:5
    STATUS:CONFIRMED
    SUMMARY:Bayern MÃ¼nchen - VfL Wolfsburg (2:1)
    TRANSP:TRANSPARENT
    END:VEVENT



}

$sSource     = 'http://www.allianz-arena.de/de/service/terminkalender/Termine_in_der_AllianzArena.ics';
$sSource     = 'http://i.cal.to/ical/2/fcbayern/bundesliga-spielplan/b132ba1e.6677324f-7033ad1e.ics';
$sTargetPath = '/kunden/175772_85354/calendar/';
$sTargetFile = 'cal_alianzarena.ics';
$hSource = array(
  'bayern' => 'https://www.google.com/calendar/ical/q0uc47v90gltqpojkk76t9mk30%40group.calendar.google.com/public/basic.ics',
);


function downloadICSCalendar($sRemoteICS) {
  $sContent = file_get_contents($sRemoteICS);
//  var_dump($sContent);
  
//  exec('wget -q '.$sSource.' -O '.$sTargetPath.$sTargetFile);
  
}

//downloadICSCalendar($hSource['bayern']);


 */


/*
$sAlarm = 'BEGIN:VALARM
ACTION:DISPLAY
TRIGGER;VALUE=DURATION:-P1D
DESCRIPTION:ICSSync
END:VALARM
END:VEVENT';

$sFile = file_get_contents($sTargetPath.$sTargetFile);
//$sFile = str_replace(';TZID=Europe/Paris', '', $sFile);
//$sFile = str_replace('END:VEVENT', $sAlarm, $sFile);
//var_dump($sFile);
@unlink($sTargetPath.$sTargetFile);
file_put_contents($sTargetPath.$sTargetFile, $sFile);
echo("[OK]");
 */


?>
