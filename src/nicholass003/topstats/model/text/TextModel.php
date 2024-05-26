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

namespace nicholass003\topstats\model\text;

use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\ModelVariant;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;

class TextModel extends FloatingTextParticle implements IModel{

	protected int $modelID;
	protected string $type;
	protected ?Position $position = null;

	public function __construct(Position $pos, int $id, string $type, string $text = "", string $title = ""){
		parent::__construct($text, $title);
		$this->modelID = $id;
		$this->position = $pos;
		$this->type = $type;
	}

	public function getModelId() : int{
		return $this->modelID;
	}

	public function setModelId(int $id) : TextModel{
		$this->modelID = $id;
		return $this;
	}

	public function getVariant() : string{
		return ModelVariant::TEXT;
	}

	public function getType() : string{
		return $this->type;
	}

	public function getPosition() : Position{
		return $this->position;
	}

	public function updateText(string $text) : TextModel{
		$this->setText($text);
		$this->update();
		return $this;
	}

	public function updateTitle(string $title) : TextModel{
		$this->setTitle($title);
		$this->update();
		return $this;
	}

	public function update() : void{
		$this->position->getWorld()->addParticle($this->position, $this);
	}

	public function destroy() : void{
		$this->setInvisible();
		$this->update();
	}
}
