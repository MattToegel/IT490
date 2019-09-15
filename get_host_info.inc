<?php

function getHostInfo(array $extra = NULL){
	$machine;
	$machine = parse_ini_file("host.ini", $process_sections=true);
	if($extra != NULL){
		foreach($extra as $ini){
			$parsed = parse_ini_file($ini, true);
			if($parsed){
				$machine = array_merge($machine,$parsed);
			}
		}
	}
	return $machine;
}
