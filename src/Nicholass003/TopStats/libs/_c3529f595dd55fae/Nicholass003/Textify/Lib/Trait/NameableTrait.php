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

namespace Nicholass003\TopStats\libs\_c3529f595dd55fae\Nicholass003\Textify\Lib\Trait;

use Nicholass003\TopStats\libs\_c3529f595dd55fae\Nicholass003\Textify\Lib\Model\Model;
use pocketmine\nbt\tag\CompoundTag;

trait NameableTrait{

	/** @var string[] */
	private array $texts = [];

	public function getText() : string{
		return $this->texts[Model::TEXT];
	}

	public function setText(string $text) : self{
		$this->texts[Model::TEXT] = $text;
		return $this;
	}

	public function getTitle() : string{
		return $this->texts[Model::TITLE];
	}

	public function setTitle(string $title) : self{
		$this->texts[Model::TITLE] = $title;
		return $this;
	}

	/** @var CompoundTag|null Custom tags for storing Textify model data */
	private ?CompoundTag $tag = null;

	public function getCompoundTag() : CompoundTag{
		return $this->tag ?? new CompoundTag();
	}

	public function setCompoundTag(?CompoundTag $tag) : self{
		$this->tag = $tag;
		return $this;
	}
}