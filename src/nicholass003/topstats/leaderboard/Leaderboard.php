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

namespace nicholass003\topstats\leaderboard;

use nicholass003\topstats\database\IDatabase;
use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\player\PlayerModel;
use nicholass003\topstats\model\text\TextModel;
use nicholass003\topstats\TopStats;
use nicholass003\topstats\utils\Utils;
use function json_encode;

class Leaderboard{

	protected IDatabase $database;

	protected string $text = "";
	protected string $title = "";

	public const TYPE_TEXT = "text";
	public const TYPE_TITLE = "title";

	protected int $id;

	public function __construct(
		protected IModel $model
	){
		$this->database = TopStats::getInstance()->getDatabase();
		$this->text = TopStats::getInstance()->getConfig()->getNested("models." . $model->getVariant() . "." . $model->getType() . ".description");
		$this->title = TopStats::getInstance()->getConfig()->getNested("models." . $model->getVariant() . "." . $model->getType() . ".title");
		$this->id = $model->getModelId();
	}

	public function getId() : int{
		return $this->id;
	}

	public function getModel() : IModel{
		return $this->model;
	}

	public function setModel(IModel $model) : Leaderboard{
		$this->model = $model;
		return $this;
	}

	public function updateText(string $text) : Leaderboard{
		$this->model->updateText($text);
		return $this;
	}

	public function updateTitle(string $title) : Leaderboard{
		$this->model->updateTitle($title);
		return $this;
	}

	public function spawn() : void{
		if($this->model instanceof TextModel){
			$this->model->spawnToAll();
		}elseif($this->model instanceof PlayerModel){
			$this->model->spawnToAll();
		}
	}

	public function update() : void{
		$this->updateText(Utils::getTopStatsText($this->database->getTemporaryData(), $this->model, $this->text, self::TYPE_TEXT));
		$this->updateTitle(Utils::getTopStatsText($this->database->getTemporaryData(), $this->model, $this->title, self::TYPE_TITLE));
		if($this->model instanceof PlayerModel){
			$skin = Utils::getTopStatsPlayerSkin($this->database->getTemporaryData(), $this->model->getType(), $this->model->getTop());
			if($skin !== null){
				$this->model->setSkin($skin);
			}
		}
	}

	public function toJSON() : string{
		return json_encode([
			"id" => $this->model->getModelId(),
			"model" => $this->model->getVariant(),
			"type" => $this->model->getType(),
			"top" => $this->model instanceof PlayerModel ? $this->model->getTop() : "none",
			"position" => [
				"x" => $this->model->getPosition()->getX(),
				"y" => $this->model->getPosition()->getY(),
				"z" => $this->model->getPosition()->getZ(),
				"world" => $this->model->getPosition()->getWorld()->getFolderName()
			]
		]);
	}
}
