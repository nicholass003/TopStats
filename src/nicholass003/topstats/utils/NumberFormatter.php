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

use nicholass003\topstats\database\data\DataAction;
use nicholass003\topstats\database\data\DataType;
use nicholass003\topstats\leaderboard\Leaderboard;
use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\player\PlayerModel;
use nicholass003\topstats\model\text\TextModel;
use nicholass003\topstats\TopStats;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\Server;
use nicholass003\topstats\libs\_f3f59fe202917385\SOFe\InfoAPI\InfoAPI;
use function count;
use function floor;
use function is_numeric;
use function random_bytes;
use function round;
use function str_repeat;
use function strlen;
use function uasort;





















































































































































class NumberFormatter{

	public static function short(float|int $number, int $precision = 1) : string{
		if($number < 1000){
			return (string) $number;
		}

		$units = [
			12 => "T",
			9 => "B",
			6 => "M",
			3 => "K",
		];

		foreach($units as $power => $suffix){
			$value = $number / (10 ** $power);
			if($value >= 1){
				return round($value, $precision) . $suffix;
			}
		}

		return (string) $number;
	}
}