<?php

// date_default_timezone_set('Europe/Paris');
// var_dump(date("d - m - Y", easter_date(2021)));

class Calendar
{

    private const MONTHS = ["january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"];
    private const WEEK_DAYS = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
    private $holidays = ["01" => [1], "05" => [1, 8], "07" => [14], "08" => [15], "11" => [1, 11], "12" => [25], "06" => [25]];

    private $day = "";
    private $dayDate = "";
    private $month = "";
    private $year = "";
    
    public function __construct(String $timezone = "Europe/Paris")
    {
        // Pâques
        $date = new DateTime();
        $date->setTimestamp(easter_date(2021));
        $date->setTimezone(new DateTimeZone($timezone));
        $easter = strtotime($date->format("d-m-Y"));

        $easter_monday = explode("-", date("m-d", $easter + 86400));
        $ascension = explode("-", date("m-d", $easter + 86400 * 39));
        $pentecost_monday = explode("-", date("m-d", $easter + 86400 * 50));

        if (!isset($this->holidays[$easter_monday[0]])) {
            $this->holidays[$easter_monday[0]] = [];
        }
        if (!isset($this->holidays[$ascension[0]])) {
            $this->holidays[$ascension[0]] = [];
        }
        if (!isset($this->holidays[$pentecost_monday[0]])) {
            $this->penholidays[$pentecost_monday[0]] = [];
        }

        array_push($this->holidays[$easter_monday[0]], $easter_monday[1]);
        array_push($this->holidays[$ascension[0]], $ascension[1]);
        array_push($this->holidays[$pentecost_monday[0]], $pentecost_monday[1]);
    }

    public function getMonth(Int $month_timestamp)
    {
        $res = [];
        $holiday = false;

        // jour courant
        $real_day = date("d", time());
        $real_month = date("m", time());

        // timestamp du premier du mois passé en paramètre
        $this_month_first_day_timestamp = strtotime("01-" . date("m", $month_timestamp) . "-" . date("Y", $month_timestamp));
        // timestamp du dernier jour du mois d'avant
        $last_month_timestamp = $this_month_first_day_timestamp - 86400;
        // le nom du dernier jour du mois d'avant
        $previous_month_last_day_name = date("l", $last_month_timestamp);
        // le nombre du dernier jour du mois d'avant
        $previous_month_last_day_nbr = date("d", $last_month_timestamp);
        // le nombre du mois du dernier mois
        $previous_month_nbr = date("m", $last_month_timestamp);
        // la position dernier jour du mois d'avant dans l'array de la constante WEEK_DAYS
        $previous_month_last_days_count = array_search(lcfirst($previous_month_last_day_name), $this::WEEK_DAYS);

        for ($i = $previous_month_last_days_count; $i > -1; $i--) {
            $day = $previous_month_last_day_nbr - $i;
            $holiday = isset($this->holidays[$previous_month_nbr]) ? in_array($day, $this->holidays[$previous_month_nbr]) : false;
            $res[] = (object) [
                "class" => "prev",
                "day" => $day,
                "holiday" => $holiday,
                "selected" => false,
            ];
        }

        // le nombre du mois
        $current_month = date("m", $month_timestamp);
        // combien de jours dans ce mois
        $days_in_current_month = cal_days_in_month(CAL_GREGORIAN, $current_month, date("Y", $month_timestamp));

        for ($i = 1; $i <= $days_in_current_month; $i++) {
            $holiday = isset($this->holidays[$current_month]) ? in_array($i, $this->holidays[$current_month]) : false;
            $res[] = (object) [
                "class" => "current",
                "day" => $i,
                "holiday" => $holiday,
                "selected" => false,
            ];
        }

        // timestamp du mois suivant
        $next_month_timestamp = $this_month_first_day_timestamp + ($days_in_current_month * 86400);
        // le nom du premier jour du mois suivant
        $next_month_first_day_name = date("l", $next_month_timestamp);
        // le nombre de jours après le mois courant pour arriver jusqu'à Dimanche
        $next_month_first_days_count = count($this::WEEK_DAYS) - array_search(lcfirst($next_month_first_day_name), $this::WEEK_DAYS);

        for ($i = 1; $i <= $next_month_first_days_count; $i++) {
            $holiday = isset($this->holidays[$next_month_first_day_name]) ? in_array($i, $this->holidays[$next_month_first_day_name]) : false;
            $res[] = (object) [
                "class" => "next",
                "day" => $i,
                "holiday" => $holiday,
                "selected" => false,
            ];
        }

        if (count($res) < 42) {
            $endItem_day = end($res)->day;
            $holiday = isset($this->holidays[$next_month_first_day_name]) ? in_array($i, $this->holidays[$next_month_first_day_name]) : false;
            for ($i = 1; $i <= 7; $i++) {
                $res[] = (object) [
                    "class" => "next",
                    "day" => $endItem_day + $i,
                    "holiday" => $holiday,
                    "selected" => false,
                ];
            }
        }

        if ($real_month == $current_month) {
            $res[strval($real_day)]->selected = "selected";
        }

        return $res;
    }

}

$calendar = new Calendar();
// $res = $calendar->getLastMonthLastDays(strtotime("first day of previous month"));

$all_days = $calendar->getMonth(mktime(0, 0, 0, 1, 1, 2021));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
    <div><?=$calendar->month?></div>
    <table class="agenda">
        <thead>
            <tr>
                <th>Lu</th>
                <th>Ma</th>
                <th>Me</th>
                <th>Je</th>
                <th>Ve</th>
                <th>Sa</th>
                <th>Di</th>
            </tr>
        </thead>
        <tbody>
<?php

// $all_days = $calendar->getMonth(time());
$id = 0;
foreach ($all_days as $day) {
    if ($id != 0 && fmod($id / 7, 1) == 0) {
        echo "</tr>";
    }

    if ($id % 7 == 0) {
        echo "<tr>";
    }

    echo "<td><div class='" . ($day->selected ?? "") . " " . $day->class . " " . ($day->holiday ? "holiday" : "") . "'>";

    echo $day->day;

    echo "<div></td>";

    $id++;
}

?>
        </tbody>
    </table>
</body>
</html>
