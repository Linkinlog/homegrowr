<?php

use Illuminate\Support\Facades\Route;
use \App\Models\Sensors;

Route::get('/overview', function () {
    $atmosphere = Sensors::getUUIDsAndName('atmosphere');
    $cameras = Sensors::getUUIDsAndName('camera');
    return view('overview', ['atmosphere' => $atmosphere->toArray(), 'cameras' => $cameras->toArray()]);
});


Route::get('/videos/{$ip}', function ($ip) {
    $server = $ip;
    $url = "/mjpeg/1";
    set_time_limit(0);
    $fp = fsockopen($server, 80, $errno, $errstr, 30);
    if (!$fp) {
            echo "$errstr ($errno)<br>\n";
    } else {
            $urlstring = "GET ".$url." HTTP/1.0\r\n\r\n";
            fputs ($fp, $urlstring);
            while ($str = trim(fgets($fp, 4096)))
            header($str);
            fpassthru($fp);
            fclose($fp);
    }
});

Route::get('/', function () {
    return redirect('/overview');
});
