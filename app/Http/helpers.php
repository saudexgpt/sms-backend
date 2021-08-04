<?php
function workingDays($day = null)
{
    $arr = [
        '1' => 'Monday',
        '2' => 'Tuesday',
        '3' => 'Wednesday',
        '4' => 'Thursday',
        '5' => 'Friday'

    ];

    if ($day) {
        return $arr[$day];
    }
    return $arr;
}

function workingDaysStr($day = null)
{
    $arr = [
        'Monday'    => '1',
        'Tuesday'   => '2',
        'Wednesday' => '3',
        'Thursday'  => '4',
        'Friday'    => '5'

    ];

    if ($day) {
        return $arr[$day];
    }
    return $arr;
}
