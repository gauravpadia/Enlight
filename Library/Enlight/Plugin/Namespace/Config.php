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
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * The Enlight_Plugin_Namespace_Config loads plugins over an Enlight_Config.
 *
 * With the Enlight_Plugin_Namespace_Config component, plugins can be loaded and stored over an Enlight_Config.
 * The Enlight_Plugin_Namespace_Config will be extended automatically if another plugin
 * is registered for the namespace.
 * It also offers the possibility to configure the plugin directly from this configuration.
 * To get the configuration of the loaded plugins, the Enlight_Plugin_Namespace_Config offers
 * a function to return the config.
 * To use this directly in the plugin there is the extra extended Plugin_Bootstrap_Config component.
 *
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Plugin_Namespace_Config extends Enlight_Plugin_Namespace
{
    /**
     * @var Enlight_Config Contains an instance of the Enlight_Config. Can be overwritten in the class
     * constructor by using the $options["storage"] array element.
     */
    protected $storage;

    /**
     * @var Enlight_Event_Subscriber Contains an instance of Enlight_Event_Subscriber.
     */
    protected $subscriber;

    /**
     * The Enlight_Plugin_Namespace_Config class constructor expects a storage (Enlight_Config).
     * The options array must contain an array element named "storage" which contains an additional array
     * with storage settings or only with a name for the storage.
     * If the name is passed as array, it is used to instantiate the storage.
     *
     * @param   string     $name
     * @param   null|array $options
     */
    public function __construct($name, $options = null)
    {
        if (is_array($name)) {
            $options = $name;
        }

        if (is_string($options)) {
            $options = array('storage' => $options);
        }
        if (!isset($options['storage'])) {
            $options['storage'] = $name;
        }
        if (is_string($options['storage'])) {
            $this->storage = new Enlight_Config(
                $options['storage'],
                array(
                    'allowModifications' => true,
                    'adapter' => isset($options['storageAdapter']) ? $options['storageAdapter'] : null,
                    'section' => isset($options['section']) ? $options['section'] : 'production'
                )
            );
        } elseif ($options['storage'] instanceof Enlight_Config) {
            $this->storage = $options['storage'];
        }

        parent::__construct($name);
    }

    /**
     * Loads a plugin in the plugin namespace by name over the Enlight_Config.
     *
     * @throws  Enlight_Exception
     * @param   $name
     * @return  Enlight_Plugin_Namespace_Config
     */
    public function load($name)
    {
        if ($this->storage->plugins->$name === null || $this->plugins->offsetExists($name)) {
            return parent::load($name);
        }
        $item = $this->storage->plugins->$name;

        /** @var $plugin Enlight_Plugin_Bootstrap_Config */
        $plugin = new $item->class($name, $this);
        return parent::registerPlugin($plugin);
    }

    /**
     * Writes all registered plugins into the storage.
     * The subscriber and the registered plugins are converted to an array.
     *
     * @return  Enlight_Plugin_Namespace_Config
     */
    public function write()
    {
        $this->storage->plugins = $this->toArray();
        $this->storage->listeners = $this->Subscriber()->toArray();
        $this->storage->write();
        return $this;
    }

    /**
     * Loads all plugins in the plugin namespace over the storage.
     *
     * @return  Enlight_Plugin_Namespace_Config
     */
    public function read()
    {
        if ($this->storage->plugins !== null) {
            foreach ($this->storage->plugins as $name => $value) {
                $this->load($name);
            }
        }
        return $this;
    }

    /**
     * Returns the instance of the Enlight_Event_Subscriber_Plugin. If the subscriber
     * isn't instantiated the function will load it automatically.
     *
     * @return  Enlight_Event_Subscriber_Plugin
     */
    public function Subscriber()
    {
        if ($this->subscriber === null) {
            $this->subscriber = new Enlight_Event_Subscriber_Plugin($this, $this->storage);
        }
        return $this->subscriber;
    }

    /**
     * Returns the plugin configuration by the plugin name. If the
     * plugin has no config, the config is automatically set an empty array.
     *
     * @param   string $name
     * @return  Enlight_Config|array
     */
    public function getConfig($name)
    {
        $item = $this->storage->plugins->$name;
        if (!isset($item->config)) {
            $item->config = array();
        }
        return $item->config;
    }

    /**
     * Registers a plugin in the collection.
     *
     * @param   Enlight_Plugin_Bootstrap_Config $plugin
     * @return  Enlight_Plugin_Namespace_Config
     */
    public function registerPlugin(Enlight_Plugin_Bootstrap_Config $plugin)
    {
        parent::registerPlugin($plugin);
        $plugin->install();
        return $this;
    }

    /**
     * Converts the internal plugin property to an array and returns it.
     * @return  array
     */
    public function toArray()
    {
        $this->read();
        $plugins = array();
        /** @var $plugin Enlight_Plugin_Bootstrap_Config */
        foreach ($this->plugins as $name => $plugin) {
            $plugins[$name] = array(
                'name' => $plugin->getName(),
                'class' => get_class($plugin),
                'config' => $plugin->Config()
            );
        }
        return $plugins;
    }
}