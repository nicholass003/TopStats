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

use nicholass003\topstats\database\data\DataType;
use nicholass003\topstats\database\IDatabase;
use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\player\PlayerModel;
use nicholass003\topstats\model\text\TextModel;
use nicholass003\topstats\TopStats;
use nicholass003\topstats\utils\Utils;
use function in_array;
use function json_encode;

class Leaderboard{

	protected IDatabase $database;

	protected string $text = "";
	protected string $title = "";

	public const TYPE_TEXT = "text";
	public const TYPE_TITLE = "title";

	private bool $forceSorting = false;

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

	public function isForceSorting() : bool{
		return $this->forceSorting;
	}

	public function setForceSorting(bool $value = true) : void{
		$this->forceSorting = $value;
	}

	public function isCustomDataType() : bool{
		return !in_array($this->model->getType(), DataType::ALL, true);
	}

	public function update(array $data = []) : void{
		if(!$this->isCustomDataType()){
			$data = $this->database->getTemporaryData();
		}
		$this->updateText(Utils::getTopStatsText($data, $this->model, $this->text, self::TYPE_TEXT, $this->forceSorting));
		$this->updateTitle(Utils::getTopStatsText($data, $this->model, $this->title, self::TYPE_TITLE, $this->forceSorting));
		if($this->model instanceof PlayerModel){
			$skin = Utils::getTopStatsPlayerSkin($data, $this->model->getType(), $this->model->getTop());
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
