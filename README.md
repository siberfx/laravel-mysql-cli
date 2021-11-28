# Tablavel - A simple Database Client for Laravel

## Table of Contents

- [About](#about)
- [Getting Started](#getting_started)
- [Usage](#usage)
- [Contributing](../CONTRIBUTING.md)

## About <a name = "about"></a>

This solves a simple problem. Know that feeling when you are in the terminal and you want to check something in your database? Those classic actions, to see if something was updated or inserted. You have your options, like your day to day DB Client, but sometimes, you just want to check something fast. So just run php artisan tablavel and the app will question you about what you want to do.

You have classic actions such as:
- show all tables
- show columns from a table
- show last records
- show first records
- show by id

The current version of this package only serves as a read only. I did not find it useful to create other operations. If you feel it is something useful for your, drop me a line on issues section and we will discuss.


### Prerequisites

In order to use this package properly, you must setup a mysql connection. If your project is not new, it will take the default.

### Installing

Install from composer

```
composer require thecoderepublic/tablavel
```

## Usage <a name = "usage"></a>

```
php artisan tablavel
```
