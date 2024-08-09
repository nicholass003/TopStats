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

use nicholass003\topstats\database\data\DataType;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use function array_merge;

class TypeSubCommand extends TopStatsSubCommand{

	protected function prepare() : void{
		$this->setPermission("topstats.command.type");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$sender->sendMessage(TextFormat::YELLOW . "TopStats DataType List:");
		foreach(array_merge(DataType::ALL, $this->plugin->getConfig()->get("custom-data", [])) as $type){
			$sender->sendMessage(TextFormat::GREEN . " - {$type}");
		}
	}
}
