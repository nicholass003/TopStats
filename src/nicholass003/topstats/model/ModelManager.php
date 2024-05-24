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

namespace nicholass003\topstats\model;

class ModelManager{

	/** @var array<int, IModel> */
	private array $models = [];

	public function add(IModel $model) : static{
		$this->models[$model->getId()] = $model;
		return $this;
	}

	public function remove(int $id) : static{
		if(isset($this->models[$id])){
			unset($this->models[$id]);
		}
		return $this;
	}

	public function get(int $id) : ?IModel{
		return $this->models[$id] ?? null;
	}

	/**
	 * @return array<int, IModel>
	 */
	public function models() : array{
		return $this->models;
	}
}
