<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Import calendar events from iCAl iCS in PHP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  </head>
  <body>
    <?php
    class ics {
      /* Function is to get all the contents from ics and explode all the datas according to the events and its sections */
      function getIcsEventsAsArray($file) {
          $icalString = file_get_contents ( $file );
          $icsDates = array ();
          /* Explode the ICs Data to get datas as array according to string ‘BEGIN:’ */
          $icsData = explode ( "BEGIN:", $icalString );
          /* Iterating the icsData value to make all the start end dates as sub array */
          foreach ( $icsData as $key => $value ) {
              $icsDatesMeta [$key] = explode ( "\n", $value );
          }
          /* Itearting the Ics Meta Value */
          foreach ( $icsDatesMeta as $key => $value ) {
              foreach ( $value as $subKey => $subValue ) {
                  /* to get ics events in proper order */
                  $icsDates = $this->getICSDates ( $key, $subKey, $subValue, $icsDates );
              }
          }
          return $icsDates;
      }

      /* funcion is to avaid the elements wich is not having the proper start, end  and summary informations */
      function getICSDates($key, $subKey, $subValue, $icsDates) {
          if ($key != 0 && $subKey == 0) {
              $icsDates [$key] ["BEGIN"] = $subValue;
          } else {
              $subValueArr = explode ( ":", $subValue, 2 );
              if (isset ( $subValueArr [1] )) {
                  $icsDates [$key] [$subValueArr [0]] = $subValueArr [1];
              }
          }
          return $icsDates;
      }
    }

    /* Replace the URL / file path with the .ics url */
    $file = "http://www.fichte-gym.de/termine.ics";

    /* Getting events from isc file */
    $obj = new ics();
    $icsEvents = $obj->getIcsEventsAsArray( $file );

    /* Here we are getting the timezone to get the event dates according to gio location */
    $timeZone = trim ( $icsEvents [1] ['X-WR-TIMEZONE'] );
    unset( $icsEvents [1] );
    $html = '<center><br><br>
    <table class="table table-bordered" style="width: 80%;">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Event Name</th>
          <th scope="col">Start at</th>
          <th scope="col">End at</th>
          <th scope="col">Standort</th>
          <th scope="col">Beschreibung</th>
          <th scope="col">Status</th>
          <th scope="col">Erstellt</th>
        </tr>
      </thead>
      <tbody>';

    $counter=1;
    foreach( $icsEvents as $icsEvent){
          $start = isset( $icsEvent ['DTSTART;VALUE=DATE'] ) ? $icsEvent ['DTSTART;VALUE=DATE'] : $icsEvent ['DTSTART'];
          $end = isset( $icsEvent ['DTEND;VALUE=DATE'] ) ? $icsEvent ['DTEND;VALUE=DATE'] : $icsEvent ['DTEND'];
          $st = isset( $icsEvent ['CREATED;VALUE=DATE'] ) ? $icsEvent ['CREATED;VALUE=DATE'] : $icsEvent ['CREATED'];

          $startDt = new DateTime ( $start );
          //$startDt->setTimeZone ( new DateTimezone ( $timeZone ) );
          $startDate = $startDt->format ( 'm/d/Y h:i' );

          $endDt = new DateTime ( $end );
          $endDate = $endDt->format ( 'm/d/Y h:i' );

          $CREATE = new DateTime ( $st );
          $CREATED = $CREATE->format ( 'm/d/Y h:i' );

          $eventName = isset( $icsEvent ['SUMMARY'] ) ? $icsEvent ['SUMMARY'] : $icsEvent ['SUMMARY;LANGUAGE=de'];
          $Location = $icsEvent['LOCATION'];
          $DESCRIPTION = $icsEvent['DESCRIPTION'];
          $STATUS = $icsEvent['STATUS'];

          $html .= '<tr><td>'.$counter.'</td><td>'.$eventName.'</td><td>'.$startDate.'</td><td>'.$endDate.'</td><td>'.$Location.'</td><td>'.$DESCRIPTION.'</td><td>'.$STATUS.'</td><td>'.$CREATED.'</td></tr>';
     $counter++;
    }
    echo $html.'</tbody></table></center>';
    var_dump($icsEvents);
  ?>
  </body>
</html>
