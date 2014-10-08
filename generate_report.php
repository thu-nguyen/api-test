<?php

$folderPath = "request_1000/report_test";

$result = readDataFromFile($folderPath);
echo generateReport($result);
function readDataFromFile($folderPath){
	$data = array();
	$actionFolders = scandir($folderPath);
	foreach ($actionFolders as $actionFolder) {
		if ($actionFolder != '.' && $actionFolder != '..'){
			$content = file_get_contents($folderPath . "/" . $actionFolder . "/user.json");
			$content = explode("\n", $content);
			foreach ($content as $row) {
				$values = json_decode($row, true);
				if (is_array($values)){
					$data[$actionFolder][] = $values;	
				}	
			}
		}
	}
	// print_r($data);
	return $data;	
}

function generateReport($data){
	$tableFormat = "| %35s | %15s | %15s | %20s | %20s |\n";
	$result = sprintf($tableFormat, "Action", "Total Records", "Concurency", "Time Per Request", "Requests Per Second");
	foreach ($data as $action => $values) {	
		foreach ($values as $value) {
			$duration = $value['end'] - $value['start'];
			$requestPersecond = round($value['total_request']/$duration,3);
			$timePerRequest = round($duration/$value['total_request'],3);
			$result .= sprintf($tableFormat, 
			$action, $value['total_request'], $value['concurrency'], $timePerRequest, $requestPersecond);	
		}		
	}
	return $result;
}