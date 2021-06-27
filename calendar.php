<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <script type="text/javascript">
        window.onload = function() {
            const all_days = JSON.parse(<?= "'" . json_encode($all_days) . "'" ?>)
            let agenda = document.getElementsByClassName("agenda")[0]
            let weekList = agenda.getElementsByTagName("tbody")[0].children
            for(i = 0; i < all_days.length; i++) {
                const week = all_days[i]
                for(u = 0; u < week.length; u++) {
                    const day = week[u]
                    weekList[i].children[u].addEventListener("click", function() {
                        console.log(Object.values(day).join(" "))
                    })
                }
            }
        }
    </script>
</head>
<body>
    <div><?=""#$calendar->month?></div>
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

// var_dump($all_days);
// die;

// $all_days = $calendar->getMonth(time());
// Helper::joinClass($week_day, "selected", "class", "holiday");

foreach($all_days as $week) {
    echo "<tr>";
    foreach($week as $week_day) {
        echo "<td><div class='day " . ($week_day->selected ?? "") . " " . $week_day->class . " " . ($week_day->holiday ? "holiday" : "") . "'>";

    echo $week_day->day;

    echo "</div></td>";
    }
    echo "</tr>";
}

?>
        </tbody>
    </table>
</body>
</html>
