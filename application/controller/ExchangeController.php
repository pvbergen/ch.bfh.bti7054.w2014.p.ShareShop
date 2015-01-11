<?php
namespace Application\Controller;

use Shareshop\Controller;

class ExchangeController extends Controller {
	
	public function proposeAction()
	{
		print_r($this->request->getParameters());
		$id = $this->request->getParameters()['id'];
		
		
		
	}
	
}