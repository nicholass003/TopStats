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

namespace nicholass003\topstats\task;

use nicholass003\topstats\database\data\DataAction;
use nicholass003\topstats\database\data\DataType;
use nicholass003\topstats\TopStats;
use nicholass003\topstats\utils\Utils;
use pocketmine\scheduler\Task;

class UpdateTask extends Task{

	public function __construct(
		protected TopStats $plugin
	){}

	public function onRun() : void{
		foreach($this->plugin->getLeaderboardManager()->leaderboards() as $id => $leaderboard){
			Utils::validatePlayerModels($leaderboard);
			$leaderboard->update();
		}
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($player->isConnected() && $player->spawned){
				$this->plugin->getDatabase()->update($player, [DataType::ONLINE_TIME => 1], DataAction::ADDITION);
			}
			$economyProvider = $this->plugin->getEconomyProvider();
			if($economyProvider !== null){
				$economyProvider->getMoney($player, function($money) use($player) : void{
					if(Utils::moneyTransaction($player, $money)){
						if($this->plugin->getDatabase()->getTemporaryDataValue($player, DataType::MONEY) !== false){
							$this->plugin->getDatabase()->update(
								$player,
								[DataType::MONEY => $money],
								DataAction::NONE
							);
						}
					}
				});
			}
		}
	}
}
