<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI\Template;

use Closure;
use pocketmine\command\CommandSender;
use RuntimeException;
use Shared\SOFe\InfoAPI\Mapping;
use Shared\SOFe\InfoAPI\Parameter;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\AwaitGenerator\Traverser;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI\Ast;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI\Ast\MappingCall;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI\Pathfind;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI\ReadIndices;

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