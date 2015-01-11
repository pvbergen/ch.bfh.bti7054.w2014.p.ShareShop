<?php
namespace Application\Controller;

use Shareshop\Controller;
use Shareshop\Application;
use Application\Models\Db\Article;
use Application\Models\Db\User;
use Application\Models\Db\Exchange;
use Application\Plugin\Auth;
use Application\Models\Db\ExchangeStep;

class ExchangeController extends Controller {
	
	public function proposeAction()
	{
		$id = $this->request->getParameters()['id'];
		//$remark = $this->request->getPost()['remark'];
		$remark = "";
		$article = Article::findById($id);
		$user = User::findById(Auth::getSession()->getUserId());
		
		$data['request'] = false;
		
		if (Exchange::findActiveByArticleAndUser($article, $user) == null) {
			$step = ExchangeStep::create();
			$step->
				setCreated(time())->
				setRemark($remark)->
				setType(ExchangeStep::REQUEST)->
				addArticle($article->getId());
			
			$exchange = Exchange::create();
			$exchange->
				setRequestingUser($user->getId())->
				setAnsweringUser($article->getUserId())->
				setState(Exchange::STATE_ACTIVE)->
				addStep($step);
			
			$exchange->save();
			$data['request'] = true;
		}
		$this->view->renderAsAjax(true);
		$this->view->register('exchange/request', $data);	
		$this->view->render();		
	}
	
	public function listAction()
	{
		$user = User::findById(Auth::getSession()->getUserId());
		$exchanges = Exchange::findByUser($user);
		$grouped = array(
			Exchange::STATE_ACTIVE => array(),
			Exchange::STATE_CANCELLED => array(),
			Exchange::STATE_COMPLETED => array()
		);
		
		foreach ($exchanges as $exchange) {
			$steps = $exchange->getSteps();
			$currentStep = $steps[count($steps)-1];
			$grouped[$exchange->getState()][] = 
				array('exchange' => $exchange, 'currentStep' => $currentStep);
		}
		$data['currentUser'] = $user;
		$data['exchanges'] = $grouped;
		$data['active_key'] = Exchange::STATE_ACTIVE;
		$data['completed_key'] = Exchange::STATE_COMPLETED;
		$data['cancelled_key'] = Exchange::STATE_CANCELLED;

		$data['request_key'] = ExchangeStep::TYPE_REQUEST;
		$data['pick_key'] = ExchangeStep::TYPE_PICK;
		$data['reoffer_key'] = ExchangeStep::TYPE_REOFFER;
		$data['exchange_key'] = ExchangeStep::TYPE_EXCHANGE;
		
		$this->view->register('exchange/list', $data);
		$this->view->render();
		return;
		
		if (Exchange::findByUser($article, $user) == null) {
			$step = ExchangeStep::create();
			$step->
			setCreated(time())->
			setRemark($remark)->
			setType(ExchangeStep::REQUEST)->
			addArticle($article->getId());
				
			$exchange = Exchange::create();
			$exchange->
			setRequestingUser($user->getId())->
			setAnsweringUser($article->getUserId())->
			setState(0)->
			addStep($step);
				
			$exchange->save();
			$data['request'] = true;
		}
		
	}
	
}