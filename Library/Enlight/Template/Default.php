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
 * @package    Enlight_Template
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

require_once('Smarty/Smarty.class.php');

/**
 * @category   Enlight
 * @package    Enlight_Template
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Template_Default extends Enlight_Template_Manager
{
    const BLOCK_REPLACE = 'replace';
    const BLOCK_APPEND = 'append';
    const BLOCK_PREPEND = 'prepend';

    /**
     * assigns a Smarty variable
     *
     * @param array|string $tpl_var the template variable name(s)
     * @param mixed        $value   the value to assign
     * @param boolean      $nocache if true any output of this variable will be not cached
     * @param boolean $scope the scope the variable will have  (local,parent or root)
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function assign($tpl_var, $value = null, $nocache = false, $scope = null)
    {
        if($scope === null || $scope === Smarty::SCOPE_LOCAL) {
            parent::assign($tpl_var, $value, $nocache);
        } elseif($scope === Smarty::SCOPE_ROOT) {
            $this->smarty->assign($tpl_var, $value);
        } elseif($scope === Smarty::SCOPE_GLOBAL) {
            $this->smarty->assignGlobal($tpl_var, $value);
        } elseif($scope == Smarty::SCOPE_PARENT) {
            $this->parent->assign($tpl_var, $value, $nocache);
        }
        return $this;
    }

    /**
     * Clears the given assigned template variable.
     *
     * @param   string|array|null $tpl_var the template variable(s) to clear
     * @param   int $scope
     * @return  Enlight_Template_Default instance for chaining
     */
    public function clearAssign($tpl_var, $scope = null)
    {
        if($tpl_var === null) {
            $function = 'clearAllAssign';
        } else {
            $function = 'clearAssign';
        }

        if($scope === null || $scope === Smarty::SCOPE_LOCAL) {
            parent::$function($tpl_var);
        } elseif($scope === Smarty::SCOPE_ROOT) {
            $this->smarty->$function($tpl_var);
        } elseif($scope == Smarty::SCOPE_PARENT) {
            $this->parent->$function($tpl_var);
        } elseif($scope === Smarty::SCOPE_GLOBAL) {
            if($tpl_var === null) {
                Smarty::$global_tpl_vars[$tpl_var] = array();
            } else {
                unset(Smarty::$global_tpl_vars[$tpl_var]);
            }
        }
        return $this;
    }

    /**
     * Extends a template block by name.
     *
     * @param $spec
     * @param $content
     * @param string $mode
     * @return void
     */
    public function extendsBlock($spec, $content, $mode = self::BLOCK_REPLACE)
    {
        if($mode === null) {
            $mode = self::BLOCK_REPLACE;
        }
    	if (strpos($content, $this->smarty->left_delimiter . '$smarty.block.child' . $this->smarty->right_delimiter) !== false) {
    		if (isset($this->block_data[$spec])) {
    			$content = str_replace(
                    $this->smarty->left_delimiter . '$smarty.block.child' . $this->smarty->right_delimiter,
                    $this->block_data[$spec]['source'],
                    $content
                );
    			unset($this->block_data[$spec]);
    		} else {
    			$content = str_replace($this->smarty->left_delimiter.'$smarty.block.child'.$this->smarty->right_delimiter, '', $content);
    		}
    	}
    	if (isset($this->block_data[$spec])) {
    		if (strpos($this->block_data[$spec]['source'], '%%%%SMARTY_PARENT%%%%') !== false) {
    			$content = str_replace('%%%%SMARTY_PARENT%%%%', $content, $this->block_data[$spec]['source']);
    		} elseif ($this->block_data[$spec]['mode'] == 'prepend') {
    			$content = $this->block_data[$spec]['source'] . $content;
    		} elseif ($this->block_data[$spec]['mode'] == 'append') {
    			$content .= $this->block_data[$spec]['source'];
    		}
    	}
    	$this->block_data[$spec] = array('source'=>$content, 'mode'=>$mode, 'file'=>null);
    }

    /**
     * @param $template_name
     * @return void
     */
	public function extendsTemplate($template_name)
    {
        //if(strpos($this->template_resource, 'extends:') !== 0) {
        //    $this->template_resource = 'extends:' . $this->template_resource;
        //}
    	$this->template_resource .= '|' . $template_name;
    }

    /**
     * Sets the cache id.
     *
     * @param   null $cacheId
     * @return  Enlight_Template_Default
     */
    public function setCacheId($cacheId = null)
    {
    	if(is_array($cacheId)) {
    		$cacheId = implode('|', $cacheId);
    	}
    	$this->cache_id = (string) $cacheId;
    	return $this;
    }

    /**
     * Extends the cache id.
     *
     * @param   null $cacheId
     * @return  Enlight_Template_Default
     */
    public function addCacheId($cacheId)
    {
    	if(is_array($cacheId)) {
    		$cacheId = implode('|', $cacheId);
    	} else {
            $cacheId = (string) $cacheId;
        }
    	if($this->cache_id === null) {
    		$this->cache_id = $cacheId;
    	} else {
    		$this->cache_id .= '|' . $cacheId;
    	}
    	return $this;
    }

    /**
     * @return Enlight_Template_Manager
     */
    public function Engine()
    {
    	return $this->smarty;
    }

    /**
     * @return Enlight_Template_Default
     */
    public function Template()
    {
    	return $this;
    }
}