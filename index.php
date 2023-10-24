<?php

function decode_shift_cipher($encoded_string, $shift)
{
    $decoded_string = "";
    $shift = $shift % 26;
    $length = strlen($encoded_string);

    for ($i = 0; $i < $length; $i++) {
        if (ctype_alpha($encoded_string[$i])) {
            if (ctype_upper($encoded_string[$i])) {
                $decoded_string .= chr((ord($encoded_string[$i]) - 65 - $shift + 26) % 26 + 65);
            } else {
                $decoded_string .= chr((ord($encoded_string[$i]) - 97 - $shift + 26) % 26 + 97);
            }
        } else {
            $decoded_string .= $encoded_string[$i];
        }
    }

    return $decoded_string;
}

function calculate_letter_frequencies($string)
{
    $letter_counts = array_fill_keys(range('A', 'Z'), 0);
    $total_letters = 0;
    $string = strtoupper($string);
    $letter_frequencies = [];

    for ($i = 0; $i < strlen($string); $i++) {
        if (ctype_alpha($string[$i])) {
            $letter_counts[$string[$i]]++;
            $total_letters++;
        }
    }

    foreach ($letter_counts as $letter => $count) {
        $letter_frequencies[$letter] = ($count / $total_letters) * 100;
    }

    return $letter_frequencies;
}

function determine_shift($encoded_string)
{
    $letter_frequencies = calculate_letter_frequencies($encoded_string);
    $shifts = [];
    $expected_frequencies = [
        'E' => 12.56, 'T' => 9.15, 'A' => 8.08, 'O' => 7.47, 'I' => 7.24,
        'N' => 7.38, 'S' => 6.59, 'R' => 6.42, 'H' => 5.27, 'D' => 3.99,
        'L' => 4.04, 'U' => 2.79, 'C' => 3.18, 'M' => 2.60, 'F' => 2.17,
        'P' => 1.91, 'G' => 1.80, 'W' => 1.89, 'Y' => 1.65, 'B' => 1.67,
        'V' => 1.00, 'K' => 0.63, 'X' => 0.21, 'J' => 0.14, 'Q' => 0.09, 'Z' => 0.07
    ];

    arsort($letter_frequencies);

    foreach ($expected_frequencies as $letter => $frequency) {
        $shift = ord('E') - ord($letter);
        $decoded_string = decode_shift_cipher($encoded_string, $shift);
        $decoded_frequencies = calculate_letter_frequencies($decoded_string);
        $distance = 0;

        foreach ($decoded_frequencies as $char => $freq) {
            $distance += abs($freq - $expected_frequencies[$char]);
        }

        $shifts[$shift] = $distance;
    }

    asort($shifts);

    return key($shifts);
}

$message = stream_get_line(STDIN, 1000 + 1, "\n");
$shift = determine_shift($message);
$decoded_message = decode_shift_cipher($message, $shift);

echo $decoded_message;
?>
