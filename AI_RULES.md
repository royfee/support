# royfee/support - AI Coding Assistant Rules

> This file is the **single source of truth** for all AI coding assistants.
> It documents the `royfee/support` PHP utility package so AI tools can use it correctly.
> Any AI tool (Qoder, Cursor, Copilot, Codex, etc.) can reference this file directly or via tool-specific adapter files.

## Package Info

- **Package**: `royfee/support`
- **Namespace**: `Royfee\Support`
- **Classes**: `Num`, `Str`, `Arr`, `Fs` (all static methods, no instantiation)
- **Install**: `composer require royfee/support`
- **Require**: PHP >= 7.4, ext-bcmath

---

## Critical Rules

1. **Financial calculations MUST use `Num` class** — never use native `+`, `-`, `*`, `/` for money/amount/balance/price/total/discount/fee/payment calculations due to PHP float precision issues.
2. **Always add `use Royfee\Support\{ClassName};`** at the top of the file before using any utility class.
3. Before writing a custom helper function for string/array/number/file operations — **check if this package already provides it**.

---

## Num Class — Numerical Calculations

### Precise Calculations (bcmath) — REQUIRED for Money/Financial

| Method | Signature | Description |
|--------|-----------|-------------|
| `add` | `Num::add($a, $b, int $scale = 2): string` | Precise addition |
| `sub` | `Num::sub($a, $b, int $scale = 2): string` | Precise subtraction |
| `mul` | `Num::mul($a, $b, int $scale = 2): string` | Precise multiplication |
| `div` | `Num::div($a, $b, int $scale = 2): string` | Precise division (throws on zero) |
| `comp` | `Num::comp($a, $b, int $scale = 2): int` | Compare: -1, 0, 1 |
| `eq` | `Num::eq($a, $b, int $scale = 2): bool` | Equal |
| `gt` | `Num::gt($a, $b, int $scale = 2): bool` | Greater than |
| `lt` | `Num::lt($a, $b, int $scale = 2): bool` | Less than |
| `gte` | `Num::gte($a, $b, int $scale = 2): bool` | >= |
| `lte` | `Num::lte($a, $b, int $scale = 2): bool` | <= |
| `toDecimal` | `Num::toDecimal($value, int $scale = 2): string` | To decimal string |

### Statistical

| Method | Signature | Description |
|--------|-----------|-------------|
| `sum` | `Num::sum(array $values, int $scale = 2): string` | Precise sum |
| `average` | `Num::average(array $values, int $scale = 2): string` | Precise average |
| `median` | `Num::median(...$values): float` | Median |
| `product` | `Num::product(array $values): int\|float` | Product |
| `max` | `Num::max(array $values): int\|float` | Maximum |
| `min` | `Num::min(array $values): int\|float` | Minimum |

### Rounding (bcmath)

| Method | Signature | Description |
|--------|-----------|-------------|
| `round` | `Num::round($value, int $precision = 0): string` | Round half up |
| `floor` | `Num::floor($value, int $precision = 0): string` | Round down |
| `ceil` | `Num::ceil($value, int $precision = 0): string` | Round up |

### Range & Formatting

| Method | Signature | Description |
|--------|-----------|-------------|
| `clamp` | `Num::clamp($value, $min, $max): int\|float` | Clamp between range |
| `between` | `Num::between($value, $min, $max): bool` | Is between |
| `format` | `Num::format($number, int $decimals = 2, ...): string` | Thousands separator |
| `currency` | `Num::currency($amount, string $symbol = '$', int $decimals = 2): string` | Currency format |
| `percent` | `Num::percent($value, int $decimals = 0): string` | Percentage format |
| `fileSize` | `Num::fileSize($bytes, int $decimals = 2): string` | Human-readable size |

### Type Checking

| Method | Signature | Description |
|--------|-----------|-------------|
| `isEven` | `Num::isEven(int $value): bool` | Even |
| `isOdd` | `Num::isOdd(int $value): bool` | Odd |
| `isPositive` | `Num::isPositive($value): bool` | > 0 |
| `isNegative` | `Num::isNegative($value): bool` | < 0 |
| `isZero` | `Num::isZero($value): bool` | == 0 |
| `isNumeric` | `Num::isNumeric($value): bool` | Is numeric |
| `isInt` | `Num::isInt($value): bool` | Is integer |
| `isFloat` | `Num::isFloat($value): bool` | Is float |

### Math & Random

| Method | Signature | Description |
|--------|-----------|-------------|
| `gcd` | `Num::gcd(int $a, int $b): int` | Greatest common divisor |
| `lcm` | `Num::lcm(int $a, int $b): int` | Least common multiple |
| `factorial` | `Num::factorial(int $n): int` | Factorial |
| `fibonacci` | `Num::fibonacci(int $n): int` | Nth Fibonacci |
| `power` | `Num::power($base, $exp): int\|float` | Power |
| `sqrt` | `Num::sqrt($value): float` | Square root |
| `random` | `Num::random(int $min, int $max): int` | Random int |
| `randomFloat` | `Num::randomFloat($min, $max, int $decimals): float` | Random float |

