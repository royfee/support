# Royfee Support

A collection of commonly used PHP utility classes including string, array, filesystem, and number helpers.

## Installation

```bash
composer require royfee/support
```

## Requirements

- PHP >= 7.4
- ext-bcmath

## Usage

### Str - String Utilities

```php
use Royfee\Support\Str;

// Basic operations
Str::substr('hello world', 6);          // 'world'
Str::length('hello');                    // 5
Str::contains('hello world', 'world');   // true
Str::contains('hello world', ['foo', 'world']); // true
Str::charAt('hello', 0);                // 'h'

// Case conversion
Str::upper('hello');                     // 'HELLO'
Str::lower('HELLO');                     // 'hello'
Str::title('hello world');              // 'Hello World'

// Case style conversion
Str::camel('hello_world');              // 'helloWorld'
Str::studly('hello_world');             // 'HelloWorld'
Str::snake('HelloWorld');               // 'hello_world'
Str::kebab('HelloWorld');               // 'hello-world'
Str::slug('Hello World');               // 'hello-world'

// String detection
Str::startsWith('hello world', 'hello'); // true
Str::endsWith('hello world', 'world');   // true
Str::containsAll('hello world', ['hello', 'world']); // true
Str::doesntContain('hello world', 'foo'); // true
Str::is('hello*', 'hello world');       // true (wildcard match)
Str::isJson('{"name":"John"}');         // true
Str::isUrl('https://example.com');      // true
Str::isEmpty('');                        // true
Str::isNotEmpty('hello');               // true

// String manipulation
Str::replaceFirst('world', 'earth', 'hello world world'); // 'hello earth world'
Str::replaceLast('world', 'earth', 'hello world world');  // 'hello world earth'
Str::replace('hello', 'hi', 'hello world'); // 'hi world'
Str::remove('ll', 'hello world');        // 'heo world'
Str::limit('hello world', 5);           // 'hello...'
Str::words('Hello World Foo Bar', 2);   // 'Hello World...'
Str::finish('hello', '/');              // 'hello/'
Str::random(32);                         // random 32-char string
Str::squish('  hello   world  ');       // 'hello world'
Str::reverse('hello');                   // 'olleh'
Str::mask('13812341234', '*', 3, 4);    // '138****1234'

// String extraction
Str::before('hello world', ' ');        // 'hello'
Str::after('hello world', ' ');         // 'world'
Str::beforeLast('a b c', ' ');          // 'a b'
Str::afterLast('a b c', ' ');           // 'c'
Str::between('[hello]', '[', ']');      // 'hello'
Str::match('/hello (\w+)/', 'hello world'); // 'world'

// Padding & wrapping
Str::padLeft('hi', 4, '0');            // '00hi'
Str::padRight('hi', 4, '0');           // 'hi00'
Str::padBoth('hi', 4, '0');            // '0hi0'
Str::wrap('hello', '<p>', '</p>');      // '<p>hello</p>'
Str::unwrap('<p>hello</p>', '<p>', '</p>'); // 'hello'

// Case conversion
Str::ucfirst('hello');                  // 'Hello'
Str::lcfirst('HELLO');                  // 'hELLO'

// Base64
Str::toBase64('hello');                 // 'aGVsbG8='
Str::fromBase64('aGVsbG8=');           // 'hello'

// Counting
Str::substrCount('hello hello hello', 'hello'); // 3

// Trim & clean
Str::trim('  hello  ');                 // 'hello'
Str::ltrim('  hello');                  // 'hello'
Str::rtrim('hello  ');                  // 'hello'
Str::clean('  hello  ');               // 'hello'

// Encoding
Str::encoding('hello', 'utf-8', 'gb2312');
Str::ascii('ü');                              // 'u'
Str::ascii('ñ');                              // 'n'
Str::ascii('Æ');                              // 'AE'

// Headline
Str::headline('fooBar');                      // 'Foo Bar'
Str::headline('laravel_php_framework');       // 'Laravel Php Framework'
Str::headline('foo-bar');                     // 'Foo Bar'
```

### Arr - Array Utilities

