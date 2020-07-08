<?php

function getHostInfo($server, array $extra = NULL){
	$machine;
	$machine[$server] = parse_ini_file("host.ini", $process_sections=true);
	if($extra != NULL){
		foreach($extra as $ini){
			$parsed = parse_ini_file($ini, true);
			if($parsed){
				$machine[$server] = array_merge($machine,$parsed);
			}
		}
	}
	return $machine;
}
