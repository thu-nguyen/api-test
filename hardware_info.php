<?php
function filterInfo($str, $filter){
    $position = strpos($str, $filter) + strlen($filter);
    return trim(substr($str,$position));
}

$cpu = shell_exec('lshw -short | grep -i "processor"');
$cpu_info = explode("\n", $cpu);
$cpu_info = filterInfo($cpu_info[0], "processor");
$info = " CPU : " . $cpu_info ."\n";

$memory = shell_exec('lshw -short | grep -i "System Memory"');
$info .= " RAM : " . str_replace("System Memory", "", filterInfo($memory, "memory")) . "\n";

$disk = shell_exec('lshw -short | grep -i "disk"');
$disk = explode("\n", $disk);
$disk = $disk[0];
$info .= " HDD : " . filterInfo($disk, "disk") . "\n";
echo $info;
file_put_contents("hardware.txt", $info);