```php
use Royfee\Support\Arr;

// Dot notation access
Arr::get($array, 'user.address.city');          // get nested value
Arr::get($array, 'user.name', 'default');       // with default value
Arr::has($array, 'user.address.city');          // check existence (dot notation)
Arr::hasAny($array, ['name', 'email']);         // check if any key exists
Arr::exists($array, 'name');                    // check key exists (no dot notation)
Arr::set($array, 'user.name', 'John');          // set nested value
Arr::forget($array, 'user.address.city');       // remove nested value
Arr::pull($array, 'name');                      // get and remove
Arr::add($array, 'email', 'john@example.com');  // add if key doesn't exist

// Array filtering
Arr::only($array, ['name', 'email']);           // get subset
Arr::except($array, ['password']);              // exclude keys
Arr::where($array, fn($v) => $v > 0);          // filter by callback
Arr::reject($array, fn($v) => $v > 0);         // reverse of where
Arr::whereNotNull($array);                      // filter null values

// Array elements
Arr::first($array);                             // first element
Arr::first($array, fn($v) => $v > 3);          // first matching
Arr::last($array);                              // last element
Arr::last($array, fn($v) => $v < 3);           // last matching
Arr::random($array);                            // random element
Arr::random($array, 3);                         // 3 random elements
Arr::take($array, 3);                           // first 3 items
Arr::take($array, -2);                          // last 2 items

// Array transformation
Arr::flatten([[1, 2], [3, 4]]);                // [1, 2, 3, 4]
Arr::collapse([[1, 2], [3, 4]]);               // [1, 2, 3, 4]
Arr::pluck($array, 'name');                     // extract column
Arr::pluck($array, 'age', 'name');             // key-value pairs
Arr::groupBy($array, 'type');                   // group by key
Arr::keyBy($array, 'name');                    // key by column
Arr::keyBy($array, fn($item) => $item['id']); // key by callback
Arr::dot(['user' => ['name' => 'John']]);      // ['user.name' => 'John']
Arr::undot(['user.name' => 'John']);           // expand dot notation
Arr::map($array, fn($v) => $v * 2);           // transform each value
Arr::mapWithKeys($array, fn($v, $k) => [$k => $v]); // map with keys
Arr::divide($array);                            // [keys, values]
Arr::join($array, ', ', ' and ');              // 'a, b and c'
Arr::crossJoin([1, 2], ['a', 'b']);           // all permutations

// Array manipulation
Arr::prepend($array, 'value');                  // prepend to array
Arr::prepend($array, 'value', 'key');          // prepend with key
Arr::wrap('value');                             // ['value']
Arr::wrap(['a', 'b']);                         // ['a', 'b']
Arr::wrap(null);                                // []

// Array inspection
Arr::isAssoc(['a' => 1, 'b' => 2]);            // true
Arr::isList([1, 2, 3]);                         // true
Arr::accessible([]);                            // true
Arr::accessible('string');                      // false

// Sorting
Arr::sort([3, 1, 2]);                           // sorted ascending
Arr::sort([3, 1, 2], SORT_REGULAR, true);      // sorted descending
Arr::sortRecursive($array);                     // recursive sort
Arr::shuffle($array);                           // randomize order

// Query string
Arr::query(['name' => 'John', 'age' => 30]);   // 'name=John&age=30'
```

### Fs - Filesystem Utilities

```php
use Royfee\Support\Fs;

// File reading & writing
Fs::exists('/path/to/file');                    // bool
Fs::missing('/path/to/file');                   // !exists
Fs::isFile('/path/to/file');                    // bool
Fs::get('/path/to/file');                       // get contents
Fs::get('/path/to/file', true);                // with shared lock
Fs::sharedGet('/path/to/file');                // shared lock read
Fs::put('/path/to/file', 'content');           // write file
Fs::put('/path/to/file', 'content', true);     // with exclusive lock
Fs::prepend('/path/to/file', 'header');        // prepend to file
Fs::append('/path/to/file', 'data');           // append to file
Fs::delete('/path/to/file');                    // delete file(s)
Fs::delete(['/path/a', '/path/b']);            // delete multiple
Fs::replace('/path/to/file', 'new content');   // atomic replace
Fs::replaceInFile('old', 'new', '/path/file'); // string replace in file
Fs::json('/path/to/file.json');                // read & decode JSON

// File info
Fs::name('/path/to/file.txt');                  // 'file'
Fs::basename('/path/to/file.txt');              // 'file.txt'
Fs::extension('/path/to/file.txt');             // 'txt'
Fs::dirname('/path/to/file.txt');               // '/path/to'
Fs::size('/path/to/file');                      // bytes (int)
Fs::lastModified('/path/to/file');              // timestamp (int)
Fs::hash('/path/to/file');                      // md5 hash
Fs::hash('/path/to/file', 'sha256');           // sha256 hash
Fs::mimeType('/path/to/file');                  // 'text/plain'
Fs::isReadable('/path/to/file');                // bool
Fs::isWritable('/path/to/file');                // bool
Fs::copy('/path/to/file', '/new/path');        // copy file

// Directory operations
Fs::isDir('/path/to/dir');                      // bool
Fs::makeDir('/path/to/dir');                    // create directory
Fs::makeDir('/path/to/dir', 0755, true);       // recursive create
Fs::ensureDirectoryExists('/path/to/dir');      // create if not exists
Fs::deleteDir('/path/to/dir');                  // delete directory
Fs::cleanDirectory('/path/to/dir');             // remove all contents
Fs::isEmptyDirectory('/path/to/dir');           // bool
Fs::copyDirectory('/src/dir', '/dest/dir');    // copy directory
Fs::files('/path/to/dir');                      // list files (no subdirs)
Fs::files('/path/to/dir', 'php');               // list PHP files only
Fs::directories('/path/to/dir');                // list subdirectories
Fs::allFiles('/path', 'php');                   // find PHP files
Fs::allFiles('/path', ['php', 'js']);          // find by extensions
Fs::allFiles('/path', 'php', false);           // non-recursive

// Move operations
Fs::move('/source/file', '/dest/file');         // move file (auto create dir)
Fs::moveDir('/source/dir', '/dest/dir');        // move directory
Fs::moveDir('/source/dir', '/dest/dir', true); // with overwrite
```