---

## Str Class — String Manipulation

All methods are multibyte-safe (UTF-8).

### Case Conversion

| Method | Signature | Description |
|--------|-----------|-------------|
| `upper` | `Str::upper(string $value): string` | UPPER CASE |
| `lower` | `Str::lower(string $value): string` | lower case |
| `ucfirst` | `Str::ucfirst(string $string): string` | First letter upper |
| `lcfirst` | `Str::lcfirst(string $string): string` | First letter lower |
| `title` | `Str::title(string $value): string` | Title Case |
| `headline` | `Str::headline(string $value): string` | Headline Style |
| `camel` | `Str::camel(string $value): string` | camelCase |
| `studly` | `Str::studly(string $value): string` | StudlyCase / PascalCase |
| `snake` | `Str::snake(string $value, string $delimiter = '_'): string` | snake_case |
| `kebab` | `Str::kebab(string $value): string` | kebab-case |

### Search & Replace

| Method | Signature | Description |
|--------|-----------|-------------|
| `contains` | `Str::contains(string $haystack, $needles): bool` | Contains substring |
| `containsAll` | `Str::containsAll(string $haystack, array $needles): bool` | Contains all |
| `doesntContain` | `Str::doesntContain(string $haystack, $needles): bool` | Not contains |
| `startsWith` | `Str::startsWith(string $haystack, $needles): bool` | Starts with |
| `endsWith` | `Str::endsWith(string $haystack, $needles): bool` | Ends with |
| `replace` | `Str::replace($search, $replace, string $subject, bool $caseSensitive = true): string` | Replace |
| `replaceFirst` | `Str::replaceFirst(string $search, string $replace, string $subject): string` | Replace first |
| `replaceLast` | `Str::replaceLast(string $search, string $replace, string $subject): string` | Replace last |
| `remove` | `Str::remove($search, string $subject, bool $caseSensitive = true): string` | Remove |
| `match` | `Str::match(string $pattern, string $subject): string` | Regex match |
| `is` | `Str::is($pattern, string $value): bool` | Wildcard match |

### Extraction

| Method | Signature | Description |
|--------|-----------|-------------|
| `before` | `Str::before(string $subject, string $search): string` | Before first |
| `after` | `Str::after(string $subject, string $search): string` | After first |
| `beforeLast` | `Str::beforeLast(string $subject, string $search): string` | Before last |
| `afterLast` | `Str::afterLast(string $subject, string $search): string` | After last |
| `between` | `Str::between(string $subject, string $from, string $to): string` | Between two |
| `substr` | `Str::substr(string $string, int $start, ?int $length = null): string` | Substring |
| `charAt` | `Str::charAt(string $subject, int $index): string\|false` | Char at index |

### Truncation & Padding

| Method | Signature | Description |
|--------|-----------|-------------|
| `limit` | `Str::limit(string $value, int $limit = 100, string $end = '...'): string` | Limit chars |
| `words` | `Str::words(string $value, int $words = 100, string $end = '...'): string` | Limit words |
| `padLeft` | `Str::padLeft(string $value, int $length, string $pad = ' '): string` | Pad left |
| `padRight` | `Str::padRight(string $value, int $length, string $pad = ' '): string` | Pad right |
| `padBoth` | `Str::padBoth(string $value, int $length, string $pad = ' '): string` | Pad both |

### Utility

| Method | Signature | Description |
|--------|-----------|-------------|
| `length` | `Str::length(string $value, ?string $encoding = null): int` | Length |
| `trim` | `Str::trim(string $string, string $characters = " \t\n\r\0\x0B"): string` | Trim |
| `ltrim` | `Str::ltrim(string $string): string` | Left trim |
| `rtrim` | `Str::rtrim(string $string): string` | Right trim |
| `clean` | `Str::clean(string $string): string` | Alias of trim |
| `squish` | `Str::squish(string $value): string` | Remove extra spaces |
| `reverse` | `Str::reverse(string $value): string` | Reverse |
| `finish` | `Str::finish(string $value, string $cap): string` | Ensure ends with |
| `wrap` | `Str::wrap(string $value, string $before, string $after = ''): string` | Wrap |
| `unwrap` | `Str::unwrap(string $value, string $before, string $after = ''): string` | Unwrap |
| `mask` | `Str::mask(string $string, string $character, int $index = 0, ?int $length = null): string` | Mask |
| `slug` | `Str::slug(string $title, string $separator = '-'): string` | URL slug |
| `ascii` | `Str::ascii(string $value, string $language = 'en'): string` | To ASCII |
| `random` | `Str::random(int $length = 16): string` | Random string |
| `encoding` | `Str::encoding(string $string, string $to = 'utf-8', string $from = 'gb2312'): string` | Convert encoding |
| `substrCount` | `Str::substrCount(string $haystack, string $needle): int` | Count occurrences |
| `toBase64` | `Str::toBase64(string $string): string` | Encode to Base64 |
| `fromBase64` | `Str::fromBase64(string $string): string\|false` | Decode Base64 |

