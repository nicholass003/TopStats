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

use Nicholass003\TopStats\libs\_05ec4535640dd243\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_05ec4535640dd243\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use Nicholass003\TopStats\Database\Data\DataAction;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\Leaderboard\Leaderboard;
use Nicholass003\TopStats\TopStats;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use Nicholass003\TopStats\libs\_05ec4535640dd243\SOFe\InfoAPI\InfoAPI;
use function count;
use function floor;
use function is_numeric;
use function random_bytes;
use function round;
use function str_repeat;
use function strlen;
use function uasort;

class Utils{

	public static function getSortedArrayBoard(array $data, string $type) : array{
		uasort($data, function($a, $b) use($type) {
			return $b[$type] <=> $a[$type];
		});
		return $data;
	}

	public static function getTopStatsText(array $data, Model $model, string $text, string $textType, bool $forceSorting = false) : string{
		$result = "";
		$num = 1;
		$max = TopStats::getInstance()->getMaxList();
		if($textType === Leaderboard::TYPE_TITLE){
			$max = 1;
		}
		if(!$forceSorting){
			$data = self::getSortedArrayBoard($data, $model->getCompoundTag()->getString(Leaderboard::TAG_TYPE));
		}
		foreach($data as $xuid => $userData){
			if($model instanceof NonPlayerCharacter){
				if($num === $model->getCompoundTag()->getByte(Leaderboard::TAG_TOP)){
					$result = self::validateTextFormat($model->getCompoundTag()->getString(Leaderboard::TAG_TYPE), $userData, $text, $num);
					break;
				}
			}else{
				$result .= self::validateTextFormat($model->getCompoundTag()->getString(Leaderboard::TAG_TYPE), $userData, $text, $num);
				if($num >= $max){
					break;
				}
			}
			++$num;
		}
		if(strlen($result) === 0 && $model instanceof NonPlayerCharacter){
			$result .= match($textType){
				Leaderboard::TYPE_TITLE => self::validateTextFormat($model->getCompoundTag()->getString(Leaderboard::TAG_TYPE), ["name" => "Unknown", $model->getCompoundTag()->getString(Leaderboard::TAG_TYPE) => 0], $text, $num),
				Leaderboard::TYPE_TEXT => InfoAPI::render(TopStats::getInstance(), TopStats::getInstance()->getConfig()->get("no-data-found-text", Leaderboard::NO_DATA_FOUND), [
					"line" => "\n"
				])
			};
		}
		return $result;
	}

	public static function getTopStatsPlayerSkin(array $data, string $type, int $top, bool $forceSorting = false) : Skin{
		$playerName = "";
		$num = 1;
		if(!$forceSorting){
			$data = self::getSortedArrayBoard($data, $type);
		}
		foreach($data as $xuid => $userData){
			if($num === $top){
				$playerName = $userData["name"];
				break;
			}
			++$num;
		}

		$player = TopStats::getInstance()->getServer()->getPlayerExact($playerName);
		if($player !== null){
			return Human::parseSkinNBT($player->getSaveData());
		}else{
			$playerData = TopStats::getInstance()->getServer()->getOfflinePlayerData($playerName);
			$standard = new Skin("Standard_Custom", str_repeat(random_bytes(3) . "\xff", 4096)); //If player data is not found, use a default solid color skin
			return $playerData !== null ? Human::parseSkinNBT($playerData) : $standard;
		}
	}

	public static function getNextTopStatsIds() : int{
		return count(TopStats::getInstance()->getLeaderboardManager()->leaderboards());
	}

	public static function validateTextFormat(string $type, array $data, string $text, int $rank) : string{
		$formattedData = $data[$type];
		if($type === DataType::ONLINE_TIME){
			$formattedData = self::timeFormat($data[$type]);
		}elseif(is_numeric($formattedData)){
			$formattedData = NumberFormatter::short($formattedData);
		}
		return InfoAPI::render(TopStats::getInstance(), $text, [
			"player" => $data["name"],
			$type => $formattedData,
			"rank_" . $type => $rank,
			"line" => "\n"
		]);
	}

	public static function timeFormat(int $time) : string{
		$years = floor($time / (365 * 24 * 60 * 60));
		$months = floor(($time - ($years * 365 * 24 * 60 * 60)) / (30 * 24 * 60 * 60));
		$weeks = floor(($time - ($years * 365 * 24 * 60 * 60) - ($months * 30 * 24 * 60 * 60)) / (7 * 24 * 60 * 60));
		$days = floor(($time - ($years * 365 * 24 * 60 * 60) - ($months * 30 * 24 * 60 * 60) - ($weeks * 7 * 24 * 60 * 60)) / (24 * 60 * 60));
		$hours = floor(($time - ($years * 365 * 24 * 60 * 60) - ($months * 30 * 24 * 60 * 60) - ($weeks * 7 * 24 * 60 * 60) - ($days * 24 * 60 * 60)) / (60 * 60));
		$minutes = floor(($time - ($years * 365 * 24 * 60 * 60) - ($months * 30 * 24 * 60 * 60) - ($weeks * 7 * 24 * 60 * 60) - ($days * 24 * 60 * 60) - ($hours * 60 * 60)) / 60);
		$seconds = $time % 60;

		$format = TopStats::getInstance()->getTimeFormat();
		return InfoAPI::render(TopStats::getInstance(), $format, [
			"year" => $years,
			"month" => $months,
			"week" => $weeks,
			"day" => $days,
			"hour" => $hours,
			"minute" => $minutes,
			"second" => $seconds
		]);
	}

	public static function moneyTransaction(Player $player, float|int $money) : bool{
		$moneyAmount = TopStats::getInstance()->getDatabase()->getTemporaryDataValue($player, DataType::MONEY);
		if($moneyAmount !== false && self::validateDataAction($moneyAmount, $money) !== DataAction::NONE){
			return true;
		}
		return false;
	}

	public static function validateDataAction(float|int $before, float|int $after) : int{
		if($before < $after){
			return DataAction::ADDITION;
		}elseif($before > $after){
			return DataAction::SUBTRACTION;
		}
		return DataAction::NONE;
	}
}