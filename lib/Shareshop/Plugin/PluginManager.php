<?php
namespace Shareshop\Plugin;
/**
 * 
 * @author Admin
 *
 */

class PluginManager {
	
	protected $_plugins = array();
	protected $_lastEvent = "";
	protected $_state = null;
	
	public function register(AbstractPlugin $plugin, $events = array())
	{
		$this->_plugins[spl_object_hash($plugin)] = array('plugin' => $plugin, 'events' => $events);
	}
	
	public function remove(AbstractPlugin $plugin) 
	{
		$this->_plugins[spl_object_hash($plugin)] = null;
	}
	
	public function inform($event, $state = null) 
	{
		$this->_lastEvent = $event;
		$this->_state = $state;
		foreach($this->_plugins as $data) {
			if($data !== null && is_array($data)) {
				if (!is_array($data['events']) || empty($data['events'])) {
					$data['plugin']->update($event);
				} elseif (in_array($event, $data['events'])) {
					$data['plugin']->update($event);
				}	
			}
		}
	}
	
	public function getState()
	{
		return $this->_state;
	}
}