### Validation

| Method | Signature | Description |
|--------|-----------|-------------|
| `isEmpty` | `Str::isEmpty(string $value): bool` | Empty/whitespace |
| `isNotEmpty` | `Str::isNotEmpty(string $value): bool` | Not empty |
| `isJson` | `Str::isJson(string $value): bool` | Valid JSON |
| `isUrl` | `Str::isUrl(string $value): bool` | Valid URL |

---

## Arr Class — Array Operations

Supports "dot" notation for nested array access (e.g., `'user.address.city'`).

### Dot Notation Access

| Method | Signature | Description |
|--------|-----------|-------------|
| `get` | `Arr::get(array $array, $key, $default = null): mixed` | Get by dot key |
| `has` | `Arr::has(array $array, $keys): bool` | Has key (dot notation) |
| `set` | `Arr::set(array &$array, $key, $value): array` | Set by dot key |
| `forget` | `Arr::forget(array &$array, $keys): void` | Remove by dot key |
| `hasAny` | `Arr::hasAny(array $array, $keys): bool` | Any key exists |
| `add` | `Arr::add(array $array, $key, $value): array` | Add if not exists |
| `pull` | `Arr::pull(array &$array, $key, $default = null): mixed` | Get and remove |
| `exists` | `Arr::exists(array $array, $key): bool` | Key exists (no dot) |

### Transformation

| Method | Signature | Description |
|--------|-----------|-------------|
| `only` | `Arr::only(array $array, $keys): array` | Subset of keys |
| `except` | `Arr::except(array $array, $keys): array` | All except keys |
| `flatten` | `Arr::flatten(array $array, $depth = INF): array` | Flatten |
| `collapse` | `Arr::collapse(array $array): array` | Collapse arrays |
| `dot` | `Arr::dot(array $array, string $prepend = ''): array` | To dot notation |
| `undot` | `Arr::undot(array $array): array` | From dot notation |
| `divide` | `Arr::divide(array $array): array` | Split keys/values |
| `wrap` | `Arr::wrap($value): array` | Wrap in array |
| `query` | `Arr::query(array $array, string $numericPrefix = ''): string` | To query string |

### Filtering & Searching

| Method | Signature | Description |
|--------|-----------|-------------|
| `first` | `Arr::first(array $array, ?callable $callback = null, $default = null): mixed` | First matching |
| `last` | `Arr::last(array $array, ?callable $callback = null, $default = null): mixed` | Last matching |
| `where` | `Arr::where(array $array, callable $callback): array` | Filter |
| `reject` | `Arr::reject(array $array, callable $callback): array` | Filter out |
| `whereNotNull` | `Arr::whereNotNull(array $array): array` | Remove nulls |
| `pluck` | `Arr::pluck(array $array, $value, ?string $key = null): array` | Pluck values |

### Grouping & Sorting

| Method | Signature | Description |
|--------|-----------|-------------|
| `groupBy` | `Arr::groupBy(array $array, $groupBy): array` | Group by key/callback |
| `keyBy` | `Arr::keyBy(array $array, $keyBy): array` | Key by field |
| `sort` | `Arr::sort(array $array, int $options = SORT_REGULAR, bool $descending = false): array` | Sort |
| `sortRecursive` | `Arr::sortRecursive(array $array, ...): array` | Recursive sort |
| `map` | `Arr::map(array $array, callable $callback): array` | Map |
| `mapWithKeys` | `Arr::mapWithKeys(array $array, callable $callback): array` | Map with keys |

### Utility

| Method | Signature | Description |
|--------|-----------|-------------|
| `join` | `Arr::join(array $array, string $glue, string $finalGlue = ''): string` | Join |
| `take` | `Arr::take(array $array, int $limit): array` | Take N items |
| `prepend` | `Arr::prepend(array $array, $value, $key = null): array` | Prepend |
| `random` | `Arr::random(array $array, ?int $number = null): mixed` | Random |
| `shuffle` | `Arr::shuffle(array $array): array` | Shuffle |
| `crossJoin` | `Arr::crossJoin(...$arrays): array` | Cross join |
| `isAssoc` | `Arr::isAssoc(array $array): bool` | Is associative |
| `isList` | `Arr::isList(array $array): bool` | Is sequential list |
| `accessible` | `Arr::accessible($value): bool` | Is array accessible |

