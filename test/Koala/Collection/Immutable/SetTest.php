<?php

namespace Koala\Collection\Immutable;

use PHPUnit_Framework_TestCase;

class SetTest extends PHPUnit_Framework_TestCase {

	public function testUniqueConstruct() {
		$s = new Set([1, 1, 2, 3, 4, 4, 1]);
		$this->assertEquals(new Set([1, 2, 3, 4]), $s);
	}

	public function testIteration() {
		$a = new Set(range(1, 10));
		$i = 0;
		foreach ($a as $key => $value) {
			$this->assertEquals($i++, $key);
			$this->assertEquals($i, $value);
		}
		$this->assertEquals(10, $i);
	}

	public function testCount() {
		$a = new Set(range(1, 10));
		$this->assertEquals(10, $a->count());
	}

	public function testIsEmpty() {
		$this->assertFalse((new Set(range(1, 10)))->isEmpty());
		$this->assertTrue((new Set([]))->isEmpty());
	}

	public function testGetItems() {
		$this->assertEquals(range(1, 10), (new Set(range(1, 10)))->getItems());
	}

	public function testPush() {
		$a = new Set(range(1, 10));
		$b = $a->push(11);
		$this->assertEquals(range(1, 10), $a->getItems());
		$this->assertEquals(range(1, 11), $b->getItems());
		$this->assertNotSame($a, $b);
	}

	public function testMap() {
		$a = new Set(range(1, 10));
		$result = $a->map(function($i) {
			return $i * 2;
		});
		$this->assertEquals(new Set(range(2, 20, 2)), $result);
	}

	public function testFlatten() {
		$a = new Set([
			[1, 2],
			[],
			[3, [4, 5]]
		]);
		$this->assertEquals(new Set([1, 2, 3, 4, 5]), $a->flatten());
	}

	public function testFlatMap() {
		$a = new Set(range(1, 5));
		$result = $a->flatMap(function($i) {
			return range(1, $i);
		});
		$this->assertEquals(new Set([1, 1, 2, 1, 2, 3, 1, 2, 3, 4, 1, 2, 3, 4, 5]), $result);
	}

	public function testFlatMapWithLists() {
		$a = new Set(range(1, 5));
		$result = $a->flatMap(function($i) {
			return new Set(range(1, $i));
		});
		$this->assertEquals(new Set([1, 1, 2, 1, 2, 3, 1, 2, 3, 4, 1, 2, 3, 4, 5]), $result);
	}

	public function testMerge() {
		$a = new Set(range(1, 5));
		$b = new Set(range(3, 7));
		$this->assertEquals(new Set([1, 2, 3, 4, 5, 3, 4, 5, 6, 7]), $a->merge($b));
	}

	public function testUnique() {
		$a = new Set([1, 1, 2]);
		$b = $a->unique();
		$this->assertEquals(new Set([1, 1, 2]), $a);
		$this->assertEquals(new Set([1, 2]), $b);
	}

	public function testFilter() {
		$a = new Set(range(1, 10));
		$result = $a->filter(function ($i) {
			return $i % 2 === 0;
		});
		$this->assertEquals(new Set(range(2, 10, 2)), $result);
	}

	public function testFind() {
		$a = new Set(range(1, 10));
		$this->assertEquals(5, $a->find(function ($i) {
			return $i > 4;
		}));
		$this->assertNull($a->find(function ($i) {
			return $i > 10;
		}));
	}

	public function testExists() {
		$a = new Set(range(1, 10));
		$this->assertTrue($a->exists(function ($i) {
			return $i > 4;
		}));
		$this->assertFalse($a->exists(function ($i) {
			return $i > 10;
		}));
	}

	public function testAllMatchCondition() {
		$a = new Set(range(1, 10));
		$this->assertTrue($a->allMatchCondition(function ($i) {
			return $i < 11 && $i > 0;
		}));
		$this->assertFalse($a->allMatchCondition(function ($i) {
			return $i < 11 && $i > 1;
		}));
	}

	public function testSort() {
		$a = new Set([3, 2, 9, 7, 1]);
		$this->assertEquals(new Set([1, 2, 3, 7, 9]), $a->sort(function ($first, $second) {
			return $first - $second;
		}));
	}

	public function testEach() {
		$a = new Set(range(1, 5));
		$i = 1;
		$a->each(function ($item) use (&$i) {
			$this->assertEquals($i++, $item);
		});
		$this->assertEquals(6, $i);
	}

	public function testFirst() {
		$a = new Set(range(1, 5));
		$this->assertEquals(1, $a->first());
	}

	public function testFirstX() {
		$a = new Set(range(1, 5));
		$this->assertEquals(new Set([1, 2, 3]), $a->firstX(3));
	}

	public function testLast() {
		$a = new Set(range(1, 5));
		$this->assertEquals(5, $a->last());
	}

	public function testRemoveNulls() {
		$a = new Set([null, null, 3, 4, null]);
		$this->assertEquals(new Set([3, 4]), $a->removeNulls());
	}

	public function testCombine() {
		$a = new Set(range(1, 3));
		$b = new Set(range(11, 13));
		$this->assertEquals(new Map([[1, 11], [2, 12], [3, 13]]), $a->combine($b));
	}

	public function testJsonSerialize() {
		$a = new Set([
			new Set([1, 2]),
			new Set([3, 4]),
		]);
		$this->assertEquals([[1, 2], [3, 4]], $a->jsonSerialize());
	}

}
