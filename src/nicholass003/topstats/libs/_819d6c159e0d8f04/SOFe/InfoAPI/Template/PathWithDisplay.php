<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_819d6c159e0d8f04\SOFe\InfoAPI\Template;

use pocketmine\command\CommandSender;
use Shared\SOFe\InfoAPI\Display;
use nicholass003\topstats\libs\_819d6c159e0d8f04\SOFe\AwaitGenerator\Traverser;

use function count;
use function sprintf;


















































































































































final class PathWithDisplay implements CoalesceChoice {
	public function __construct(
		public ResolvedPath $path,
		public Display $display,
	) {
	}

	public function getPath() : ResolvedPath {
		return $this->path;
	}

	public function getDisplay() : ?Display {
		return $this->display;
	}
}