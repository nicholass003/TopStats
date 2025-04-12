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

use nicholass003\topstats\libs\_95ef3006793f2228\CortexPE\Commando\args\RawStringArgument;
use nicholass003\topstats\database\SQLInterface;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AutoSaveSubCommand extends TopStatsSubCommand{

	private const AUTOSAVE_ON = "on";
	private const AUTOSAVE_OFF = "off";

	protected function prepare() : void{
		$this->setPermission("topstats.command.autosave");

		$this->registerArgument(0, new RawStringArgument("toggle"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$database = $this->plugin->getDatabase();
		if(!$database instanceof SQLInterface){
			$sender->sendMessage(TextFormat::RED . "The current Database is not a SQL Database.");
			return;
		}else{
			$autoSave = $database->isAutoSaveActive();
			$this->applyAction($sender, $args["toggle"], $autoSave, $database);
		}
	}

	private function applyAction(CommandSender $sender, string $toggle, bool $autoSave, SQLInterface $database) : void{
		switch($toggle){
			case self::AUTOSAVE_OFF:
				if($autoSave === false){
					$sender->sendMessage(TextFormat::RED . "AutoSave already turned off.");
					return;
				}
				$database->toggleAutoSave(false);
				$sender->sendMessage(TextFormat::GREEN . "AutoSave is now turned off.");
				break;
			case self::AUTOSAVE_ON:
				if($autoSave === true){
					$sender->sendMessage(TextFormat::RED . "AutoSave already turned on.");
					return;
				}
				$database->toggleAutoSave(true);
				$sender->sendMessage(TextFormat::GREEN . "AutoSave is now turned on.");
				break;
			default:
				$sender->sendMessage(TextFormat::RED . "Usage: /topstats autosave <on/off>");
				break;
		}
	}
}