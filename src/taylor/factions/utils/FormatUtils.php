<?php namespace taylor\factions\utils;

use dktapps\pmforms\MenuOption;

class FormatUtils {

    public static function getCloseButton() : MenuOption {
        return new MenuOption("Close Form.");
    }

    public static function numberToSuffix(int $number) : string {
        $prefixes = ["", "K", "M", "B", "T", "Q"];
        $exponent = floor(log10($number) / 3);
        $prefix = $prefixes[$exponent] ?? "???";
        $number /= pow(1000, $exponent);
        $number = round($number, 2);
        return $number . $prefix;
    }

    public static function numberToHumanReadable($seconds) : string {
        $units = [
            "day" => 86400,
            "hour" => 3600,
            "minute" => 60,
            "second" => 1
        ];
        $result = "";
        foreach ($units as $unit => $value) {
            if ($seconds >= $value) {
                $count = floor($seconds / $value);
                $result .= $count . " " . $unit;
                if ($count > 1) {
                    $result .= "s";
                }
                $result .= ", ";
                $seconds -= $count * $value;
            }
        }
        $result = rtrim($result, ",");
        return preg_replace('/, ([^,]+)$/', ' and $1', $result);
    }

}