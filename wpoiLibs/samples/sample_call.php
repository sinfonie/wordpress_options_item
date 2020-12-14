<?php

/**
 * You can use this mechanism whenever you want in WordPress.
 *
 * Put library in `wp-content/libraries` catalog in Your WordPress.
 *
 * Then use statement
 */

require_once WP_CONTENT_DIR . '/libraries/wp_options_item/wpoiLibs/loader.php';

/* Now You can use Your items!!! :) */
/* Create Your first entry. Lets create Stephen King fan group!*/

$groups = new \wpoiLibs\items\exampleGroups;

$groups->setParam('name', 'Stephen King Fans');
$groups->setParam('type', 'literature');
$groups->setParam('tags', ['horror', 'thriller', 'fantasy']);

/* Save Group into database (note: if you pass false argument an entry must be unique) */

$groups->createEntry(false);

$group_id = $groups->getOption()[0]['id']; //for example use, it will return first existing group ID

/* Lets add some group members (note: item counter starts from 0 so You can assign 0 to group_id param) */
$members = new \wpoiLibs\items\exampleMembers;

/* setParam() add or update param */
$user_1 = $members;
$user_1->setParam('name', 'John Smith');
$user_1->setParam('group_id', $group_id);
$user_1->setParam('books', ['Revival, Stephen King', 'Outsider, Stephen King']);
$user_1->setParam('active', true);
$user_1->createEntry();

$user_2 = $members;
$user_2->setParam('name', 'Mary Jane');
$user_2->setParam('group_id', $group_id);
$user_2->setParam('books', ['Shining, Stephen King']);
$user_2->setParam('active', true);
$user_2->createEntry();

/* Now You have two members in one group "Stephen King Fans" group :)
 * Check Your Items via getOption() method
 */

echo '<pre>"Stephen King Fan Group"<br />';
print_r($groups->getOption());
echo '<br />';
echo '<br /> "Group members"<br />';
print_r($members->getOption());

/* If You want to delete member use deleteEntry() method */

$idUsr = $members->getOption()[0]['id']; //for example use, it will return first existing user ID

$user_to_delete = $members->getItem($idUsr);
$user_to_delete->deleteEntry();

echo '<br />"User with ID: ' . $idUsr . ' was deleted"<br />';
print_r($members->getOption());
echo '<br />';

/* Cascade deletion */

$group_to_delete = $groups->getItem($group_id);
$group_to_delete->deleteEntry();

echo '<br />Cascade deletion<br />';
print_r($members->getOption());
echo '<br />';

echo '</pre>';
