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

use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\Nicholass003\Textify\Lib\Model\Action;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\Database\IDatabase;
use Nicholass003\TopStats\External\ExternalIntegrationRegistry;
use Nicholass003\TopStats\TopStats;
use Nicholass003\TopStats\Utils\Utils;
use function count;
use function in_array;

/**
 * Represents a single leaderboard instance.
 *
 * Handles rendering, updating, and interaction with
 * data sources (database or external integrations).
 */
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

	/**
	 * @param Model $model
	 */
	public function __construct(
		protected Model $model
	){
		$this->database = TopStats::getInstance()->getDatabase();
		$this->text = TopStats::getInstance()->getConfig()->getNested("models." . $model->getVariant()->value . "." . $this->getType() . ".description");
		$this->title = TopStats::getInstance()->getConfig()->getNested("models." . $model->getVariant()->value . "." . $this->getType() . ".title");
		$this->id = Utils::getNextTopStatsIds();
	}

	/**
	 * Unique runtime identifier of this leaderboard.
	 */
	public function getId() : int{
		return $this->id;
	}

	/**
	 * Leaderboard data type (e.g. "kdr", "kills").
	 */
	public function getType() : string{
		return $this->model->getCompoundTag()->getString(self::TAG_TYPE);
	}

	/**
	 * Underlying model used for rendering.
	 */
	public function getModel() : Model{
		return $this->model;
	}

	/**
	 * Replace the model instance.
	 */
	public function setModel(Model $model) : Leaderboard{
		$this->model = $model;
		return $this;
	}

	/**
	 * Update the displayed text.
	 */
	public function updateText(string $text) : Leaderboard{
		$this->model->setText($text);
		$this->model->update(Action::EDIT);
		return $this;
	}

	/**
	 * Update the displayed title.
	 */
	public function updateTitle(string $title) : Leaderboard{
		$this->model->setTitle($title);
		$this->model->update(Action::EDIT);
		return $this;
	}

	/**
	 * Spawn the leaderboard for all current viewers.
	 *
	 * Sends the model and triggers an initial update.
	 */
	public function spawn() : void{
		$source = ExternalIntegrationRegistry::getInstance()->getSource($this->getType());
		foreach($this->model->getViewers() as $player){
			$this->model->send($player, Action::ADD);
			$this->update($source !== null ? $source->getEntries() : []);
		}
	}

	/**
	 * Whether sorting is handled externally.
	 */
	public function isForceSorting() : bool{
		return $this->forceSorting;
	}

	/**
	 * Enable or disable external sorting.
	 */
	public function setForceSorting(bool $value = true) : void{
		$this->forceSorting = $value;
	}

	/**
	 * Check if this leaderboard uses a custom data type.
	 */
	public function isCustomDataType() : bool{
		return !in_array($this->getType(), DataType::ALL, true);
	}

	/**
	 * Update leaderboard content using provided or database data.
	 *
	 * @param array<string, array<string, int|float|string>> $data
	 */
	public function update(array $data = []) : void{
		if(!$this->isCustomDataType()){
			$data = $this->database->getTemporaryData();
		}

		if(count($data) === 0){
			return;
		}

		if(DataType::isDerived($this->getType())){
			$data = Utils::applyDerivedStat($data, $this->getType());
		}

		$this->updateText(Utils::getTopStatsText($data, $this->model, $this->text, self::TYPE_TEXT, $this->forceSorting));
		$this->updateTitle(Utils::getTopStatsText($data, $this->model, $this->title, self::TYPE_TITLE, $this->forceSorting));

		if($this->model instanceof NonPlayerCharacter){
			$skin = Utils::getTopStatsPlayerSkin(
				$data,
				$this->getType(),
				$this->model->getCompoundTag()->getByte(self::TAG_TOP),
				$this->forceSorting
			);
			$this->model->setSkin($skin);
		}
	}

	/**
	 * Serialize leaderboard data for storage.
	 *
	 * @return array{id: int}
	 */
	public function jsonSerialize() : array{
		return [
			"id" => $this->model->getActorId()
		];
	}
}