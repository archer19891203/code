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
        $duration .= (int) $day.'��';
    }
    if ($hour > 0) {
        $duration .= (int) $hour.'Сʱ';
    }
    if ($minute > 0) {
        $duration .= (int) $minute.'��';
    }
    if ($second > 0) {
        $duration .= (int) $second.'��';
    }

    return $duration;
}