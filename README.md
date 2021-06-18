<p align="center">
<img width="280" alt="lampager" src="https://user-images.githubusercontent.com/1351893/31754281-a36010cc-b4d1-11e7-8371-851f5faa3785.png">
</p>
<p align="center">
<a href="https://travis-ci.com/lampager/lampager"><img src="https://travis-ci.com/lampager/lampager.svg?branch=master" alt="Build Status"></a>
<a href="https://coveralls.io/github/lampager/lampager?branch=master"><img src="https://coveralls.io/repos/github/lampager/lampager/badge.svg?branch=master" alt="Coverage Status"></a>
<a href="https://scrutinizer-ci.com/g/lampager/lampager/?branch=master"><img src="https://scrutinizer-ci.com/g/lampager/lampager/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality"></a>
</p>

# Lampager Core

The core package of Lampager

## Requirements

- PHP: ^5.6 || ^7.0

## Installing

```bash
composer require lampager/lampager
```

## Usage

Basically you don't need to directly use this package.
For example, if you use Laravel, install [lampager/lampager-laravel](https://github.com/lampager/lampager-laravel).

However, you can manually use like this:

```php
use Lampager\Paginator;
use Lampager\ArrayProcessor;

$cursor = [
    'id' => 3,
    'created_at' => '2017-01-10 00:00:00',
    'updated_at' => '2017-01-20 00:00:00',
];

$query = (new Paginator())
    ->forward()
    ->limit(5)
    ->orderByDesc('updated_at') // ORDER BY `updated_at` DESC, `created_at` DESC, `id` DESC
    ->orderByDesc('created_at')
    ->orderByDesc('id')
    ->seekable()
    ->configure($cursor);

$rows = run_your_query_using_PDO($query); // Note: SQLite3 driver example is bundled in the tests/StubPaginator.php. Please refer to that.

$result = (new ArrayProcessor())->process($query, $rows);
```

It will run the optimized query.


```sql
(

    SELECT * FROM `posts`
    WHERE `user_id` = 1
    AND (
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` = '2017-01-10 00:00:00' AND `id` > 3
        OR
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` > '2017-01-10 00:00:00'
        OR
        `updated_at` > '2017-01-20 00:00:00'
    )
    ORDER BY `updated_at` ASC, `created_at` ASC, `id` ASC
    LIMIT 1

) UNION ALL (

    SELECT * FROM `posts`
    WHERE `user_id` = 1
    AND (
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` = '2017-01-10 00:00:00' AND `id` <= 3
        OR
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` < '2017-01-10 00:00:00'
        OR
        `updated_at` < '2017-01-20 00:00:00'
    )
    ORDER BY `updated_at` DESC, `created_at` DESC, `id` DESC
    LIMIT 6

)
```

And you'll get


```php
object(Lampager\PaginationResult)#1 (5) {
  ["records"]=>
  array(5) {
    [0]=>
    array(5) {
      ["id"]=>
      int(3)
      ["user_id"]=>
      int(1)
      ["text"]=>
      string(3) "foo"
      ["created_at"]=>
      string(19) "2017-01-10 00:00:00"
      ["updated_at"]=>
      string(19) "2017-01-20 00:00:00"
    }
    [1]=>
    array(5) {
      ["id"]=>
      int(5)
      ["user_id"]=>
      int(1)
      ["text"]=>
      string(3) "bar"
      ["created_at"]=>
      string(19) "2017-01-05 00:00:00"
      ["updated_at"]=>
      string(19) "2017-01-20 00:00:00"
    }
    [2]=>
    array(5) {
      ["id"]=>
      int(4)
      ["user_id"]=>
      int(1)
      ["text"]=>
      string(3) "baz"
      ["created_at"]=>
      string(19) "2017-01-05 00:00:00"
      ["updated_at"]=>
      string(19) "2017-01-20 00:00:00"
    }
    [3]=>
    array(5) {
      ["id"]=>
      int(2)
      ["user_id"]=>
      int(1)
      ["text"]=>
      string(3) "qux"
      ["created_at"]=>
      string(19) "2017-01-17 00:00:00"
      ["updated_at"]=>
      string(19) "2017-01-18 00:00:00"
    }
    [4]=>
    array(5) {
      ["id"]=>
      int(1)
      ["user_id"]=>
      int(1)
      ["text"]=>
      string(3) "quux"
      ["created_at"]=>
      string(19) "2017-01-16 00:00:00"
      ["updated_at"]=>
      string(19) "2017-01-18 00:00:00"
    }
  }
  ["hasPrevious"]=>
  bool(false)
  ["previousCursor"]=>
  NULL
  ["hasNext"]=>
  bool(true)
  ["nextCursor"]=>
  array(2) {
    ["updated_at"]=>
    string(19) "2017-01-18 00:00:00"
    ["created_at"]=>
    string(19) "2017-01-14 00:00:00"
    ["id"]=>
    int(6)
  }
}
```

## Question: How about [Tuple comparison](https://www.sql-workbench.eu/comparison/tuple_comparison.html)?

With this feature, SQL statements should be simpler. However, according to [SQL Feature Comparison](https://www.sql-workbench.eu/dbms_comparison.html), some RDBMS, such as SQLServer, do not support this syntax. Therefore, Lampager continuously uses redundant statements.

## Classes

| Name | Type | Parent Class<br>Implemented Interface | Description |
|:---|:---|:---|:---|
| Lampager\\**`Paginator`** | Class | | Fluent factory for building Query |
| Lampager\\**`AbstractProcessor`** | Abstract Class | | Receive fetched records and format them |
| Lampager\\**`PaginationResult`** | Class | | Processor wraps result with this by default |
| Lampager\\`ArrayProcessor` | Class | Lampager\\`AbstractProcessor` | Simple Processor implementation for pure PDO |
| Lampager\\`ArrayCursor` | Class | Lampager\\Contracts\\`Cursor` | Simple Cursor implementation for pure PDO<br>Arrays are automatically wrapped with this |
| Lampager\\`Query` | Class | | SQL configuration container generated by Paginator |
| Lampager\\Query\\... | Class | | Child components of Query |
| Lampager\\Contracts\\`Cursor` | Interface | | Indicates parameters for retrieving previous/next records |
| Lampager\\Contracts\\`Formatter` | Interface | | Formatter interface pluggable to Processor |
| Lampager\\Concerns\\`HasProcessor` | Trait | | Helper for extended Paginator providing convenient accessibility to Processor |

## API

### Paginator::orderBy()<br>Paginator::orderByDesc()<br>Paginator::clearOrderBy()

Add or clear cursor parameter name for `ORDER BY` statement.  
At least one parameter required.

```php
Paginator::orderBy(string $column, string $direction = 'asc'): $this
Paginator::orderByDesc(string $column): $this
Paginator::clearOrderBy(): $this
```

**IMPORTANT**: The last key MUST be the primary key.

e.g. `$paginator->orderBy('updated_at')->orderBy('id')`

#### Arguments

- **`(string)`** __*$column*__<br> Table column name.
- **`(string)`** __*$direction*__<br> `"asc"` or `"desc"`.

### Paginator::limit()

Define the pagination limit.

```php
Paginator::limit(int $limit): $this
```

#### Arguments

- **`(int)`** __*$limit*__<br> Positive integer.

### Paginator::forward()<br>Paginator::backward()

Define the pagination direction.

```php
Paginator::forward(bool $forward = true): $this
Paginator::backward(bool $backward = true): $this
```

#### Forward (Default)

```
    ===============>
[2] [ 3, 4, 5, 6, 7] [8]
 |    |               └ next cursor
 |    └ current cursor
 └ previous cursor
```

```
    ===============>
[8] [ 7, 6, 5, 4, 3] [2]
 |    |               └ next cursor
 |    └ current cursor
 └ previous cursor
```

#### Backward

```
    <===============
[2] [ 3, 4, 5, 6, 7] [8]
 |                |   └ next cursor
 |                └ current cursor
 └ previous cursor
```

```
    <===============
[8] [ 7, 6, 5, 4, 3] [2]
 |                |   └ next cursor
 |                └ current cursor
 └ previous cursor
```

**IMPORTANT**: You need **previous** cursor to retrieve more results.

### Paginator::inclusive()<br>Paginator::exclusive()

```php
Paginator::inclusive(bool $inclusive = true): $this
Paginator::exclusive(bool $exclusive = true): $this
```

Change the behavior of handling cursor.

#### Inclusive (Default)

Current cursor will be included in the current page.

```
    ===============>
[2] [ 3, 4, 5, 6, 7] [8]
 |    |               └ next cursor
 |    └ current cursor
 └ previous cursor
```

```
    <===============
[2] [ 3, 4, 5, 6, 7] [8]
 |                |   └ next cursor
 |                └ current cursor
 └ previous cursor
```

#### Exclusive

Current cursor will not be included in the current page.

```
    ===============>
[2] [ 3, 4, 5, 6, 7] [8]
 |                └ next cursor
 └ current cursor
```

```
    <===============
[2] [ 3, 4, 5, 6, 7] [8]
      |               |
      |               └ current cursor
      └ previous cursor
```

### Paginator::unseekable()<br>Paginator::seekable()

```php
Paginator::unseekable(bool $unseekable = true): $this
Paginator::seekable(bool $seekable = true): $this
```

Define that the pagination result should contain both of the next cursor and the previous cursor.

- `unseekable()` always requires one simple `SELECT` query. (Default)
- `seekable()` may require `SELECT ... UNION ALL SELECT ...` query when the cursor parameters are not empty.

#### Unseekable (Default)

```
    ===============>
[?] [ 3, 4, 5, 6, 7] [8]
      |               └ next cursor
      └ current cursor

```

#### Seekable

```
    ===============>
[2] [ 3, 4, 5, 6, 7] [8]
 |    |               └ next cursor
 |    └ current cursor
 └ previous cursor
```

#### Always when the current cursor parameters are empty

```
===============>
[ 1, 2, 3, 4, 5] [6]
                  └ next cursor
```

### Paginator::fromArray()

Define options from an associative array.

```php
Paginator::fromArray(array $options): $this
```

#### Arguments

- **`(array)`** __*$options*__<br> Associative array that contains the following keys.
  - **`(int)`** __*limit*__
  - **`(bool)`** __*backward*__ / __*forward*__
  - **`(bool)`** __*exclusive*__ / __*inclusive*__
  - **`(bool)`** __*seekable*__ / __*unseekable*__
  - **`(string[][])`** __*$orders*__

e.g.

```php
[
    'limit' => 30,
    'backward' => true,
    'unseekable' => false,
    'orders' => [
        ['created_at', 'asc'],
        ['id', 'asc'],
    ],
]
```

### Paginator::configure()

Generate Query corresponding to the current cursor.

```php
Paginator::configure(Cursor|array $cursor = []): Query
```

#### Arguments

- **`(mixed)`** __*$cursor*__<br> An associative array that contains `$column => $value` or an object that implements `\Lampager\Contracts\Cursor`. It must be **all-or-nothing**.
  - For the initial page, omit this parameter or pass an empty array.
  - For subsequent pages, pass all parameters. Partial parameters are not allowed.

### AbstractProcessor::process()

Receive a pair of Query and fetched rows to analyze and format them.

```php
AbstractProcessor::process(Query $query, mixed $rows): mixed
```

#### Arguments

- **`(Query)`** __*$query*__
- **`(mixed)`** __*$rows*__<br> Fetched records from database. Typically it should be an array or a Traversable.

#### Return Value

**`(mixed)`**

By default, an instance of `\Lampager\PaginationResult` is returned. All fields are public.

e.g.

```php
object(Lampager\PaginationResult)#1 (5) {
  ["records"]=>
  array(5) {
    /* ... */
  }
  ["hasPrevious"]=>
  bool(false)
  ["previousCursor"]=>
  NULL
  ["hasNext"]=>
  bool(true)
  ["nextCursor"]=>
  array(2) {
    ["updated_at"]=>
    string(19) "2017-01-18 00:00:00"
    ["created_at"]=>
    string(19) "2017-01-14 00:00:00"
    ["id"]=>
    int(6)
  }
}
```

Note that

- `hasPrevious`/`hasNext` will be **`false`** when there are no more results for the corresponding direction.
- Either `hasPrevious`/`hasNext` will be **`null`** when `$cursor` is empty or `seekable()` is not be enabled.

### PaginationResult::getIterator()

It can be directly traversed using `foreach` thanks to the interface `\IteratorAggregate`.

```php
AbstractProcessor::getIterator(): \ArrayIterator
```

#### Return Value

**`(mixed)`**

`ArrayIterator` instance that wraps `records`.

### AbstractProcessor::useFormatter()<br>AbstractProcessor::restoreFormatter()

Override or restore the formatter for the pagination result.

```php
AbstractProcessor::useFormatter(Formatter|callable $formatter): $this
AbstractProcessor::restoreFormatter(): $this
```

#### Callable Formatter Example

```php
<?php

use Lampager\Query;
use Lampager\ArrayProcessor;
use Lampager\PaginationResult;

$formatter = function ($rows, array $meta, Query $query) {
    // Drop table prefix in meta properties (e.g. "posts.updated_at" -> "updated_at")
    foreach (array_filter($meta, 'is_array') as $property => $cursor) {
        foreach ($cursor as $column => $field) {
            unset($meta[$property][$column]);
            $segments = explode('.', $column);
            $meta[$property][end($segments)] = $field;
        }
    }
    return new PaginationResult($rows, $meta);
};

$result = (new ArrayProcessor())->useFormatter($formatter)->process($query, $rows);
```

#### Class Formatter Example

```php
<?php

use Lampager\Query;
use Lampager\ArrayProcessor;
use Lampager\PaginationResult;
use Lampager\Contracts\Formatter;

class DropTablePrefix implements Formatter
{
    public function format($rows, array $meta, Query $query)
    {
        // Drop table prefix in meta properties (e.g. "posts.updated_at" -> "updated_at")
        foreach (array_filter($meta, 'is_array') as $property => $cursor) {
            foreach ($cursor as $column => $field) {
                unset($meta[$property][$column]);
                $segments = explode('.', $column);
                $meta[$property][end($segments)] = $field;
            }
        }
        return new PaginationResult($rows, $meta);
    }
}

$result = (new ArrayProcessor())->useFormatter(DropTablePrefix::class)->process($query, $rows);
```

### AbstractProcessor::setDefaultFormatter()<br>AbstractProcessor::restoreDefaultFormatter()

Globally override or restore the formatter.

```php
static AbstractProcessor::setDefaultFormatter(Formatter|callable $formatter): void
static AbstractProcessor::restoreDefaultFormatter(): void
```

#### Example (Laravel)

```php
<?php

use Illuminate\Database\Eloquent\Builder;
use Lampager\Query;
use Lampager\Laravel\Processor as IlluminateProcessor;

IlluminateProcessor::setDefaultFormatter(function ($rows, array $meta, Query $query) {

   // Note:
   //    $builder is provided from extended Paginator.
   //    For example, lampager/lampager-laravel provides QueryBuilder, EloquentBuilder or Relation.
   $builder = $query->builder();

   switch ($builder instanceof Builder ? $builder->getModel() : null) {

       case Post::class:
           return (new PostFormatter())->format($rows, $meta, $query);

       case Comment::class:
           return (new CommentFormatter())->format($rows, $meta, $query);

       default:
           return new PaginationResult($rows, $meta);
   }
});

$posts = Post::lampager()->orderBy('created_at')->orderBy('id')->paginate();
$comments = Comment::lampager()->orderBy('created_at')->orderBy('id')->paginate();
```
