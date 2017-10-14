# Lampager Core  [![Build Status](https://travis-ci.org/lampager/core.svg?branch=master)](https://travis-ci.org/lampager/core) [![Coverage Status](https://coveralls.io/repos/github/lampager/core/badge.svg?branch=master)](https://coveralls.io/github/lampager/core?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lampager/core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lampager/core/?branch=master)

The core package of Lampager

## Requirements

- PHP: ^5.6 || ^7.0

## Installing

```bash
composer require lampager/core:^0.0.1
```

## Core API

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
Paginator::forward(): $this
Paginator::backward(): $this
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
Paginator::inclusive(): $this
Paginator::exclusive(): $this
```

Change the behavior of handling cursor.

#### Inclusive (Default)

Current cursor is included in the current page.

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

Current cursor is not included in the current page.

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
Paginator::unseekable(): $this
Paginator::seekable(): $this
```

Define that the pagination result should contain both of the next cursor and the previous cursor.  

- `unseekable()` always requires simple one `SELECT` query. (Default)
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

#### When always the current cursor parameters are empty

```
===============>
[ 1, 2, 3, 4, 5] [6]
                  └ next cursor
```
