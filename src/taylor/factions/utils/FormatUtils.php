<?php namespace taylor\factions\utils;

class FormatUtils {

    public static function numberToSuffix(int $number) : string {
        $prefixes = ["", "K", "M", "B", "T", "Q"];
        $exponent = floor(log10($number) / 3);
        $prefix = $prefixes[$exponent] ?? "???";
        $number /= pow(1000, $exponent);
        $number = round($number, 2);
        return $number . $prefix;
    }

}