### Num - Number Utilities

```php
use Royfee\Support\Num;

// Range & constraint
Num::clamp(15, 0, 10);                       // 10
Num::between(5, 1, 10);                       // true

// Formatting
Num::format(1234567.89);                       // '1,234,567.89'
Num::currency(1234.5, '$');                    // '$1,234.50'
Num::percent(0.856, 1);                        // '85.6%'
Num::fileSize(1048576);                        // '1.00 MB'

// Statistical calculations
Num::average([1, 2, 3]);                       // '2.00' (string, bcmath)
Num::median(1, 2, 3, 4);                       // 2.5
Num::sum([1, 2, 3]);                           // '6.00' (string, bcmath)
Num::product([2, 3, 4]);                       // 24
Num::max([3, 1, 4]);                           // 4
Num::min([3, 1, 4]);                           // 1

// Rounding (always bcmath, returns string)
Num::round('3.1415', 2);                       // '3.15'
Num::floor('3.9');                              // '3'
Num::ceil('3.1');                               // '4'

// Type checking
Num::isEven(4);                                // true
Num::isOdd(3);                                 // true
Num::isPositive(1);                            // true
Num::isNegative(-1);                           // true
Num::isZero(0);                                // true
Num::isNumeric('3.14');                        // true
Num::isInt(3.0);                               // true
Num::isFloat(3.14);                            // true

// Math operations
Num::gcd(12, 8);                               // 4
Num::lcm(4, 6);                                // 12
Num::factorial(5);                             // 120
Num::fibonacci(7);                             // 13
Num::power(2, 10);                             // 1024
Num::sqrt(16);                                 // 4.0

// Random numbers
Num::random(1, 100);                           // random int 1~100
Num::randomFloat(0, 1, 2);                     // random float 0.00~1.00
```

### Num - Precise Calculations (bcmath)

> For financial / amount calculations where floating-point precision matters.
> Always pass values as **strings** to avoid precision loss.

```php
use Royfee\Support\Num;

// Precise arithmetic
Num::add('0.1', '0.2', 2);                    // '0.30' (not 0.30000000000000004)
Num::sub('1.00', '0.33', 2);                   // '0.67'
Num::mul('9.99', '100', 2);                    // '999.00'
Num::div('100', '3', 2);                       // '33.33'

// Precise comparison
Num::comp('0.1', '0.2', 2);                    // -1 (a < b)
Num::eq('0.1', '0.10', 2);                     // true
Num::gt('1.01', '1.00', 2);                    // true
Num::lt('0.99', '1.00', 2);                    // true
Num::gte('1.00', '1.00', 2);                   // true
Num::lte('0.99', '1.00', 2);                   // true

// Precise formatting
Num::toDecimal(0.1 + 0.2, 2);                  // '0.30'

// Precise aggregation (always use bcmath, returns string)
Num::sum([1, 2, 3], 2);                        // '6.00'
Num::average([10, 20, 30], 2);                 // '20.00'

// Financial calculation example
$price = '33.33';
$total = Num::mul($price, '3', 2);             // '99.99'
$discount = Num::mul($total, '0.10', 2);       // '10.00'
$final = Num::sub($total, $discount, 2);       // '89.99'
```

## Development

### Install dependencies

```bash
composer install
```

### Run tests

```bash
composer test
```

### Code style

```bash
composer cs-check   # check
composer cs-fix     # fix
```

### Static analysis

```bash
composer analyse
```

## License

MIT
