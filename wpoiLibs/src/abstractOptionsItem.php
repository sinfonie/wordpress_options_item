<?php

/**
 * WordPress Options Item
 * @author  Maciej Rumiński
 * @version 1.0.0
 */

namespace wpoiLibs\src;

use wpoiLibs\src\helpers\helper;
use wpoiLibs\settings\settings;

abstract class abstractOptionsItem
{
  private static $itemScheme = [
    'id'           => ['type' => 'int'],
    'date_added'   => ['type' => 'string'],
    'date_updated' => ['type' => 'string'],
    'user_added'   => ['type' => 'string'],
    'user_updated' => ['type' => 'string'],
  ];

  abstract function optionName(): string;
  abstract function createScheme(): array;

  public function __construct($id)
  {

    if (!get_network_option(null, 'options_items_data')) {
      add_network_option(null, 'options_items_data', []);
    }

    $this->date = date("Y-m-d H:i:s");
    $this->addOption();
    $this->scheme = array_merge($this->createScheme(), self::$itemScheme);

    ($id === null) ? $this->setSpecializedVals()->setItemVals() : $this->getItem($id);
  }

  private function getItemsData($type = null)
  {
    return ($type == null) ? get_network_option(null, 'options_items_data') : get_network_option(null, 'options_items_data')[$type];
  }


  private function setItemVals(): self
  {
    $currentUser = get_current_user_id();
    $this->id = null;
    $this->date_added = $this->date;
    $this->date_updated = $this->date;
    $this->user_added = $currentUser;
    $this->user_updated = $currentUser;
    return $this;
  }

  public function setSpecializedVals(): self
  {
    foreach ($this->createScheme() as $name => $type) {
      $this->$name = null;
    }
    return $this;
  }

  private function setUpdateParam()
  {
    if (!isset($this->updateParams)) {
      $this->updateParams = new \stdClass();
    }
  }

  public function setParam(string $name, $value)
  {

    $param = $this->scheme[$name]['type'];
    if ($param !== null) {
      if (call_user_func('is_' . $param, $value)) {
        $this->setUpdateParam();
        $this->updateParams->$name = $value;
      } else if ($value === null && isset($this->scheme[$name]['null'])) {
        $this->setUpdateParam();
        $this->updateParams->$name = $value;
      } else {
        throw new \Exception('Check "$scheme" in ' . get_class($this) . '. Given parameter type "' . $name . '" is not ' . $this->scheme[$name]['type'] . ' . It is ' . gettype($value));
      }
    } else {
      throw new \Exception('Check "$scheme" in ' . get_class($this) . '. There is no parameter named "' . $name . '"');
    };
    return $this;
  }

  private function assignVals(int $id, $entry = null)
  {
    foreach ($this->scheme as $name => $value) {
      // ID \\
      if ($name === 'id') {
        $output[$name] = $id;
        // DATE_ADDED \\ // USER_ADDED \\
      } else if ($name === 'date_added' || $name === 'user_added') {
        # @create
        if ($entry === null) {
          $output[$name] = $this->$name;
          # @update
        } else {
          $output[$name] = $entry[$name];
        }
      }
      // DATE_UPDATED \\
      else if ($name === 'date_updated') {
        $output[$name] = $this->date;
        $this->$name   = $this->date;
        // USER_UPDATED \\
      } else if ($name === 'user_updated') {
        $id = get_current_user_id();
        $output[$name] = $id;
        $this->$name   = $id;
        // OTHER \\
      } else {
        # @update
        if (property_exists($this->updateParams, $name)) {
          $output[$name] = $this->updateParams->$name;
          $this->$name   = $this->updateParams->$name;
          # @create
        } else if ($entry === null) {
          $output[$name] = $this->$name;
          # @status quo
        } else {
          $output[$name] = $entry[$name];
        }
      }
    }
    return $output;
  }

  public function updateEntry()
  {
    $entries = $this->getFullOption();
    foreach ($entries as $key => $entry) {
      if ($entry['id'] === $this->id) {
        $entries[$key] = $this->assignVals($this->id, $entry);
        break;
      }
      continue;
    }
    $this->updateOption($entries);
  }

  private function getMyReferences()
  {

    $myReferences = [];
    foreach ($this->getItemsData('references') as $referrerName => $refererVal) {
      foreach ($refererVal as $referingTo => $reference) {
        if ($referingTo === $this->optionName()) {
          foreach ($reference as $refering_field => $value) {
            $myReferences[] = [
              'referrer'       => $referrerName,
              'refering_field' => $refering_field,
              'refered_field'  => $value['refered_field'],
              'on_delete'      => $value['on_delete'],
            ];
          }
        }
      }
    }
    return $myReferences;
  }

  public function deleteEntry(int $id = null)
  {
    $this->id = ($id === null) ? $this->id : $id;
    $entries = $this->getFullOption();
    $this->deleteReferenceVals();

    foreach ($entries as $key => $entry) {
      if ($key === 'counter') continue;
      if ($entry['id'] === $this->id) {
        unset($entries[$key]);
        break;
      }
      continue;
    }
    $this->updateOption($entries);
  }

