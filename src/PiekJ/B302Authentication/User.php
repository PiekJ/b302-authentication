<?php namespace PiekJ\B302Authentication;

use Eloquent;
use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

/*
 * The User class to use as default
 */
class User extends Eloquent implements ConfideUserInterface
{
    use ConfideUser, HasRole;
}