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

use CortexPE\Commando\args\RawStringArgument;
use nicholass003\topstats\database\data\DataType;
use nicholass003\topstats\leaderboard\Leaderboard;
use nicholass003\topstats\model\ModelVariant;
use nicholass003\topstats\model\player\PlayerModel;
use nicholass003\topstats\model\text\TextModel;
use nicholass003\topstats\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function in_array;
use function strtolower;

class CreateSubCommand extends TopStatsSubCommand{

	public function prepare() : void{
		$this->setPermission("topstats.command.create");

		$this->registerArgument(0, new RawStringArgument("model"));
		$this->registerArgument(1, new RawStringArgument("type"));
		$this->registerArgument(2, new RawStringArgument("top", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must login to use this command.");
			return;
		}
		if(isset($args["model"])){
			if(isset($args["type"])){
				if(!in_array($args["type"], DataType::ALL, true)){
					$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " " . $args["model"] . " <type> <top>");
					$sender->sendMessage(TextFormat::RED . "Type \"/topstats types\" to get type list");
					return;
				}
				$id = Utils::getNextTopStatsIds();
				switch(strtolower($args["model"])){
					case ModelVariant::PLAYER:
						$leaderboard = new Leaderboard(new PlayerModel($sender->getLocation(), Utils::getTopStatsPlayerSkin($this->plugin->getDatabase()->getTemporaryData(), $args["type"], (int) $args["top"] ?? 1), $id, $args["type"], (int) $args["top"] ?? 1));
						$leaderboard->spawn();
						$this->leaderboardManager->add($leaderboard);
						$sender->sendMessage(TextFormat::GREEN . "Successfully spawn TopStats with model: " . $args["model"] . " type: " . $args["type"] . " top: " . $args["top"]);
						break;
					case ModelVariant::TEXT:
						$leaderboard = new Leaderboard(new TextModel($sender->getPosition(), $id, $args["type"]));
						$leaderboard->spawn();
						$this->leaderboardManager->add($leaderboard);
						$sender->sendMessage(TextFormat::GREEN . "Successfully spawn TopStats with model: " . $args["model"] . " type: " . $args["type"]);
						break;
					default:
						$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " <player|text> <type> <top>");
						break;
				}
			}else{
				$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " " . $args["model"] . " <type> <top>");
			}
		}else{
			$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " <player|text> <type> <top>");
		}
	}
}
