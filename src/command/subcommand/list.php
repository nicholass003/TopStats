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

namespace nicholass003\topstats\command\subcommand;

use nicholass003\topstats\model\IModel;
use nicholass003\topstats\TopStats;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use function count;

class ListSubCommand extends TopStatsSubCommand{

	protected function prepare() : void{
		$this->setPermission("topstats.command.list");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$leaderboardManager = TopStats::getInstance()->getLeaderboardManager();
		if(count($leaderboardManager->leaderboards()) < 1){
			$sender->sendMessage(TextFormat::RED . "There are no TopStats Model available.");
		}else{
			$sender->sendMessage(TextFormat::YELLOW . "TopStats List:");
			foreach($leaderboardManager->leaderboards() as $id => $leaderboard){
				if(!$leaderboard->getModel() instanceof IModel) continue;
				$sender->sendMessage(TextFormat::GREEN . "- ModelID: {$id}, ModelVariant: " . $leaderboard->getModel()->getVariant() . ", DataType: " . $leaderboard->getModel()->getType());
			}
		}
	}
}
