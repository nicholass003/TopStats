<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\InfoAPI\Ast;

use JsonException;
use Shared\SOFe\InfoAPI\Mapping;
use Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\InfoAPI\QualifiedRef;
use Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\InfoAPI\StringParser;
use function is_numeric;
use function is_string;
use function json_decode;
use function strlen;












































/** Calls a mapping with or without arguments. */
final class MappingCall {
	public function __construct(
		/** The partially qualified reference to the mapping, e.g. `a:b` in `{ a:b(c, d=e) }`. */
		public QualifiedRef $name,
		/** @var Arg[]|null The arguments passed to the mapping, e.g. `c` and `d=e` in `{ a:b(c, d=e) }` */
		public ?array $args,
	) {
	}
}