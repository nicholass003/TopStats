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

namespace Nicholass003\TopStats\Utils;

use Nicholass003\TopStats\libs\_809aa045474901d4\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_809aa045474901d4\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use Nicholass003\TopStats\Database\Data\DataAction;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\Leaderboard\Leaderboard;
use Nicholass003\TopStats\TopStats;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use Nicholass003\TopStats\libs\_809aa045474901d4\SOFe\InfoAPI\InfoAPI;
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