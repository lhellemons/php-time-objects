<?php

namespace SolidPhp\Utility;

use IteratorAggregate;
use SolidPhp\ValueObjects\Value\SingleValueObjectTrait;

/**
 * Class Iterator
 *
 * Wrapper class for iterables that can chain lazy operations.
 */
final class Iterator implements IteratorAggregate
{
    public const SPLIT_INCLUDE_BEFORE = SPLIT_INCLUDE_BEFORE;
    public const SPLIT_INCLUDE_AFTER = SPLIT_INCLUDE_AFTER;
    public const SPLIT_INCLUDE_NONE = SPLIT_INCLUDE_NONE;
    public const SPLIT_INCLUDE_DELIMITER = SPLIT_INCLUDE_DELIMITER;

    /** @var iterable */
    private $iterable;

    private function __construct(iterable $iterable)
    {
        $this->iterable = $iterable;
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function create(iterable $iterable): self
    {
        if ($iterable instanceof self) {
            return $iterable;
        }

        return new self($iterable);
    }

    public static function fromEntries(iterable $iterable): self
    {
        return self::create(iterable_from_entries($iterable));
    }

    /**
     * Returns an endless stream of increasing integers, starting at the given number
     *
     * @param int $start
     *
     * @return Iterator
     */
    public static function integers(int $start = 0): self
    {
        return new self(iterable_integers($start));
    }

    public static function range(float $end, float $start = 0, float $step = 1): self
    {
        return new self(iterable_range($end, $start, $step));
    }

    public static function iterate(callable $valueGenerator, ?callable $keyGenerator = null): self
    {
        return new self(iterable_iterate($valueGenerator, $keyGenerator));
    }

    /**
     * Returns an endless stream of identical values
     *
     * @param      $value
     * @param null $key
     *
     * @return Iterator
     */
    public static function const($value, $key = null): self
    {
        return new self(iterable_const($value, $key));
    }

    /**
     * Returns an endless stream where each value is computed from the last value.
     * If $keyRecursor is given, the key is also computed. If not, the key is simply numerical.
     *
     * @param callable      $recursor     Callable that computes the next value.
     *                                    Signature: ($oldValue, $oldKey) => $newValue
     * @param mixed|null    $initialValue
     *
     * @param callable|null $keyRecursor  . If provided, callable that computes the next key.
     *                                    Signature: ($oldKey, $oldValue) => $newKey
     * @param mixed|null    $initialKey
     *
     * @return Iterator
     */
    public static function recurse(
        callable $recursor,
        $initialValue = null,
        ?callable $keyRecursor = null,
        $initialKey = null
    ): self {
        return new self(iterable_recurse($recursor, $initialValue, $keyRecursor, $initialKey));
    }

    public function isSame(iterable $iterable): bool
    {
        return $this->zipWith($iterable)->all(
            static function (array $values) {
                return $values[0] === $values[1];
            }
        );
    }

    /**
     * Repeats the values of this iterable.
     *
     * @param int|null $number The number of times to repeat the entries of this Iterator. null (default) will
     *                         produce an endless stream
     *
     * @return Iterator
     */
    public function repeat(?int $number = null): self
    {
        return new self(iterable_repeat($this->iterable, $number));
    }

    public function any(?callable $test = null): bool
    {
        return iterable_any($this->iterable, $test ?: static function ($value) { return (bool)$value; });
    }

    public function all(?callable $test = null): bool
    {

        return iterable_all($this->iterable, $test ?: static function ($value) { return (bool)$value; });
    }

    /**
     * Returns the first value, optionally the first that matches a given test
     *
     * @param callable|null $test If given, a callable that tests the entries. The value of the first one
     *                            for which this test returns true or true-equivalent is returned.
     *                            If not given, the first value is returned
     *
     *
     * @return mixed|null The value of the first entry in this Iterator that passes the test, or simply the first.
     *                    If there are no entries or none that pass the test, null is returned.
     */
    public function first(?callable $test = null)
    {
        $results = ($test ? $this->filter($test) : $this)->take(1)->getArray(false);

        return count($results) > 0 ? array_pop($results) : null;
    }

    /**
     * Returns the last value, optionally the last that matches a given test
     *
     * @param callable|null $test If given, a callable that tests the entries. The value of the last one
     *                            for which this test returns true or true-equivalent is returned.
     *                            If not given, the last value is returned
     *
     *
     * @return mixed|null The value of the last entry in this Iterator that passes the test, or simply the last.
     *                    If there are no entries or none that pass the test, null is returned.
     */
    public function last(?callable $test = null)
    {
        $results = ($test ? $this->filter($test) : $this)->getArray(false);

        return count($results) > 0 ? array_shift($results) : null;
    }

    /**
     *
     * @param callable      $mapper
     * @param callable|null $keyMapper
     *
     * @return Iterator
     */
    public function map(callable $mapper, ?callable $keyMapper = null): self
    {
        return new self(iterable_map($mapper, $keyMapper, $this->iterable));
    }

    public function mapKeys(callable $keyMapper): self
    {
        return new self(iterable_map(null, $keyMapper, $this->iterable));
    }

    public function flatten(): self
    {
        return new self(iterable_flatten($this->iterable));
    }

    public function flatMap(callable $mapper, ?callable $keyMapper = null): self
    {
        return new self(iterable_flatmap($this->iterable, $mapper, $keyMapper));
    }

    public function filter(callable $filter): self
    {
        return new self(iterable_filter($filter, $this->iterable));
    }

    /**
     * Returns an Iterator that will yield all values in this Iterator exactly once, skipping any duplicates.
     *
     * By default, the uniqueness of elements is determined by strict equality. If $identityTest is provided, that
     * callback is used instead.
     *
     * This operation is lazy
     *
     * @param callable|null $identityTest If provided, this callable is called in order to determine whether two
     *                                    items are equal. The call signature is (valueA, valueB, keyA, keyB).
     *                                    If the callable returns true, the items are considered equal.
     *
     * @return Iterator
     */
    public function unique(?callable $identityTest = null): self
    {
        return new self(
            $identityTest ?
                iterable_unique_by_test($this->iterable, $identityTest) :
                iterable_unique($this->iterable)
        );
    }

    /**
     * Runs the provided reducer callback on every item in this Iterator, and returns the ultimate result.
     *
     * This operation is not lazy.
     *
     * @param callable $reducer
     * @param mixed    $initialValue
     *
     * @return mixed
     */
    public function reduce(callable $reducer, $initialValue)
    {
        return iterable_reduce($reducer, $this->iterable, $initialValue);
    }

    /**
     * Like reduce, but every intermediate return value of the reducer is yielded.
     *
     * @param callable $reducer
     * @param          $initialValue
     *
     * @return Iterator
     * @see reduce
     */
    public function yieldReduce(callable $reducer, $initialValue): self
    {
        return new self(iterable_yield_reduce($reducer, $this->iterable, $initialValue));
    }

    public function take(int $nr): self
    {
        if ($nr < 0) {
            throw new \DomainException("Can't take fewer than 0 items");
        }

        return new self(iterable_take($nr, $this->iterable));
    }

    public function takeWhile(callable $callback): self
    {
        return new self(iterable_take_while($callback, $this->iterable));
    }

    public function takeUntil(callable $test): self
    {
        return new self(iterable_take_until($test, $this->iterable));
    }

    public function skip(int $nr): self
    {
        if ($nr < 0) {
            throw new \DomainException("Can't skip fewer than 0 items");
        }

        return new self(iterable_skip($nr, $this->iterable));
    }

    public function skipWhile(callable $test): self
    {
        return new self(iterable_skip_while($test, $this->iterable));
    }

    public function skipUntil(callable $test): self
    {
        return new self(iterable_skip_until($test, $this->iterable));
    }

    public function concat(iterable ...$sources): self
    {
        return new self(iterable_concat($this->iterable, ...$sources));
    }

    public function zipWith(iterable ...$sources): self
    {
        return new self(iterable_zip($this->iterable, ...$sources));
    }

    public function group(callable $keyCallback): self
    {
        return new self(iterable_group_by($this->iterable, $keyCallback));
    }

    public function getChunks(int $chunkSize): self
    {
        return new self(iterable_chunks($this->iterable, $chunkSize));
    }

    public function split(callable $delimiterTest, int $flags = self::SPLIT_INCLUDE_NONE): self
    {
        return new self(iterable_split($this->iterable, $delimiterTest, $flags));
    }

    public function sort(?callable $comparator): self
    {
        return new self(iterable_sort($this->iterable, $comparator));
    }

    public static function zip(iterable $source, iterable ...$sources): self
    {
        return self::create($source)->zipWith(...$sources);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->iterable;
    }

    public function getArray(bool $useKeys = true): array
    {
        return iterable_to_array($this->iterable, $useKeys);
    }

    public function getEntries(): self
    {
        return new self(iterable_to_entries($this->iterable));
    }

    public function getValues(): self
    {
        return new self(iterable_values($this->iterable));
    }

    public function getKeys(): self
    {
        return new self(iterable_keys($this->iterable));
    }

    public function groupBy(callable $groupBy, $preserveKeys = false): self
    {
        $results = [];

        foreach ($this as $key => $value) {
            $groupKeys = $groupBy($value, $key);
            if (! is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }
            foreach ($groupKeys as $groupKey) {
                $groupKey = (string) $groupKey;
                $groupKey = is_bool($groupKey) ? (int) $groupKey : $groupKey;
                if (! array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = [];
                }
                if (false != $preserveKeys) {
                    $results[$groupKey][$key] = $value;
                } else {
                    $results[$groupKey][] = $value;
                }

            }
        }

        $result = self::create($results);
        if (! empty($nextGroups)) {
            return $result->groupBy($groupBy, $preserveKeys);
        }

        return $result;
    }

}

function iterable_to_array(iterable $iterable, bool $useKeys = true): array
{
    if (is_array($iterable)) {
        return $useKeys ? $iterable : array_values($iterable);
    }

    return iterator_to_array(to_iterator($iterable), $useKeys);
}

function to_iterator(iterable $iterable): \Iterator
{
    if ($iterable instanceof \Iterator) {
        return $iterable;
    }
    if ($iterable instanceof IteratorAggregate) {
        return $iterable->getIterator();
    }
    if (is_array($iterable)) {
        return new \ArrayIterator($iterable);
    }

    throw new \RuntimeException(sprintf('to_iterator called with %s, which is not an iterable.', gettype($iterable)));
}

function iterable_map(?callable $mapper, ?callable $keyMapper, iterable $source): iterable
{
    foreach ($source as $key => $value) {
        $key = $keyMapper ? $keyMapper($key, $value, $source) : $key;
        $value = $mapper ? $mapper($value, $key, $source) : $value;
        yield $key => $value;
    }
}

function iterable_filter(callable $filter, iterable $source): iterable
{
    foreach ($source as $key => $value) {
        if ($filter($value, $key, $source)) {
            yield $key => $value;
        }
    }
}

function iterable_reduce(callable $reducer, iterable $source, $initialValue = null)
{
    $result = $initialValue;
    foreach ($source as $key => $value) {
        $result = $reducer($result, $value, $key, $source);
    }

    return $result;
}

function iterable_yield_reduce(callable $reducer, iterable $source, $initialValue = null)
{
    $result = $initialValue;
    foreach ($source as $key => $value) {
        $result = $reducer($result, $value, $key, $source);
        yield $result;
    }
}

function iterable_take(int $nr, iterable $source): iterable
{
    $nr = max($nr, 0);
    foreach ($source as $key => $value) {
        if ($nr-- <= 0) {
            return;
        }

        yield $key => $value;
    }
}

function iterable_take_while(callable $test, iterable $source): iterable
{
    foreach ($source as $key => $value) {
        if (!$test($value, $key, $source)) {
            break;
        }

        yield $key => $value;
    }
}

function iterable_take_until(callable $test, iterable $source): iterable
{
    foreach ($source as $key => $value) {
        if ($test($value, $key,$source)) {
            break;
        }

        yield $key => $value;
    }
}

function iterable_skip(int $nr, iterable $source): iterable
{
    $nr = max($nr, 0);
    foreach ($source as $key => $value) {
        if ($nr > 0) {
            $nr--;
        } else {
            yield $key => $value;
        }
    }
}

function iterable_skip_while(callable $test, iterable $source): iterable
{
    $testFailed = false;
    foreach ($source as $key => $value) {
        if ($testFailed = ($testFailed || !$test($value, $key, $source))) {
            yield $key => $value;
        }
    }
}

function iterable_skip_until(callable $test, iterable $source): iterable
{
    $testPassed = false;
    foreach ($source as $key => $value) {
        if ($testPassed = ($testPassed || $test($value, $key, $source))) {
            yield $key => $value;
        }
    }
}

function iterable_zip(iterable ...$sources): iterable
{
    if (count($sources) < 1) {
        return;
    }

    $zipIterator = new \MultipleIterator(\MultipleIterator::MIT_KEYS_NUMERIC || \MultipleIterator::MIT_NEED_ALL);
    foreach ($sources as $source) {
        $zipIterator->attachIterator(to_iterator($source));
    }

    yield from $zipIterator;
}

/** @internal */
class GroupKey
{
    use SingleValueObjectTrait;
}

function iterable_group_by(iterable $source, callable $groupKeyComputer): iterable
{
    $map = new \SplObjectStorage();

    foreach ($source as $key => $value) {
        $groupKey = GroupKey::of($groupKeyComputer($value, $key, $source));
        $currentElement = $map[$groupKey] ?? [];
        $currentElement[] = [$key, $value];
        $map[$groupKey] = $currentElement;
    }

    foreach ($map as $groupKey) {
        yield $groupKey->getValue() => iterable_from_entries($map->offsetGet($groupKey));
    }
}

function iterable_concat(iterable ...$sources): iterable
{
    foreach ($sources as $source) {
        yield from $source;
    }
}

function iterable_integers(int $start = 0): iterable
{
    $nr = $start;
    while (true) {
        yield $nr => $nr;
        $nr++;
    }
}

function iterable_const($value, $key = null): iterable
{
    if ($key) {
        while (true) {
            yield $key => $value;
        }
    } else {
        while (true) {
            yield $value;
        }
    }
}

function iterable_from_entries(iterable $entries): iterable
{
    foreach ($entries as $entry) {
        yield $entry[0] => $entry[1];
    }
}

function iterable_to_entries(iterable $iterable): iterable
{
    foreach ($iterable as $key => $value) {
        yield [$key, $value];
    }
}

function iterable_values(iterable $iterable): iterable
{
    foreach ($iterable as $value) {
        yield $value;
    }
}

function iterable_keys(iterable $iterable): iterable
{
    foreach ($iterable as $key => $value) {
        yield $key;
    }
}

function iterable_repeat(iterable $values, ?int $times = null): iterable
{
    if ($times === null) {
        while (true) {
            yield from $values;
        }
    } else {
        while ($times-- > 0) {
            yield from $values;
        }
    }
}

function iterable_recurse(
    callable $recursor,
    $initialValue = null,
    ?callable $keyRecursor = null,
    $initialKey = null
): iterable {
    $value = $initialValue;

    if ($keyRecursor) {
        $key = $initialKey;
        while (true) {
            yield $key => $value;

            $newValue = $recursor($value, $key);
            $newKey = $keyRecursor($key, $value);

            [$key, $value] = [$newKey, $newValue];
        }
    } else {
        while (true) {
            yield $value;

            $value = $recursor($value);
        }
    }
}

function iterable_chunks(iterable $iterable, int $chunkSize): iterable
{
    if ($chunkSize <= 0) {
        throw new \DomainException('Chunk size must be greater than 0');
    }

    $currentChunk = [];
    foreach ($iterable as $key => $value) {
        $currentChunk[] = [$key, $value];
        if (count($currentChunk) === $chunkSize) {
            yield iterable_from_entries($currentChunk);
            $currentChunk = [];
        }
    }

    if (count($currentChunk) > 0) {
        yield iterable_from_entries($currentChunk);
    }
}

const SPLIT_INCLUDE_NONE = 0;
const SPLIT_INCLUDE_BEFORE = 1;
const SPLIT_INCLUDE_AFTER = 2;
const SPLIT_INCLUDE_DELIMITER = 4;

function iterable_split(iterable $iterable, callable $delimiterTest, int $flags): iterable
{
    $buffer = [];
    foreach ($iterable as $key => $value) {
        if ($delimiterTest($value, $key, $iterable)) {
            if (($flags & SPLIT_INCLUDE_AFTER) === SPLIT_INCLUDE_AFTER) {
                $buffer[] = [$key, $value];
            }

            yield iterable_from_entries($buffer);

            if (($flags & SPLIT_INCLUDE_DELIMITER) === SPLIT_INCLUDE_DELIMITER) {
                yield iterable_from_entries([[$key, $value]]);
            }

            $buffer = [];
            if (($flags & SPLIT_INCLUDE_BEFORE) === SPLIT_INCLUDE_BEFORE) {
                $buffer[] = [$key, $value];
            }
        } else {
            $buffer[] = [$key, $value];
        }
    }

    if (count($buffer) > 0) {
        yield iterable_from_entries($buffer);
    }
}

function iterable_flatten(iterable $iterable): iterable
{
    foreach ($iterable as $key => $value) {
        if (!is_iterable($value)) {
            throw new \DomainException('flatten needs an iterable of iterables');
        }
        yield from $value;
    }
}

function iterable_flatmap(iterable $iterable, callable $mapper, ?callable $keyMapper = null): iterable
{
    foreach ($iterable as $key => $value) {
        if (!is_iterable($value)) {
            throw new \DomainException('flatmap needs an iterable of iterables');
        }
        foreach ($value as $subKey => $subValue) {
            $mappedValue = $mapper($subValue, $subKey, $key);
            $mappedKey = $keyMapper ? $keyMapper($subKey, $subValue, $key) : $subKey;
            yield $mappedKey => $mappedValue;
        }
    }
}

function iterable_unique(iterable $iterable): iterable
{
    $uniqueValues = [];
    foreach ($iterable as $key => $value) {
        if (in_array($value, $uniqueValues, true)) {
            continue;
        }

        $uniqueValues[] = $value;

        yield $key => $value;
    }
}

function iterable_unique_by_test(iterable $iterable, callable $identityTest): iterable
{
    $uniqueEntries = [];
    foreach ($iterable as $key => $value) {
        foreach ($uniqueEntries as $entry) {
            if ($identityTest($value, $entry[1], $key, $entry[0])) {
                continue 2;
            }
        }

        $uniqueEntries[] = [$key, $value];

        yield $key => $value;
    }
}

function iterable_sort(iterable $iterable, ?callable $comparator = null): iterable
{
    $entries = iterable_to_array(iterable_to_entries($iterable));

    $comparator = $comparator ?: static function ($a, $b) {
        return $a <=> $b;
    };

    usort($entries, static function ($a, $b) use ($comparator) {
        return $comparator($a[1], $b[1], $a[0], $b[0]);
    });

    yield from iterable_from_entries($entries);
}

function iterable_iterate(callable $valueGenerator, ?callable $keyGenerator = null): iterable
{
    $index = 0;
    while (true) {
        $key = $keyGenerator ? $keyGenerator($index) : $index;
        yield $key => $valueGenerator($key, $index);
    }
}

function sign(float $nr): int
{
    return $nr <=> 0;
}

function iterable_range(float $end, float $start = 0, float $step = 1): iterable
{
    $value = $start;

    do  {
        yield $value;
        $value += $step;
    } while (sign($end - $value) !== sign($start - $value));
}

function iterable_any(iterable $source, callable $test): bool
{
    foreach ($source as $key => $value) {
        if ($test($value, $key, $source)) {
            return true;
        }
    }

    return false;
}


function iterable_all(iterable $source, callable $test): bool
{
    foreach ($source as $key => $value) {
        if (!$test($value, $key, $source)) {
            return false;
        }
    }

    return true;
}
