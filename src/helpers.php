<?php

/**
 * @param $value
 * @return string
 */
function normalizeNumberForBc($value)
{
    $result = trim($value);

    if (stripos($result, 'e') !== false) {
        $array = preg_split('/e/i', $result);
        $base = trim($array[0]);
        $exp = (int)trim($array[1]);

        if ($base[0] === '+') {
            $base = substr($base, 1);
        }

        $tenPow = bcpow('10', (string)abs($exp));

        $scale = strlen(strstr($base, '.')) - 1 + abs($exp) + 10;

        $result = $exp < 0
            ? bcdiv($base, $tenPow, $scale)
            : bcmul($base, $tenPow, $scale);
    }

    $pos = strpos($result, '.');
    if ($pos !== false) {
        $result = rtrim($result, '0');
        $scale = strlen(substr($result, $pos + 1));
    } else {
        $scale = 0;
    }

    return bcadd($result, '0', $scale);
}

/**
 * @param mixed ...$decimals
 * @return int
 */
function decimalPlaces(...$decimals)
{
    $max = 0;

    foreach ($decimals as $decimal) {
        $decimal = normalizeNumberForBc($decimal);
        $pos = strpos($decimal, '.');
        $length = $pos !== false ? strlen(substr($decimal, $pos + 1)) : 0;
        $max = max($max, $length);
    }

    return $max;
}

/**
 * @param $number
 * @return bool
 */
function gt0($number)
{
    return gt($number, '0', false);
}

/**
 * @param $left
 * @param $right
 * @param bool $eq
 * @param int|null $decimal
 * @return bool
 */
function gt($left, $right, $eq = true, $decimal = null)
{
    $left = normalizeNumberForBc($left);
    $right = normalizeNumberForBc($right);

    $decimal = $decimal === null ? decimalPlaces($left, $right) : (int)$decimal;

    return $eq
        ? bccomp($left, $right, $decimal) >= 0
        : bccomp($left, $right, $decimal) > 0;
}

/**
 * @param $left
 * @param $right
 * @param bool $eq
 * @param int|null $decimal
 * @return bool
 */
function lt($left, $right, $eq = true, $decimal = null)
{
    return !gt($left, $right, !$eq, $decimal);
}

/**
 * @param ...$numbers
 * @return string
 */
function bcaddd(...$numbers)
{
    $sum = '0';

    foreach ($numbers as $number) {
        $number = normalizeNumberForBc($number);
        $sum = bcadd($sum, $number, decimalPlaces($sum, $number));
    }

    return $sum;
}

/**
 * @param $left
 * @param $right
 * @param int|null $decimal
 * @return string
 */
function bcsubSafe($left, $right, $decimal = null)
{
    $left = normalizeNumberForBc($left);
    $right = normalizeNumberForBc($right);

    return clearDecimal(bcsub($left, $right, decimalPlaces($left, $right)), $decimal);
}

/**
 * @param $left
 * @param $right
 * @param int|null $decimal
 * @return string
 */
function bcmulSafe($left, $right, $decimal = null)
{
    $left = normalizeNumberForBc($left);
    $right = normalizeNumberForBc($right);

    $scale = decimalPlaces($left) + decimalPlaces($right);

    return clearDecimal(bcmul($left, $right, $scale), $decimal);
}

/**
 * @param $left
 * @param $right
 * @param int|null $decimal
 * @return string
 */
function bcdivSafe($left, $right, $decimal = null)
{
    $left = normalizeNumberForBc($left);
    $right = normalizeNumberForBc($right);

    if (bccomp($right, '0', decimalPlaces($right)) <= 0) {
        return '0';
    }

    return clearDecimal(bcdiv($left, $right, 20), $decimal);
}

/**
 * @param string $amount
 * @param int|null $decimal
 * @return string
 */
function clearDecimal($amount, $decimal = null)
{
    $result = normalizeNumberForBc($amount);
    if ($decimal !== null) {
        $result = bcadd($result, '0', $decimal);
    }

    return $result;
}
