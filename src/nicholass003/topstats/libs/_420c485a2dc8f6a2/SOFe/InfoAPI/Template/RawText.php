<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_420c485a2dc8f6a2\SOFe\InfoAPI\Template;

use pocketmine\command\CommandSender;
use Shared\SOFe\InfoAPI\Display;
use nicholass003\topstats\libs\_420c485a2dc8f6a2\SOFe\AwaitGenerator\Traverser;

use function count;
use function sprintf;











final class RawText implements TemplateElement {
	public function __construct(public string $raw) {
	}

	public function render(mixed $context, ?CommandSender $sender, GetOrWatch $getOrWatch) : RenderedElement {
		return $getOrWatch->staticElement($this->raw);
	}
}