/**
 * Enlight ExtJS
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
 * @package    Enlight_ExtJs
 * @copyright  Copyright (c) 2012, Mitchell Simoens <mitchell.simoens@sencha.com>
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @link       http://github.com/mitchellsimoens/SubAppDemo/
 * @author     Mitchell Simoens <mitchell.simoens@sencha.com>
 * @author     $Author$
 */

/**
 * Override the default ext application
 * to add our sub application functionality
 *
 * {@link http://github.com/mitchellsimoens/SubAppDemo/}
 *
 * @category   Enlight
 * @package    Enlight_ExtJs
 * @license    http://enlight.de/license     New BSD License
 */
Ext.override(Ext.app.EventBus, {
    uncontrol: function(controllerArray) {
        var me  = this,
            bus = me.bus,
            deleteThis, idx;

        Ext.iterate(bus, function(ev, controllers) {
            Ext.iterate(controllers, function(query, controller) {
                deleteThis = false;

                Ext.iterate(controller, function(controlName) {
                    idx = controllerArray.indexOf(controlName);

                    if (idx >= 0) {
                        deleteThis = true;
                    }
                });

                if (deleteThis) {
                    delete controllers[query];
                }
            });
        });
    }
});
