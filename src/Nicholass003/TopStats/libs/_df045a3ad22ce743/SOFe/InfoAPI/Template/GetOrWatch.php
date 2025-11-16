<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_df045a3ad22ce743\SOFe\InfoAPI\Template;

use Closure;
use pocketmine\command\CommandSender;
use RuntimeException;
use Shared\SOFe\InfoAPI\Mapping;
use Shared\SOFe\InfoAPI\Parameter;
use Nicholass003\TopStats\libs\_df045a3ad22ce743\SOFe\AwaitGenerator\Traverser;
use Nicholass003\TopStats\libs\_df045a3ad22ce743\SOFe\InfoAPI\Ast;
use Nicholass003\TopStats\libs\_df045a3ad22ce743\SOFe\InfoAPI\Ast\MappingCall;
use Nicholass003\TopStats\libs\_df045a3ad22ce743\SOFe\InfoAPI\Pathfind;
use Nicholass003\TopStats\libs\_df045a3ad22ce743\SOFe\InfoAPI\ReadIndices;

use function array_keys;
use function array_map;
use function count;
use function implode;
use function json_decode;
use function range;
use function sprintf;




























































































































































































































/**
 * @template R of RenderedElement
 * @template G of RenderedGroup
 */
interface GetOrWatch {
	/**
	 * @param R[] $elements
	 * @return G
	 */
	public function buildResult(array $elements) : RenderedGroup;

	/**
	 * @return EvalChain<R>
	 */
	public function startEvalChain() : EvalChain;

	/**
	 * @return R
	 */
	public function staticElement(string $raw) : RenderedElement;
}