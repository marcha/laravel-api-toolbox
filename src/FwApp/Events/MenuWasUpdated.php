<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/28/18
 * Time: 19:35
 */

namespace Erpmonster\FwApp\Events;

use Erpmonster\Events\Event;
use Erpmonster\FwApp\Models\Menu;

class MenuWasUpdated extends Event
{
    /**
     * @var Menu
     */
    private $menu;

    /**
     * MenuWasCreated constructor.
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }
}
