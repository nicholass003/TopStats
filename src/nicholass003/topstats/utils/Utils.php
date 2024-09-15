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
use function count;
use function floor;
use function str_replace;
use function uasort;

class Utils{

	public static function getSortedArrayBoard(array $data, string $type) : array{
		uasort($data, function($a, $b) use($type) {
			return $b[$type] <=> $a[$type];
		});
		return $data;
	}

	public static function getTopStatsText(array $data, IModel $model, string $text, string $textType, bool $forceSorting = false) : string{
		$result = "";
		$num = 1;
		$max = TopStats::getInstance()->getMaxList();
		if($textType === Leaderboard::TYPE_TITLE){
			$max = 1;
		}
		if(!$forceSorting){
			$data = self::getSortedArrayBoard($data, $model->getType());
		}
		foreach($data as $xuid => $userData){
			if($model instanceof PlayerModel){
				if($num === $model->getTop()){
					$result = self::validateTextFormat($model->getType(), $userData, $text, $num);
					break;
				}
			}else{
				$result .= self::validateTextFormat($model->getType(), $userData, $text, $num);
				if($num >= $max){
					break;
				}
			}
			++$num;
		}
		return $result;
	}

	public static function getTopStatsPlayerSkin(array $data, string $type, int $top) : ?Skin{
		$playerName = "";
		$num = 1;
		foreach(self::getSortedArrayBoard($data, $type) as $xuid => $userData){
			if($num === $top){
				$playerName = $userData["name"];
				break;
			}
			++$num;
		}

		$player = TopStats::getInstance()->getServer()->getPlayerByPrefix($playerName);
		if($player !== null){
			return Human::parseSkinNBT($player->getSaveData());
		}else{
			$playerData = TopStats::getInstance()->getServer()->getOfflinePlayerData($playerName);
			return $playerData !== null ? Human::parseSkinNBT($playerData) : null;
		}
	}

	public static function getNextTopStatsIds() : int{
		return count(TopStats::getInstance()->getLeaderboardManager()->leaderboards());
	}

	public static function validatePlayerModels(Leaderboard $leaderboard) : void{
		foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
			$garbageModels = [];
			$model = $leaderboard->getModel();
			foreach($world->getEntities() as $entity){
				if(($entity instanceof PlayerModel || $entity instanceof TextModel) &&
				$entity->getModelId() === $leaderboard->getId() &&
				$entity->getPosition()->equals($model->getPosition())){
					$garbageModels[] = $entity;
				}
			}
			if(count($garbageModels) > 1){
				$num = 1;
				foreach($garbageModels as $garbageModel){
					if($num === count($garbageModels)){
						$leaderboard->setModel($garbageModel);
						break;
					}
					$garbageModel->flagForDespawn();
					++$num;
				}
			}
		}
	}

	public static function validateTextFormat(string $type, array $data, string $text, int $rank) : string{
		$formattedData = $data[$type];
		if($type === DataType::ONLINE_TIME){
			$formattedData = self::timeFormat($data[$type]);
		}
		return str_replace(["{player}", "{" . $type . "}", "{rank_" . $type . "}", "{line}"], [$data["name"], $formattedData, $rank, "\n"], $text);
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
		return str_replace(["{year}", "{month}", "{week}", "{day}", "{hour}", "{minute}", "{second}"], [$years, $months, $weeks, $days, $hours, $minutes, $seconds], $format);
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
