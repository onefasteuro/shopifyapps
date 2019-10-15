<?php


if(! function_exists('shopifyAppsConfig')) {
	function shopifyAppsConfig($app_id) {
		$keyname = 'app_' . $app_id;
		return config('shopifyapps.' . $keyname, []);
	}
}