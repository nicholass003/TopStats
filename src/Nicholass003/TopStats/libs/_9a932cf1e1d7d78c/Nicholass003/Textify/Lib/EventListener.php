<?php

/*
 * Copyright (c) 2024 - present nicholass003
 *   _______        _   _  __
 *  |__   __|      | | (_)/ _|
 *     | | _____  _| |_ _| |_ _   _
 *     | |/ _ \ \/ / __| |  _| | | |
 *     | |  __/>  <| |_| | | | |_| |
 *     |_|\___/_/\_\ __|_|_|  \__, |
 *                             __/ |
 *                            |___/
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

namespace Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib;

use Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\Exception\TextifyException;
use Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\world\World;
use function array_values;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function json_decode;
use function json_encode;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

























































































































class EventListener implements Listener{

	/** @var TextifyFactory */
	private TextifyFactory $factory;

	public function __construct(){
		$this->factory = TextifyFactory::getInstance();
	}

	/**
	 * @priority LOWEST
	 */
	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		$this->factory->despawnAllFrom($player);
	}
}