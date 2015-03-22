<?php

namespace Koala\Collection;

interface IMap {

	public function put($key, $value);
	public function getKeyList();
	public function getValueList();
	public function getValue($key);
	public function getValueOrDefault($key, $defaultValue);
	public function count();
	public function isEmpty();
	public function exists(callable $existsCallback);
	public function map(callable $mapCallback);
	public function getItems();
	public function merge(IMap $map);
	public function each(callable $eachCallback);
	public function filter(callable $filterCallback);
	public function firstKey();
	public function firstValue();
	public function findKey(callable $matchCallback);
	public function findValue(callable $matchCallback);
	public function flip();
	public function sortKeys(callable $comparisonCallback);
	public function sortValues(callable $comparisonCallback);

}
