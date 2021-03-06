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
		$type = $this->request->getParameters()['type'];
		$type = ($type == 'borrow' || $type == 'exchange') ? $type : "exchange";
		$postData = $this->request->getPost();
		
		$exchangeType = ExchangeStep::TYPE_BORROW;
		if ($type == "exchange") {
			$exchangeType = ExchangeStep::TYPE_REQUEST;
		}
		
		$article = Article::findById($postData['exchangeArticle']);
		$user = User::findById(Auth::getSession()->getUserId());
				
		$data['request'] = false;

		if (Exchange::findActiveByArticleAndUser($article, $user) == null) {
			$step = ExchangeStep::create();
			$step->
				setCreated(time())->
				setRemark("")->
				setType($exchangeType)->
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
	
	public function listborrowAction()
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
			if ($steps[0]->getType() == ExchangeStep::TYPE_BORROW) {
				$currentStep = $steps[count($steps)-1];
				$grouped[$exchange->getState()][] =
				array('exchange' => $exchange, 'currentStep' => $currentStep);
			}
		}
		
		$data['currentUser'] = $user;
		$data['exchanges'] = $grouped;
		$data['active_key'] = Exchange::STATE_ACTIVE;
		$data['completed_key'] = Exchange::STATE_COMPLETED;
		$data['cancelled_key'] = Exchange::STATE_CANCELLED;

		$data['borrow_key'] = ExchangeStep::TYPE_BORROW;
		$data['exchange_key'] = ExchangeStep::TYPE_EXCHANGE;
		
		$this->view->register('exchange/listborrow', $data);
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->render();
	}
	
	public function listexchangeAction()
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
			if ($steps[0]->getType() == ExchangeStep::TYPE_REQUEST) {
				$currentStep = $steps[count($steps)-1];
				$grouped[$exchange->getState()][] =
				array('exchange' => $exchange, 'currentStep' => $currentStep);
			}
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
	
		$this->view->register('exchange/listexchange', $data);
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->render();
	}
	
	public function showexchangeAction()
	{
		$id = $this->request->getParameters()['item'];
		$user = User::findById(Auth::getSession()->getUserId());
		$exchange = Exchange::findById($id);
		
		$postData = $this->request->getPost();
		if (isset($postData['counterOfferSubmit'])) {
			$this->_saveCounterSelection($exchange, $postData['exchangeSelection']);
		} elseif (isset($postData['exhangeSubmit'])) {
			$this->_submitExchange($exchange, "");
		} elseif (isset($postData['exhangeCancel'])) {
			$this->_cancelExchange($exchange);
		} elseif (isset($postData['exhangeRate'])) {
			$this->_saveRating($exchange, $user, $postData['rating']);
		}
		
		$steps = $exchange->getSteps();
		$currentStep = $steps[count($steps)-1];
		
		$data['currentUser'] = $user;
		$data['exchange'] = $exchange;
		$data['currentStep'] = $currentStep;
		if (
				$exchange->getRequestingUser()->getId() != $user->getId() && 
				(
					$currentStep->getType() == ExchangeStep::TYPE_REQUEST ||
					$currentStep->getType() == ExchangeStep::TYPE_REOFFER
				)
			) {
			$data['articles'] = Article::findArticlesByUserId($exchange->getRequestingUser()->getId());
		}
		
		$data['request_key'] = ExchangeStep::TYPE_REQUEST;
		$data['pick_key'] = ExchangeStep::TYPE_PICK;
		$data['reoffer_key'] = ExchangeStep::TYPE_REOFFER;
		$data['exchange_key'] = ExchangeStep::TYPE_EXCHANGE;
		
		$this->view->register('exchange/show', $data);
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->render();
	}
	

	public function showborrowAction()
	{
		$id = $this->request->getParameters()['item'];
		$user = User::findById(Auth::getSession()->getUserId());
		$exchange = Exchange::findById($id);
	
		$postData = $this->request->getPost();
		if (isset($postData['exhangeSubmit'])) {
			$this->_terminateExchange($exchange, $postData['exchangeRemark']);
		} elseif (isset($postData['exhangeCancel'])) {
			$this->_cancelExchange($exchange);
		} elseif (isset($postData['exhangeRate'])) {
			$this->_saveRating($exchange, $user, $postData['rating']);
		}
	
		$steps = $exchange->getSteps();
		$currentStep = $steps[count($steps)-1];
	
		$data['currentUser'] = $user;
		$data['exchange'] = $exchange;
		$data['currentStep'] = $currentStep;
		if (
				$exchange->getRequestingUser()->getId() != $user->getId() &&
				(
						$currentStep->getType() == ExchangeStep::TYPE_REQUEST ||
						$currentStep->getType() == ExchangeStep::TYPE_REOFFER
				)
		) {
			$data['articles'] = Article::findArticlesByUserId($exchange->getRequestingUser()->getId());
		}
	
	
		$data['borrow_key'] = ExchangeStep::TYPE_BORROW;
		$data['exchange_key'] = ExchangeStep::TYPE_EXCHANGE;
	
		$this->view->register('exchange/showborrow', $data);
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->render();
	}
	
	
	protected function _saveCounterSelection(Exchange $exchange, $articleId)
	{
		$article = Article::findById($articleId);
		$step = ExchangeStep::create();
		$step->setExchangeId($exchange->getId())->setType(ExchangeStep::TYPE_PICK)->addArticle($article->getId())->setRemark("")->setCreated(time());
		$step->save();
		$exchange->getSteps();
	}
	
	protected function _submitExchange(Exchange $exchange, $remark)
	{
		$steps = $exchange->getSteps();
		$requestedArticle = $steps[0]->getArticles()[0];
		$requestedArticle->setUserId($exchange->getRequestingUser()->getId());
		$requestedArticle->modify();
		
		$offeredArticle = $steps[1]->getArticles()[0];
		$offeredArticle->setUserId($exchange->getAnsweringUser()->getId());
		$offeredArticle->modify();

		$this->_terminateExchange($exchange, $remark);
	}
	
	protected function _terminateExchange(Exchange $exchange, $remark)
	{	
		$step = ExchangeStep::create();
		$step->setType(ExchangeStep::TYPE_EXCHANGE)->setExchangeId($exchange->getId())->setRemark($remark)->setCreated(time());
		$step->save();
		$exchange->getSteps();
	}
	
	protected function _cancelExchange(Exchange $exchange)
	{
		$exchange->setState(Exchange::STATE_CANCELLED);
		$exchange->save();
	}
	
	protected function _saveRating(Exchange $exchange, User $user, $rating)
	{
		if ($user->getId() == $exchange->getRequestingUser()->getId()) {
			$exchange->setRequestingRating($rating);		
		} else {
			$exchange->setAnsweringRating($rating);
		}
		if ($exchange->getAnsweringRating() > 0 && $exchange->getRequestingRating() > 0) {
			$exchange->setState(Exchange::STATE_COMPLETED);
		}
		$exchange->save();
	}
}