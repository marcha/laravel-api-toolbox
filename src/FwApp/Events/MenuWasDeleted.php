<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/28/18
 * Time: 19:36
 */

namespace Erpmonster\FwApp\Events;


use Erpmonster\Events\Event;
use Erpmonster\FwApp\Models\Menu;

class MenuWasDeleted extends Event
{
    /**
     * @var Menu
     */
    private $menu;

    public function __construct(Menu $menu)
    {

        $this->menu = $menu;
    }
}