  private function deleteReferenceVals()
  {
    $myReferences = $this->getMyReferences();
    if (!empty($myReferences)) {
      foreach ($myReferences as $reference) {
        if ($reference['on_delete']) {
          $this->getInstancesToDelete($reference);
        }
      }
    }
  }

  public function getInstancesToDelete($reference)
  {
    $objectClass = settings::getItemClassPath() . helper::camelize($reference['referrer']);
    $instance = new $objectClass;
    $entries = $instance->getOption();
    $refered_field = $reference['refered_field'];
    foreach ($entries as $entry) {
      if ($entry[$reference['refering_field']] === $this->$refered_field) {
        $instaToDelete = $instance->getItem($entry['id']);
        $instaToDelete->deleteEntry();
      }
    }
  }

  public function getItem(int $id)
  {
    $found = false;
    foreach ($this->getOption() as $entry) {
      if ($entry['id'] === $id) {
        $found = true;
        foreach ($this->scheme as $name => $type) {
          $this->$name = $entry[$name];
        }
        break;
      }
    }
    if ($found) {
      return $this;
    } else {

      throw new \Exception('Given ID: ' . $id . ' does not exist');
    }
  }

  private function isAlreadyAdded($oldEntries, $newEntry)
  {
    unset($newEntry['id']);
    unset($newEntry['date_added']);
    unset($newEntry['date_updated']);
    unset($newEntry['user_added']);
    unset($newEntry['user_updated']);
    foreach ($oldEntries as $oldEntry) {
      unset($oldEntry['id']);
      unset($oldEntry['date_added']);
      unset($oldEntry['date_updated']);
      unset($oldEntry['user_added']);
      unset($oldEntry['user_updated']);
      $cmp = (strcmp(json_encode($newEntry), json_encode($oldEntry)));
      if ($cmp == 0) {
        return true;
      }
    }
    return false;
  }

  private function createReferences()
  {
    $option = $this->getItemsData();
    if (empty($option['references'][$this->optionName()])) {
      foreach ($this->scheme as $name => $scheme) {
        if (isset($scheme['reference'])) {
          $references[$scheme['reference']['option']][$name] =
            [
              'refered_field' => $scheme['reference']['refered_field'],
              'on_delete' => $scheme['reference']['on_delete'],
            ];
        }
      }
      if (!empty($references)) {
        $option['references'][$this->optionName()] = $references;
        update_network_option(null, 'options_items_data', $option);
      }
    }
  }

  public function createEntry($addDouble = true)
  {
    $this->createReferences();
    $entries = $this->getFullOption();
    if (!is_array($entries)) {
      $entries = [];
    }

    $entries = $this->setId($entries);
    $newEntry = $this->assignVals($entries['counter']);
    $newEntries = array_merge($entries, [array_key_last(array_filter(array_keys($entries), 'is_int')) + 1 => $newEntry]);
    $counterless = $entries;
    unset($counterless['counter']);
    if (empty($counterless)) {
      $this->updateOption($newEntries);
      $added = true;
    } else {
      if ($addDouble) {
        $this->updateOption($newEntries);
        $added = true;
      } else {
        $is_added = $this->isAlreadyAdded($counterless, $newEntry);
        if ($is_added === false) {
          $this->updateOption($newEntries);
          $added = true;
        } else {
          $added = false;
        }
      }
    }
    if ($added) {
      $this->id = $entries['counter'];
    }
    return $this;
  }

  public function setId($entries)
  {
    if (!isset($entries['counter'])) {
      $entries['counter'] = 0;
    } else {
      $entries['counter'] += 1;
    }
    return $entries;
  }

  private function addOption()
  {
    if (!get_network_option(null, $this->optionName())) {
      add_network_option(null, $this->optionName(), []);
    }
  }

  public function getOption()
  {
    $option = get_network_option(null, $this->optionName());
    unset($option['counter']);
    return $option;
  }

  public function getFullOption()
  {
    return get_network_option(null, $this->optionName());
  }

  public function updateOption($entry)
  {
    update_network_option(null, $this->optionName(), $entry);
  }

  private function hasArraysVals()
  {
    foreach ($this->scheme as $key => $val) {
      if ($val['type'] === 'array') {
        $this->arrayVals[] = $key;
      }
    }
    return (isset($this->arrayVals) && !empty($this->arrayVals)) ? true : false;
  }

  public function deleteArrayItem($itemField, $itemValue, $id = null)
  {
    if ($id !== null) {
      $this->getItem($id);
    }
    if ($this->hasArraysVals()) {
      if (in_array($itemField, $this->arrayVals)) {
        if (in_array($itemValue, $this->$itemField)) {
          $key = array_search($itemValue, $this->$itemField);
          unset($this->$itemField[$key]);
          $this->setParam($itemField, $this->$itemField);
          $this->updateEntry();
        }
      }
    }
    return false;
  }

  public function getValuesToArray()
  {
    foreach ($this->scheme as $key => $value) {
      $data[$key] = $this->$key;
    }
    return $data;
  }
}
