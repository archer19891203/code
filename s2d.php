function second2duration($seconds)
{
    $duration = '';

    $seconds  = (int) $seconds;
    if ($seconds <= 0) {
        return $duration;
    }

    list($day, $hour, $minute, $second) = explode(' ', gmstrftime('%j %H %M %S', $seconds));

    $day -= 1;
    if ($day > 0) {
        $duration .= (int) $day.'天';
    }
    if ($hour > 0) {
        $duration .= (int) $hour.'小时';
    }
    if ($minute > 0) {
        $duration .= (int) $minute.'分';
    }
    if ($second > 0) {
        $duration .= (int) $second.'秒';
    }

    return $duration;
}