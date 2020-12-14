<?php

namespace wpoiLibs\items;

use wpoiLibs\src\abstractOptionsItem;

class exampleGroups extends abstractOptionsItem
{

  /**
  * Constructor must call the parent's constructor
  * @param int|null $id an entry, if set parent constructor calls getItem($id) method
  */
  public function __construct(int $id = null)
  {
    parent::__construct($id);
  }

  /**
  * Option will be saved in the database under the given name
  */
  public function optionName(): string
  {
    return 'exampleGroups';
  }

  /**
  * Option scheme define which fields the table will consist of, what type of data it stores, possible references, cascade deletion or allow null saving (default: false)
  */
  public function createScheme(): array
  {
    return [
      'name' => ['type' => 'string', 'null' => true],
      'type' => ['type' => 'string'],
      'tags' => ['type' => 'array']
    ];
  }
}
