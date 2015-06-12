<?php

namespace Koala\Collection\Immutable;

use ArrayIterator;
use JsonSerializable;
use Koala\Collection\ICollection;
use Traversable;

class ArrayList implements ICollection {

	protected $items;

	public function __construct(array $items) {
		$this->items = $items;
	}

	public function getIterator() {
		return new ArrayIterator($this->items);
	}

	public function count() {
		return count($this->items);
	}

	public function isEmpty() {
		return ($this->count() === 0);
	}

	public function getItems() {
		return $this->items;
	}

	public function push($value) {
		$copied = $this->items;
		$copied[] = $value;
		return new static($copied);
	}

	public function map(callable $mapCallback) {
		$extractedProperties = [];
		foreach ($this->items as $key => $item) {
			$extractedProperties[] = $mapCallback($item, $key);
		}
		return new self($extractedProperties);
	}

	public function flatten() {
		return new static($this->doFlatten($this->items));
	}

	public function flatMap(callable $mapCallback) {
		$mapped = $this->map($mapCallback);
		return $mapped->flatten();
	}

	private function doFlatten($items) {
		$flattened = [];
		if ($items instanceof Traversable || is_array($items)) {
			foreach ($items as $item) {
				$flattened = array_merge($flattened, $this->doFlatten($item));
			}
		}
		else {
			$flattened[] = $items;
		}
		return $flattened;
	}

	public function merge(ICollection $list) {
		return new static(array_merge($this->toArray(), $list->toArray()));
	}

	public function unique() {
		$unique = [];
		foreach ($this->items as $item) {
			if (!in_array($item, $unique)) { // o(n^2) !!! optimization needed
				$unique[] = $item;
			}
		}
		return new static($unique);
	}

	public function filter(callable $filterCallback) {
		return new static(array_values(array_filter($this->items, $filterCallback)));
	}

	public function find(callable $findCallback) {
		foreach ($this->items as $item) {
			if ($findCallback($item)) {
				return $item;
			}
		}
		return null;
	}

	public function exists(callable $existsCallback) {
		return $this->find($existsCallback) !== null;
	}

	public function allMatchCondition(callable $matchCallback) {
		return !$this->exists(function ($item) use ($matchCallback) {
			return !$matchCallback($item);
		});
	}

	public function sort(callable $comparsionCallback) {
		$copied = $this->items;
		usort($copied, $comparsionCallback);
		return new static($copied);
	}

	public function toArray() {
		return $this->items;
	}

	public function each(callable $eachCallback) {
		foreach ($this->items as $key => $item) {
			$eachCallback($item, $key);
		}
	}

	public function first() {
		return $this->find(function ($item) {
			return true;
		});
	}

	public function firstX($numberOfFirstXElements) {
		return new static(array_slice($this->items, 0, $numberOfFirstXElements, true));
	}

	public function last() {
		return $this->items[$this->count() - 1];
	}

	public function removeNulls() {
		return $this->filter(function ($item) {
			return $item !== null;
		});
	}

	public function combine(ICollection $values) {
		$valuesArray = $values->toArray();
		return new Map($this->map(function($item, $key) use ($valuesArray) {
			return [$item, $valuesArray[$key]];
		})->toArray());
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return $this->map(function($item) {
			return $item instanceof JsonSerializable ? $item->jsonSerialize() : $item;
		})->toArray();
	}
}
