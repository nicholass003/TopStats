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

use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use Nicholass003\TopStats\Database\Data\DataAction;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\Leaderboard\Leaderboard;
use Nicholass003\TopStats\TopStats;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\SOFe\InfoAPI\InfoAPI;
use function count;
use function floor;
use function is_numeric;
use function random_bytes;
use function round;
use function str_repeat;
use function strlen;
use function uasort;

/**
 * Utility helpers for leaderboard processing and formatting.
 */
class Utils{

	/**
	 * Sort data in descending order based on the given type key.
	 *
	 * @param array<string, array<string, int|float|string>> $data
	 * @param string                                         $type
	 * @return array<string, array<string, int|float|string>>
	 */
	public static function getSortedArrayBoard(array $data, string $type) : array{
		uasort($data, function($a, $b) use($type) {
			return $b[$type] <=> $a[$type];
		});
		return $data;
	}

	/**
	 * Generate formatted leaderboard text.
	 *
	 * Applies sorting (unless forced), limits entries,
	 * and renders text using the provided template.
	 *
	 * @param array<string, array<string, int|float|string>> $data
	 * @param Model                                          $model
	 * @param string                                         $text
	 * @param string                                         $textType
	 * @param bool                                           $forceSorting
	 * @return string
	 */
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
				Leaderboard::TYPE_TITLE => self::validateTextFormat(
					$model->getCompoundTag()->getString(Leaderboard::TAG_TYPE),
					["name" => "Unknown", $model->getCompoundTag()->getString(Leaderboard::TAG_TYPE) => 0],
					$text,
					$num
				),
				Leaderboard::TYPE_TEXT => InfoAPI::render(
					TopStats::getInstance(),
					TopStats::getInstance()->getConfig()->get("no-data-found-text", Leaderboard::NO_DATA_FOUND),
					["line" => "\n"]
				)
			};
		}
		return $result;
	}

	/**
	 * Get the skin of a player at a specific leaderboard rank.
	 *
	 * Falls back to offline data or a default generated skin.
	 *
	 * @param array<string, array<string, int|float|string>> $data
	 * @param string                                         $type
	 * @param int                                            $top
	 * @param bool                                           $forceSorting
	 * @return Skin
	 */
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

	/**
	 * Get the next leaderboard ID based on current count.
	 */
	public static function getNextTopStatsIds() : int{
		return count(TopStats::getInstance()->getLeaderboardManager()->leaderboards());
	}

	/**
	 * Format a leaderboard entry into text.
	 *
	 * Applies value formatting (time or numeric) before rendering.
	 *
	 * @param string                          $type
	 * @param array<string, int|float|string> $data
	 * @param string                          $text
	 * @param int                             $rank
	 * @return string
	 */
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

	/**
	 * Convert seconds into a formatted duration string.
	 */
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

	/**
	 * Check if a money value change represents a valid transaction.
	 */
	public static function moneyTransaction(Player $player, float|int $money) : bool{
		$moneyAmount = TopStats::getInstance()->getDatabase()->getTemporaryDataValue($player, DataType::MONEY);
		if($moneyAmount !== false && self::validateDataAction($moneyAmount, $money) !== DataAction::NONE){
			return true;
		}
		return false;
	}

	/**
	 * Determine the type of change between two values.
	 *
	 * @return int One of DataAction constants
	 */
	public static function validateDataAction(float|int $before, float|int $after) : int{
		if($before < $after){
			return DataAction::ADDITION;
		}elseif($before > $after){
			return DataAction::SUBTRACTION;
		}
		return DataAction::NONE;
	}

	/**
	 * Apply derived statistics (e.g. KDR) to the dataset.
	 *
	 * @param array<string, array<string, int|float>> $data
	 * @param string                                  $type
	 * @return array<string, array<string, int|float>>
	 */
	public static function applyDerivedStat(array $data, string $type) : array{
		switch($type){
			case DataType::KDR:
				foreach($data as $xuid => &$stats){
					$kill = $stats[DataType::KILL] ?? 0;
					$death = $stats[DataType::DEATH] ?? 0;

					$stats[DataType::KDR] = $death <= 0 ? (float) $kill : round($kill / $death, 2);
				}
			break;
		}
		return $data;
	}
}