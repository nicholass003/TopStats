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

namespace nicholass003\topstats\database\data;

final class DataType{

	public const DEATH = "death";
	public const KILL = "kill";
	public const BLOCK_BREAK = "block-break";
	public const BLOCK_PLACE = "block-place";
	public const CHANGE_SKIN = "change-skin";
	public const CHAT = "chat";
	public const CONSUME = "consume";

	public const ALL = [
		self::DEATH,
		self::KILL,
		self::BLOCK_BREAK,
		self::BLOCK_PLACE,
		self::CHANGE_SKIN,
		self::CHAT,
		self::CONSUME
	];
}
