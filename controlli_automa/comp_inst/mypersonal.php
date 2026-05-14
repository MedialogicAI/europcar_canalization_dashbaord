<?php

//include_once 'vendor/spatie/opening-hours/src/optimizerchainfactory.php';
//include_once 'vendor/spatie/opening-hours/src/optimizerchain.php';
//include_once 'vendor/spatie/opening-hours/src/dummylogger.php';



include_once 'vendor/spatie/opening-hours/src/Exceptions/Exception.php'; 
include_once 'vendor/spatie/opening-hours/src/Exceptions/OverlappingTimeRanges.php';
include_once 'vendor/spatie/opening-hours/src/Exceptions/NonMutableOffsets.php';
include_once 'vendor/spatie/opening-hours/src/Exceptions/InvalidTimeString.php';
include_once 'vendor/spatie/opening-hours/src/Exceptions/InvalidTimeRangeString.php';
include_once 'vendor/spatie/opening-hours/src/Exceptions/InvalidTimeRangeList.php';
include_once 'vendor/spatie/opening-hours/src/Exceptions/InvalidTimeRangeArray.php';
include_once 'vendor/spatie/opening-hours/src/Exceptions/InvalidDayName.php';
include_once 'vendor/spatie/opening-hours/src/Exceptions/InvalidDate.php';
include_once 'vendor/spatie/opening-hours/src/Exceptions/Exception.php'; 



include_once 'vendor/spatie/opening-hours/src/Helpers/DataTrait.php';
include_once 'vendor/spatie/opening-hours/src/Time.php';
include_once 'vendor/spatie/opening-hours/src/TimeRange.php';
include_once 'vendor/spatie/opening-hours/src/Day.php';
include_once 'vendor/spatie/opening-hours/src/OpeningHours.php';
include_once 'vendor/spatie/opening-hours/src/OpeningHoursForDay.php';







//namespace Spatie\OpeningHours\Test;
use DateTime;
use DateTimeImmutable;
use vendor\Spatie\OpeningHours\Time;
use PHPUnit\Framework\TestCase;
use Spatie\OpeningHours\Exceptions\InvalidTimeString;


$openingHours = new OpeningHours::create([
    'monday' => ['09:00-12:00', '13:00-18:00'],
    'tuesday' => ['09:00-12:00', '13:00-18:00'],
    'wednesday' => ['09:00-12:00'],
    'thursday' => ['09:00-12:00', '13:00-18:00'],
    'friday' => ['09:00-12:00', '13:00-20:00'],
    'saturday' => ['09:00-12:00', '13:00-16:00'],
    'sunday' => [],
    'exceptions' => [
        '2016-11-11' => ['09:00-12:00'],
        '2016-12-25' => [],
    ],
]);



// Open on Mondays:
echo "Lunedi " . $openingHours->isOpenOn('monday'); // true
echo "<br>ok";

// Closed on Sundays:
echo "Domenica " . $openingHours->isOpenOn('sunday'); // false

echo "<br>oook";






?>