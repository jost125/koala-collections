<?php

namespace Koala\Collection\Immutable;

use InvalidArgumentException;
use Koala\Collection\IMap;

class Map implements IMap {

	private $keys;
	private $values;
	private $items;

	public function __construct(array $items) {
		$this->keys = [];
		$this->values = [];
		$this->items = $items;

		foreach ($items as $keyValueArray) {
			list($key, $value) = $keyValueArray;
			$keyHash = $this->hashKey($key);
			$this->keys[$keyHash] = $key;
			$this->values[$keyHash] = $value;
		};
	}

	public function put($key, $value) {
		return $this->merge(new self([[$key, $value]]));
	}

	public function getKeyList() {
		return new ArrayList(array_values($this->keys));
	}

	public function getValueList() {
		return $this->getKeyList()->map(function($key) {
			return $this->getValue($key);
		});
	}

	public function getValue($key) {
		if (!array_key_exists($this->hashKey($key), $this->values)) {
			throw new InvalidArgumentException("key does not exist");
		}
		return $this->values[$this->hashKey($key)];
	}

	public function getValueOrDefault($key, $defaultValue) {
		if (!array_key_exists($this->hashKey($key), $this->values)) {
			return $defaultValue;
		}
		return $this->values[$this->hashKey($key)];
	}

	public function count() {
		return count($this->values);
	}

	public function isEmpty() {
		return ($this->count() === 0);
	}

	public function exists(callable $existsCallback) {
		foreach ($this->getKeyList() as $key) {
			$value = $this->getValue($key);
			if ($existsCallback($value, $key)) {
				return true;
			}
		}
		return false;
	}

	public function map(callable $mapCallback) {
		$mapped = [];
		$this->each(function($value, $key) use (&$mapped, $mapCallback) {
			$mapped[] = $mapCallback($value, $key);
		});

		return new ArrayList($mapped);
	}

	public function getItems() {
		return new ArrayList($this->items);
	}

	public function merge(IMap $map) {
		return new static(array_merge($this->getItems()->toArray(), $map->getItems()->toArray()));
	}

	public function each(callable $eachCallback) {
		foreach ($this->getKeyList() as $key) {
			$value = $this->getValue($key);
			$eachCallback($value, $key);
		}
	}

	public function filter(callable $filterCallback) {
		$filtered = [];
		$this->each(function($item, $key) use ($filterCallback, &$filtered) {
			if ($filterCallback($item, $key)) {
				$filtered[] = [$key, $item];
			}
		});
		return new static($filtered);
	}

	public function firstKey() {
		return $this->findKey(function ($item) {
			return true;
		});
	}

	public function firstValue() {
		return $this->findValue(function ($item) {
			return true;
		});
	}

	public function findKey(callable $matchCallback) {
		foreach ($this->getKeyList() as $key) {
			$value = $this->getValue($key);
			if ($matchCallback($value, $key)) {
				return $key;
			}
		}
		return null;
	}

	public function findValue(callable $matchCallback) {
		foreach ($this->getKeyList() as $key) {
			$value = $this->getValue($key);
			if ($matchCallback($value, $key)) {
				return $value;
			}
		}
		return null;
	}

	public function flip() {
		return $this->getValueList()->combine($this->getKeyList());
	}

	public function sortKeys(callable $comparisonCallback) {
		$keys = $this->getKeyList();
		$sortedKeys = $keys->sort($comparisonCallback);

		return new static($sortedKeys->map(function($key) {
			return [$key, $this->getValue($key)];
		})->toArray());
	}

	public function sortValues(callable $comparisonCallback) {
		return $this->flip()->sortKeys($comparisonCallback)->flip();
	}

	private function hashKey($key) {
		if (is_array($key)) {
			throw new InvalidArgumentException("Keys of map items must be Object or scalar, array given");
		}
		return is_object($key) ? spl_object_hash($key) : $key;
	}

}
