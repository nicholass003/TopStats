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

namespace Nicholass003\TopStats\Leaderboard;

use Nicholass003\TopStats\libs\_60066bca077282b2\Nicholass003\Textify\Lib\Model\Action;
use Nicholass003\TopStats\libs\_60066bca077282b2\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_60066bca077282b2\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\Database\IDatabase;
use Nicholass003\TopStats\TopStats;
use Nicholass003\TopStats\Utils\Utils;
use function count;
use function in_array;

class Leaderboard implements \JsonSerializable{

	protected IDatabase $database;

	protected string $text = "";
	protected string $title = "";

	public const TYPE_TEXT = "text";
	public const TYPE_TITLE = "title";

	public const TAG_TYPE = "TopStatsType"; //TAG_String
	public const TAG_TOP = "TopStatsTop"; //TAG_Byte

	public const NO_DATA_FOUND = "No records found-looks like the battlefield is yours to conquer!{line}Are you ready to rise to the top?";

	private bool $forceSorting = false;

	protected int $id;

	public function __construct(
		protected Model $model
	){
		$this->database = TopStats::getInstance()->getDatabase();
		$this->text = TopStats::getInstance()->getConfig()->getNested("models." . $model->getVariant()->value . "." . $model->getCompoundTag()->getString(self::TAG_TYPE) . ".description");
		$this->title = TopStats::getInstance()->getConfig()->getNested("models." . $model->getVariant()->value . "." . $model->getCompoundTag()->getString(self::TAG_TYPE) . ".title");
		$this->id = Utils::getNextTopStatsIds();
	}

	public function getId() : int{
		return $this->id;
	}

	public function getModel() : Model{
		return $this->model;
	}

	public function setModel(Model $model) : Leaderboard{
		$this->model = $model;
		return $this;
	}

	public function updateText(string $text) : Leaderboard{
		$this->model->setText($text);
		$this->model->update(Action::EDIT);
		return $this;
	}

	public function updateTitle(string $title) : Leaderboard{
		$this->model->setTitle($title);
		$this->model->update(Action::EDIT);
		return $this;
	}

	public function spawn() : void{
		foreach($this->model->getViewers() as $player){
			$this->model->send($player, Action::ADD);
			$this->update();
		}
	}

	public function isForceSorting() : bool{
		return $this->forceSorting;
	}

	public function setForceSorting(bool $value = true) : void{
		$this->forceSorting = $value;
	}

	public function isCustomDataType() : bool{
		return !in_array($this->model->getCompoundTag()->getString(self::TAG_TYPE), DataType::ALL, true);
	}

	public function update(array $data = []) : void{
		if(!$this->isCustomDataType()){
			$data = $this->database->getTemporaryData();
		}

		if(count($data) === 0){
			return;
		}

		$this->updateText(Utils::getTopStatsText($data, $this->model, $this->text, self::TYPE_TEXT, $this->forceSorting));
		$this->updateTitle(Utils::getTopStatsText($data, $this->model, $this->title, self::TYPE_TITLE, $this->forceSorting));
		if($this->model instanceof NonPlayerCharacter){
			$skin = Utils::getTopStatsPlayerSkin($data, $this->model->getCompoundTag()->getString(self::TAG_TYPE), $this->model->getCompoundTag()->getByte(self::TAG_TOP));
			$this->model->setSkin($skin);
		}
	}

	public function jsonSerialize() : array{
		return [
			"id" => $this->model->getActorId()
		];
	}
}