<?php

namespace Koala\Collection\Immutable;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;

class MapTest extends PHPUnit_Framework_TestCase {

	public function testPut() {
		$map1 = new Map([]);
		$map2 = $map1->put(1, 1);
		$map3 = $map2->put(2, 2);

		$this->assertEquals([], $map1->getItems()->toArray());
		$this->assertEquals([[1, 1]], $map2->getItems()->toArray());
		$this->assertEquals([[1, 1], [2, 2]], $map3->getItems()->toArray());
	}

	public function testGetKeyList() {
		$a = new ArrayList(range(1, 3));
		$b = new ArrayList(range(11, 13));
		$map = $a->combine($b);
		$this->assertEquals(new ArrayList(range(1, 3)), $map->getKeyList());
	}

	public function testGetValueList() {
		$a = new ArrayList(range(1, 3));
		$b = new ArrayList(range(11, 13));
		$map = $a->combine($b);
		$this->assertEquals(new ArrayList(range(11, 13)), $map->getValueList());
	}

	public function testGetValue() {
		$a = new ArrayList(range(1, 3));
		$b = new ArrayList(range(11, 13));
		$map = $a->combine($b);
		$this->assertEquals(12, $map->getValue(2));
	}

	public function testGetValueInvalidOffset() {
		$a = new ArrayList(range(1, 3));
		$b = new ArrayList(range(11, 13));
		$map = $a->combine($b);
		try {
			$map->getValue(4);
			$this->fail("Exception expected");
		} catch (InvalidArgumentException $ex) {
			$this->assertEquals("key does not exist", $ex->getMessage());
		}
	}

	public function testGetValueOrDefault() {
		$a = new ArrayList(range(1, 3));
		$b = new ArrayList(range(11, 13));
		$map = $a->combine($b);
		$this->assertEquals(12, $map->getValueOrDefault(2, 'default'));
		$this->assertEquals('default', $map->getValueOrDefault(4, 'default'));
	}

	public function testCount() {
		$map = new Map([[1, 1], [2, 2]]);
		$this->assertEquals(2, $map->count());
	}

	public function testIsEmpty() {
		$map = new Map([[1, 1], [2, 2]]);
		$this->assertFalse($map->isEmpty());
		$this->assertTrue((new Map([]))->isEmpty());
	}

	public function testExists() {
		$map = new Map([[1, 1], [2, 2]]);
		$this->assertTrue($map->exists(function($item, $key) {
			return $item + $key == 4;
		}));
		$this->assertFalse($map->exists(function($item, $key) {
			return $item + $key == 3;
		}));
	}

	public function testMap() {
		$map = new Map([[1, 1], [2, 2]]);
		$this->assertEquals(new ArrayList([2, 4]), $map->map(function($key, $value) {
			return $key + $value;
		}));
	}

	public function testGetItems() {
		$map = new Map([[1, 1], [2, 2]]);
		$this->assertEquals(new ArrayList([[1, 1], [2, 2]]), $map->getItems());
	}

	public function testMerge() {
		$map1 = new Map([[1, 1], [2, 2]]);
		$map2 = new Map([[4, 4], [5, 5]]);
		$this->assertEquals(new Map([[1, 1], [2, 2], [4, 4], [5, 5]]), $map1->merge($map2));
	}

	public function testEach() {
		$map = new Map([[0, 1], [1, 2]]);
		$i = 0;
		$map->each(function($value, $key) use (&$i) {
			$this->assertEquals($i++, $key);
			$this->assertEquals($i, $value);
		});
		$this->assertEquals($i, 2);
	}

	public function testFilter() {
		$a = new ArrayList(range(1, 4));
		$b = new ArrayList(range(11, 14));
		$map = $a->combine($b);
		$result = $map->filter(function($value, $key) {
			return $value < 14 && $key > 1;
		});
		$this->assertEquals(new Map([[2, 12], [3, 13]]), $result);
	}

	public function testFirstKey() {
		$map = new Map([[2, 3], [3, 4]]);
		$this->assertEquals(2, $map->firstKey());
	}

	public function testFirstValue() {
		$map = new Map([[2, 3], [3, 4]]);
		$this->assertEquals(3, $map->firstValue());
	}

	public function testFindKey() {
		$map = new Map([[2, 3], [3, 4]]);
		$this->assertEquals(3, $map->findKey(function($value, $key) {
			return $value == 4;
		}));
		$this->assertEquals(null, $map->findKey(function($value, $key) {
			return $value == 5;
		}));
	}

	public function testFindValue() {
		$map = new Map([[2, 3], [3, 4]]);
		$this->assertEquals(4, $map->findValue(function($value, $key) {
			return $key == 3;
		}));
		$this->assertEquals(null, $map->findValue(function($value, $key) {
			return $key == 4;
		}));
	}

	public function testFlip() {
		$map = new Map([[3, 4], [2, 3]]);
		$this->assertEquals(new Map([[4, 3], [3, 2]]), $map->flip());
	}

	public function testSortKeys() {
		$map = new Map([[3, 4], [2, 3]]);
		$this->assertEquals(new Map([[2, 3], [3, 4]]), $map->sortKeys(function($first, $second) {
			return $first - $second;
		}));
	}

	public function testSortValues() {
		$map = new Map([[3, 4], [2, 3], [9, 1]]);
		$this->assertEquals(new Map([[9, 1], [2, 3], [3, 4]]), $map->sortValues(function($first, $second) {
			return $first - $second;
		}));
	}

	public function testObjectAsKeys() {
		$key1 = new stdClass();
		$key1->foo = 100;
		$key2 = new stdClass();
		$key2->foo = 200;

		$map = new Map([[$key1, 10], [$key2, 20]]);
		$this->assertEquals(new ArrayList([1000, 4000]), $map->map(function($value, stdClass $key) {
			return $key->foo * $value;
		}));
	}

	public function testArrayAsKeys() {
		try {
			$map = new Map([[[1], 1], [[2], 2]]);
			$this->fail("Exception expected");
		} catch (InvalidArgumentException $ex) {
			$this->assertEquals("Keys of map items must be Object or scalar, array given", $ex->getMessage());
		}
	}

}
