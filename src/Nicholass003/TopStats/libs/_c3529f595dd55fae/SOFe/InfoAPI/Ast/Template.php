<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\InfoAPI\Ast;

use JsonException;
use Shared\SOFe\InfoAPI\Mapping;
use Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\InfoAPI\QualifiedRef;
use Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\InfoAPI\StringParser;
use function is_numeric;
use function is_string;
use function json_decode;
use function strlen;

/** The entire template string. */
final class Template {
	public function __construct(
		/** @var (RawText|Expr)[] */
		public array $elements,
	) {
	}
}