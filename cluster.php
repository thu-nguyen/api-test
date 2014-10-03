<?php
$number = (isset($argv[1])) ? $argv[1] : 1;

generateCluster($number);
function generateCluster($connections){
	$cmd = "sudo cssh -l thu ";
	for($i = 0; $i < $connections; $i++){
		$cmd .="localhost ";
	}
	shell_exec($cmd);	
}