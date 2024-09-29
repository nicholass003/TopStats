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

use nicholass003\topstats\libs\_819d6c159e0d8f04\CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class DeleteSubCommand extends TopStatsSubCommand{

	protected function prepare() : void{
		$this->setPermission("topstats.command.delete");

		$this->registerArgument(0, new IntegerArgument("id"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must login to use this command.");
			return;
		}
		if(!isset($args["id"])){
			$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " <id>");
			return;
		}
		$id = (int) $args["id"];
		$leaderboard = $this->leaderboardManager->get($id);
		if($leaderboard === null){
			$sender->sendMessage(TextFormat::RED . "TopStats with id {$id} not found");
			return;
		}
		$this->leaderboardManager->remove($id);
		$leaderboard->getModel()->destroy();
		$sender->sendMessage(TextFormat::GREEN . "TopStats with id {$id} successfully removed");
	}
}