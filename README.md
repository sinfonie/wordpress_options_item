# WordPress options Item

Library that allows you to save structured data based on the WordPress options saving mechanism.

- Saves data in the form of an array using the WordPress `network_options` mechanism and creating an `ItemOption`.
- Each ItemOption is a specific table with records, imitating a database table.
- Each ItemOption entry has its own records:
  - numeric key `id`,
  - date added ` date_added`,
  - update date `date_updated`,
  - user id who added` user_added`,
  - user id who modified `user_updated`.
- You can cascade delete entries from different ItemOptions. In this case, you must specify a reference to the appropriate record from another ItemOption.
- You can freely specify whether the ItemOptions record writes the `null` value.

## Requirements

  * PHP > 5.6.0
  * Wordpress > 5.3.6

## Installation

Install via composer:
```php
composer require sinfonie/wordpress_options_item
```

Install via git over https:
```php
git clone https://github.com/sinfonie/wordpress_options_item.git
```
manual download:
https://github.com/sinfonie/wordpress_options_item/archive/master.zip

## Configuration

Please remember to specify the appropriate path for the library in your project.

You can find simple implementaion in examples below

[exampleGroups](wpoiLibs/items/exampleGroups.php)
[exampleMember](wpoiLibs/items/exampleMembers.php)
[ClientCode](wpoiLibs/samples/sample_call.php)
