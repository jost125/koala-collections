<?php

namespace Koala\Collection;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use Koala\Collection\Immutable\ArrayList;

interface ICollection extends IteratorAggregate, Countable, JsonSerializable {

	public function getIterator();
	public function count();
	public function toArray();
	public function isEmpty();
	public function getItems();
	public function push($value);
	public function map(callable $mapCallback);
	public function flatten();
	public function flatMap(callable $mapCallback);
	public function merge(ICollection $list);
	public function unique();
	public function filter(callable $filterCallback);
	public function find(callable $findCallback);
	public function exists(callable $existsCallback);
	public function allMatchCondition(callable $matchCallback);
	public function sort(callable $comparsionCallback);
	public function each(callable $eachCallback);
	public function first();
	public function firstX($numberOfFirstXElements);
	public function last();
	public function removeNulls();
	public function combine(ICollection $values);
	public function jsonSerialize();

}
