<?php
/**
 * Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Qobo\Survey\Controller;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Webmozart\Assert\Assert;

/**
 * @property \Qobo\Survey\Model\Table\SurveysTable $Surveys
 * @property \Qobo\Survey\Model\Table\SurveyQuestionsTable $SurveyQuestions
 * @property \Qobo\Survey\Model\Table\SurveySectionsTable $SurveySections
 */
class SurveyQuestionsController extends AppController
{
    /**
     * Initializing the controller
     * and also preload Surveys Table
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        /** @var \Qobo\Survey\Model\Table\SurveysTable $table */
        $table = TableRegistry::getTableLocator()->get('Qobo/Survey.Surveys');
        $this->Surveys = $table;

        /** @var \Qobo\Survey\Model\Table\SurveySectionsTable $table */
        $table = TableRegistry::getTableLocator()->get('Qobo/Survey.SurveySections');
        $this->SurveySections = $table;
    }

    /**
     * Before Filter callback
     *
     * Preloading numerous vars for question methods
     *
     * @param \Cake\Event\Event $event broadcasted.
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $questionTypes = $this->SurveyQuestions->getQuestionTypes();
        $this->set(compact('questionTypes'));
    }

    /**
     * View method
     *
     * @param string $surveyId of the survey or either its slug
     * @param string $id Survey Question id.
     * @return \Cake\Http\Response|void|null
     */
    public function view(string $surveyId, string $id)
    {
        $surveyQuestion = $this->SurveyQuestions->get($id, [
            'contain' => [
                'Surveys',
                'SurveyAnswers' => [
                    'sort' => ['SurveyAnswers.order' => 'ASC'],
                ],
            ],
        ]);
        Assert::isInstanceOf($surveyQuestion, EntityInterface::class);

        $survey = $this->Surveys->getSurveyData($surveyId, false);
        Assert::isInstanceOf($survey, EntityInterface::class);

        $this->set(compact('surveyQuestion', 'survey'));
    }

    /**
     * View method
     *
     * @param string $surveyId of the survey or either its slug
     * @param string $id Survey Question id.
     * @return \Cake\Http\Response|void|null
     */
    public function preview(string $surveyId, string $id)
    {
        $this->request->allowMethod(['get']);

        $survey = $this->Surveys->getSurveyData($surveyId);

        $surveyQuestion = $this->SurveyQuestions->get($id, [
            'contain' => [
                'Surveys',
                'SurveyAnswers' => [
                    'sort' => ['SurveyAnswers.order' => 'ASC'],
                ],
            ],
        ]);

        Assert::isInstanceOf($surveyQuestion, EntityInterface::class);

        $this->set(compact('surveyQuestion', 'survey'));
    }

    /**
     * Add method
     *
     * @param string $surveyId of the survey or either its slug
     * @return \Cake\Http\Response|void|null Redirects on successful add, renders view otherwise.
     */
    public function add(string $surveyId)
    {
        $survey = $this->Surveys->getSurveyData($surveyId);
        Assert::isInstanceOf($survey, EntityInterface::class);

        $surveyQuestion = $this->SurveyQuestions->newEntity();

        $sections = $this->SurveySections->getSurveySectionsList($survey->get('id'));

        if ($this->request->is(['post', 'put', 'patch'])) {
            $surveyQuestion = $this->SurveyQuestions->patchEntity($surveyQuestion, (array)$this->request->getData());

            if ($this->SurveyQuestions->save($surveyQuestion)) {
                $this->Flash->success((string)__d('Qobo/Survey', 'The survey question has been saved.'));

                return $this->redirect(['controller' => 'SurveyAnswers', 'action' => 'add', $surveyId, $surveyQuestion->get('id')]);
            }
        }

        $this->set(compact('surveyQuestion', 'survey', 'sections'));
    }

    /**
     * Edit method
     *
     * @param string $surveyId of the survey or either its slug
     * @param string $id Survey Question id.
     * @return \Cake\Http\Response|void|null Redirects on successful edit, renders view otherwise.
     */
    public function edit(string $surveyId, string $id)
    {
        $survey = $this->Surveys->getSurveyData($surveyId);
        Assert::isInstanceOf($survey, EntityInterface::class);

        $surveyQuestion = $this->SurveyQuestions->get($id);

        $sections = $this->SurveySections->getSurveySectionsList($survey->get('id'));

        if ($this->request->is(['patch', 'post', 'put'])) {
            $surveyQuestion = $this->SurveyQuestions->patchEntity($surveyQuestion, (array)$this->request->getData());

            if ($this->SurveyQuestions->save($surveyQuestion)) {
                $this->Flash->success((string)__d('Qobo/Survey', 'The survey question has been saved.'));

                return $this->redirect($this->referer());
            }
        }

        $this->set(compact('surveyQuestion', 'survey', 'sections'));
    }

    /**
     * Delete method
     *
     * @param string $surveyId of the survey or either its slug
     * @param string $id Survey Question id.
     *
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     *
     * @return \Cake\Http\Response|void|null Redirects to index.
     */
    public function delete(string $surveyId, string $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        $question = $this->SurveyQuestions->get($id);

        if ($this->SurveyQuestions->delete($question)) {
            $this->Flash->success((string)__d('Qobo/Survey', 'The survey question has been deleted.'));

            $survey = $this->Surveys->getSurveyData($surveyId);
            Assert::isInstanceOf($survey, EntityInterface::class);

            $surveyId = empty($survey->get('slug')) ? $survey->get('id') : $survey->get('slug');
            $redirect = [
                'controller' => 'Surveys',
                'action' => 'view',
                $surveyId,
            ];

            return $this->redirect($redirect);
        } else {
            $this->Flash->error((string)__d('Qobo/Survey', 'The survey question could not be deleted. Please, try again.'));
        }
    }
}
