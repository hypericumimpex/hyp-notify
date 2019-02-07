<?php declare(strict_types = 1);

namespace PHPStan\PhpDocParser\Ast\ConstExpr;

class ConstExprArrayItemNode implements ConstExprNode
{

	/** @var null|ConstExprNode */
	public $key;

	/** @var ConstExprNode */
	public $value;

	public function __construct(ConstExprNode $key = null, ConstExprNode $value)
	{
		$this->key = $key;
		$this->value = $value;
	}


	public function __toString(): string
	{
		if ($this->key !== null) {
			return "{$this->key} => {$this->value}";

		} else {
			return "{$this->value}";
		}
	}

}
