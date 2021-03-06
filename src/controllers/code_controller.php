<?php

/**
 * Description of code
 *
 * @author robertopereznygaard
 */
class CodeController extends CodazoController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->_app->register(new Silex\Provider\FormServiceProvider);
		$form = $this->_app['form.factory']->createBuilder('form')
			->add('code', 'textarea', array('label' => ' ', 'attr' => array('style' => 'height: 300px', 'class' => 'span12')))
			//->add('lang', 'text', array('label' => 'Language', 'required' => false, 'attr' => array('placeholder' => 'Auto')))
			->getForm();

		$request = $this->_app['request'];
		if ('POST' == $request->getMethod())
		{
			$form->bindRequest($request);
			if ($form->isValid())
			{
				$data = $form->getData();

				$values = array(
					'code' => $data['code'],
					'created_at' => time(),
					'ip' => $_SERVER['REMOTE_ADDR'],
					'language' => $data['lang']
				);

				$codeModel = new CodeModel();
				$uniqueId = $codeModel->saveCode($values);

				return $this->_app->redirect('/' . $uniqueId);
			}
		}

		$viewParams = array(
			'form' => $form->createView(),
			'currentSection' => 'paste'
		);

		return $this->_app['twig']->render('paste.twig', $viewParams);
	}

	public function viewCode($id)
	{
		$codeModel = new CodeModel();
		$code = $codeModel->get(array('unique_id' => $id));

		$viewParams = array(
			'code' => $code,
			'currentSection' => 'view',
			'language'	=> '',
			'id' => $id
		);

		return $this->_app['twig']->render('view.twig', $viewParams);
	}

	public function viewRaw($id)
	{
		$codeModel = new CodeModel();

		return $this->_app['twig']->render('view_raw.twig', array('code' => $codeModel->get(array('unique_id' => $id))));
	}

}