<?php

include "./class/calendar.php";

// date_default_timezone_set('Europe/Paris');
// var_dump(date("d - m - Y", easter_date(2021)));



$calendar = new Calendar();
$all_days = $calendar->getMonth(time(), Calendar::WEEK_LENGTH);
// $res = $calendar->getLastMonthLastDays(strtotime("first day of previous month"));

// $all_days = $calendar->getMonth(mktime(0, 0, 0, 1, 1, 2021));


include "calendar.php";

?>