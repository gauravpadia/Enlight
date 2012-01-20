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
 * to license
 * @shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * The Enlight_Plugin_PluginManager allows to extends the enlight application or enlight components.
 * Registered plugins will be mapped into a hierarchical structure via namespaces.
 * The manager can register single plugins or register multiple plugins over namespaces.
 * Depending on the namespace the plugins will read from a directory structure automatically or will be instantiated
 * by a passed config.
 * The Enlight_Plugin_PluginCollection serves as an array of registered plugins, or namespaces.
 * For additional plugins the Enlight_Bootstrap will serves as basic class.
 *
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Plugin_PluginManager extends Enlight_Plugin_PluginCollection
{
    /**
     * Application instance.
     *
     * @var Enlight_Application
     */
    protected $application;

    /**
     * @param Enlight_Application $application
     */
    public function __construct(Enlight_Application $application)
    {
        $this->setApplication($application);
        parent::__construct();
    }

    /**
     * @param Enlight_Plugin_Namespace $namespace
     * @return Enlight_Plugin_PluginManager
     */
    public function registerNamespace(Enlight_Plugin_Namespace $namespace)
    {
        $namespace->setManager($this);
        $this->plugins[$namespace->getName()] = $namespace;
        return $this;
    }

    /**
     * Sets the application instance.
     *
     * @param  Enlight_Application $application
     * @return Enlight_Bootstrap
     */
    public function setApplication(Enlight_Application $application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * Returns the application instance.
     *
     * @return Enlight_Application
     */
    public function Application()
    {
        return $this->application;
    }
}