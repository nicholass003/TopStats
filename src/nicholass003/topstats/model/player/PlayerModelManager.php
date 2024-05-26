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

namespace nicholass003\topstats\model\player;

class PlayerModelManager{

	/** @var array<int, PlayerModel> */
	protected array $playerModels = [];

	public function add(PlayerModel $model) : PlayerModelManager{
		$this->playerModels[$model->getModelId()] = $model;
		return $this;
	}

	public function remove(int $id) : PlayerModelManager{
		if(isset($this->playerModels[$id])){
			unset($this->playerModels[$id]);
		}
		return $this;
	}

	public function get(int $id) : ?PlayerModel{
		return $this->playerModels[$id] ?? null;
	}

	public function models() : array{
		return $this->playerModels;
	}
}
