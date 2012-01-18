<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Event
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Event
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Event_Subscriber_Plugin extends Enlight_Event_Subscriber_Config
{
    /**
     * @var Enlight_Plugin_Namespace
     */
    protected $namespace;

    /**
     * @param      $namespace
     * @param null $options
     */
    public function __construct($namespace, $options = null)
    {
        $this->namespace = $namespace;
        parent::__construct($options);
    }

    /**
     * @return  Enlight_Event_Subscriber_Config
     */
    public function write()
    {
        $this->storage->listeners = $this->toArray();
        $this->storage->write();
        return $this;
    }

    /**
     * Loads the event listener from storage.
     *
     * @return  Enlight_Event_Subscriber_Config
     */
    public function read()
    {
        $this->listeners = array();

        if ($this->storage->listeners !== null) {
            foreach ($this->storage->listeners as $entry) {
                if (!$entry instanceof Enlight_Config) {
                    continue;
                }
                $this->listeners[] = new Enlight_Event_Handler_Plugin(
                    $entry->name,
                    $entry->position,
                    $this->namespace,
                    $entry->plugin,
                    $entry->listener
                );
            }
        }
        return $this;
    }

    public function toArray()
    {
        $listeners = array();
        /** @var $handler Enlight_Event_Handler_Plugin */
        foreach ($this->listeners as $handler) {
            $listeners[] = $handler->toArray();
        }
        return $listeners;
    }
}