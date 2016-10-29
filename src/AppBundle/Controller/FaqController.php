<?php

namespace AppBundle\Controller;

use AppBundle\Facade\AnswerFacade;
use AppBundle\Facade\FaqFacade;
use AppBundle\Facade\QuestionFacade;
use AppBundle\FormType\FaqFormType;
use AppBundle\FormType\VO\FaqVO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="app.controller.faq_controller")
 */
class FaqController
{
	/** @var FormFactory */
	private $formFactory;

	/** @var QuestionFacade */
	private $questionFacade;

	/** @var AnswerFacade */
	private $answerFacade;

	/** @var FaqFacade */
	private $faqFacade;

	/**
	 * @param FormFactory $formFactory
	 * @param QuestionFacade $questionFacade
	 * @param AnswerFacade $answerFacade
	 * @param FaqFacade $faqFacade
	 */
	public function __construct(FormFactory $formFactory, QuestionFacade $questionFacade, AnswerFacade $answerFacade, FaqFacade $faqFacade)
	{
		$this->formFactory = $formFactory;
		$this->questionFacade = $questionFacade;
		$this->answerFacade = $answerFacade;
		$this->faqFacade = $faqFacade;
	}

	/**
	 * @Route("/faq", name="faq")
	 * @Template("faq/faq.html.twig")
	 */
	public function faqAction(Request $request)
	{
		$questions = $this->questionFacade->findAll();
		$answers = $this->answerFacade->findByQuestions($questions);

		return [
			"questions" => $questions,
			"answers" => $answers,
		];
	}

	/**
	 * @Route("/faq/list", name="faq_list")
	 * @Template("faq/list.html.twig")
	 */
	public function listAction()
	{
		$questions = $this->questionFacade->findAll();
		$answers = $this->answerFacade->findByQuestions($questions);

		return [
			"questions" => $questions,
			"answers" => $answers,
		];
	}

	/**
	 * @Route("/faq/add", name="faq_add")
	 * @Template("faq/add.html.twig")
	 * @param Request $request
	 * @return array
	 */
	public function addAction(Request $request)
	{
		$faqVO = new FaqVO();
		$form = $this->formFactory->create(FaqFormType::class, $faqVO);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->faqFacade->insert($faqVO);
		}

		return [
			"form" => $form->createView()
		];
	}

	/**
	 * @Route("/faq/edit/{id}", name="faq_edit", requirements={"id": "\d+"})
	 * @Template("faq/edit.html.twig")
	 * @param int $id
	 * @param Request $request
	 * @return array
	 */
	public function editAction($id, Request $request)
	{
		$question = $this->questionFacade->findById($id);
		$answer = $this->answerFacade->findByQuestion($question);

		$faqVO = FaqVO::fromEntity($question, $answer);
		$form = $this->formFactory->create(FaqFormType::class, $faqVO);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->faqFacade->update($question, $answer, $faqVO);
		}

		return [
			"form" => $form->createView()
		];
	}
}