<?php

function getDateFromTimestamp($timestamp = null) {
    if (is_null($timestamp) || $timestamp === '') {
        return null;
    }
    $date = new DateTime();
    return $date->setTimestamp($timestamp);
}

function getDateFromISO8601($iso8601 = null) {
    if (is_null($iso8601) || $iso8601 === '') {
        return null;
    }
    $date = new DateTime($iso8601);
    $date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
    return $date;
}

function getISO8601FromDate($date = null) {
    if (is_null($date) || !$date) {
        return null;
    }
    return $date->format(DateTime::ATOM);
}

function getISO8601FromTimestamp($timestamp = null) {
    if (is_null($timestamp) || $timestamp === '') {
        return null;
    }
    return getISO8601FromDate(getDateFromTimestamp($timestamp));
}

function getTimestampFromISO8601($iso8601 = null) {
    $date = getDateFromISO8601($iso8601);
    if (is_null($date)) return null;

    return $date->getTimestamp();
}

function getTimestampFromDate($date = null) {
    if (is_null($date)) {
        return null;
    }
    return $date->getTimestamp();
}

function getMilliseconds() {
    $mt = explode(' ', microtime());
    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
}