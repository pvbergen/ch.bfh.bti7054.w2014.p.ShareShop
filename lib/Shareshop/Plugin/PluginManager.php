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
	
	public function register(AbstractPlugin $plugin, $events = array())
	{
		$this->_plugins[spl_object_hash($plugin)] = array('plugin' => $plugin, 'events' => $events);
	}
	
	public function remove(AbstractPlugin $plugin) 
	{
		$this->_plugins[spl_object_hash($plugin)] = null;
	}
	
	public function inform($event) 
	{
		$this->_lastEvent = $event;
		foreach($this->_plugins as $data) {
			if (!is_array($data['events']) || empty($data['events'])) {
				$data['plugin']->update($event);
			} elseif (in_array($event, $data['events'])) {
				$data['plugin']->update($event);
			}
		}
	}
	
	public function getState()
	{
		return $this->_lastEvent;
	}
}