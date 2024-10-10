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

use nicholass003\topstats\libs\_8d917a7f9d1fddb3\CortexPE\Commando\args\IntegerArgument;
use nicholass003\topstats\model\IModel;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_keys;
use function ctype_digit;
use function in_array;

class TeleportSubCommand extends TopStatsSubCommand{

	protected function prepare() : void{
		$this->setPermission("topstats.command.teleport");

		$this->registerArgument(0, new IntegerArgument("id"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must login to use this command.");
			return;
		}
		$leaderboards = $this->leaderboardManager->leaderboards();
		if(isset($args["id"])){
			if(ctype_digit((string) $args["id"])){
				if(in_array((int) $args["id"], array_keys($leaderboards), true)){
					foreach($leaderboards as $id => $leaderboard){
						if($leaderboard->getModel() instanceof IModel && $id === (int) $args["id"]){
							$sender->teleport($leaderboard->getModel()->getPosition());
							$sender->sendMessage(TextFormat::GREEN . "Success teleported to TopStats position with id: {$id}");
							break;
						}
					}
				}else{
					$sender->sendMessage(TextFormat::RED . "Failed to teleported, there are no TopStats with id: " . $args["id"]);
				}
			}else{
				$sender->sendMessage(TextFormat::RED . "ModelID should be a number.");
				$sender->sendMessage(TextFormat::RED . "To get list of TopStats data, type \"/topstats list\"");
			}
		}else{
			$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " <id>");
			$sender->sendMessage(TextFormat::RED . "To get list of TopStats data, type \"/topstats list\"");
		}
	}
}