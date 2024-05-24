<?php

/*
 * Copyright (c) 2024 - present nicholass003
 *        _      _           _                ___   ___ ____
 *       (_)    | |         | |              / _ \ / _ \___ \
 *  _ __  _  ___| |__   ___ | | __ _ ___ ___| | | | | | |__) |
 * | '_ \| |/ __| '_ \ / _ \| |/ _` / __/ __| | | | | | |__ <
 * | | | | | (__| | | | (_) | | (_| \__ \__ \ |_| | |_| |__) |
 * |_| |_|_|\___|_| |_|\___/|_|\__,_|___/___/\___/ \___/____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  nicholass003
 * @link    https://github.com/nicholass003/
 *
 *
 */

declare(strict_types=1);

namespace nicholass003\topstats\utils;

use nicholass003\topstats\TopStats;
use function str_replace;
use function uasort;

class Utils{

	public static function getSortedArrayBoard(array $data, string $type) : array{
		uasort($data, function($a, $b) use($type) {
			return $b[$type] <=> $a[$type];
		});
		return $data;
	}

	public static function getTopStatsText(array $data, string $type, string $text) : string{
		$result = "";
		$num = 1;
		foreach(self::getSortedArrayBoard($data, $type) as $xuid => $userData){
			$result .= str_replace(["{player}", "{" . $type . "}", "{rank_" . $type . "}"], [$userData["name"], $userData[$type], $num], $text);
			if($num >= TopStats::getInstance()->getMaxList()){
				break;
			}
			++$num;
		}
		return $result;
	}
}