---

## Fs Class — Filesystem Operations

### File I/O

| Method | Signature | Description |
|--------|-----------|-------------|
| `get` | `Fs::get(string $path, bool $lock = false): string` | Read file |
| `sharedGet` | `Fs::sharedGet(string $path): string` | Read with shared lock |
| `json` | `Fs::json(string $path, int $flags = 0): array` | Read JSON file |
| `put` | `Fs::put(string $path, string $contents, bool $lock = false): int\|bool` | Write file |
| `append` | `Fs::append(string $path, string $data, bool $lock = false): int` | Append |
| `prepend` | `Fs::prepend(string $path, string $data): int` | Prepend |
| `replace` | `Fs::replace(string $path, string $content): void` | Atomic replace |
| `replaceInFile` | `Fs::replaceInFile($search, $replace, string $path): void` | Replace in file |
| `delete` | `Fs::delete($paths): bool` | Delete file(s) |

### File Info

| Method | Signature | Description |
|--------|-----------|-------------|
| `exists` | `Fs::exists(string $path): bool` | Exists |
| `missing` | `Fs::missing(string $path): bool` | Missing |
| `isFile` | `Fs::isFile(string $file): bool` | Is file |
| `isDir` | `Fs::isDir(string $directory): bool` | Is directory |
| `isReadable` | `Fs::isReadable(string $path): bool` | Is readable |
| `isWritable` | `Fs::isWritable(string $path): bool` | Is writable |
| `size` | `Fs::size(string $path): int` | Bytes |
| `lastModified` | `Fs::lastModified(string $path): int` | Timestamp |
| `hash` | `Fs::hash(string $path, string $algorithm = 'md5'): string` | Hash |
| `mimeType` | `Fs::mimeType(string $path): string\|false` | MIME type |
| `name` | `Fs::name(string $path): string` | Filename no ext |
| `basename` | `Fs::basename(string $path): string` | Full filename |
| `extension` | `Fs::extension(string $path): string` | Extension |
| `dirname` | `Fs::dirname(string $path): string` | Directory |
| `copy` | `Fs::copy(string $path, string $target): bool` | Copy file |

### Directory

| Method | Signature | Description |
|--------|-----------|-------------|
| `makeDir` | `Fs::makeDir(string $path, int $mode = 0755, bool $recursive = false): bool` | Create |
| `ensureDirectoryExists` | `Fs::ensureDirectoryExists(string $path, int $mode = 0755, bool $recursive = true): void` | Ensure exists |
| `deleteDir` | `Fs::deleteDir(string $directory, bool $preserve = false): bool` | Delete dir |
| `cleanDirectory` | `Fs::cleanDirectory(string $directory): bool` | Empty dir |
| `isEmptyDirectory` | `Fs::isEmptyDirectory(string $directory, bool $ignoreDotFiles = false): bool` | Is empty |
| `move` | `Fs::move(string $source, string $destination): bool` | Move file |
| `moveDir` | `Fs::moveDir(string $from, string $to, bool $overwrite = false): bool` | Move dir |
| `copyDirectory` | `Fs::copyDirectory(string $directory, string $destination): bool` | Copy dir |
| `files` | `Fs::files(string $directory, $extensions = null, bool $sorted = true): array` | List files |
| `allFiles` | `Fs::allFiles(string $directory, $extensions, bool $recursive = true, int $maxDepth = 0): array` | List recursive |
| `directories` | `Fs::directories(string $directory, bool $excludeHidden = true, bool $sorted = true): array` | List subdirs |

---

## Quick Examples

### Financial (use Num, NOT native operators)

```php
use Royfee\Support\Num;

$balance = Num::add('100.50', '200.30', 2);       // '300.80'
$remaining = Num::sub($balance, '50.00', 2);       // '250.80'
$discount = Num::mul('299.00', '0.85', 2);         // '254.15'
$perPerson = Num::div('1000.00', '3', 2);          // '333.33'
if (Num::gte($balance, $orderAmount, 2)) { /* ... */ }
```

### String

```php
use Royfee\Support\Str;

Str::snake('fooBar');         // 'foo_bar'
Str::camel('foo_bar');        // 'fooBar'
Str::slug('Hello World');     // 'hello-world'
Str::limit('Long text...', 10); // 'Long te...'
```

### Array

```php
use Royfee\Support\Arr;

Arr::get($config, 'database.host', 'localhost');
Arr::set($data, 'user.name', 'John');
Arr::pluck($users, 'email', 'id');
Arr::groupBy($orders, 'status');
```

### Filesystem

```php
use Royfee\Support\Fs;

Fs::put('/path/file.txt', 'content');
$data = Fs::json('/path/config.json');
Fs::ensureDirectoryExists('/path/to/dir');
```
