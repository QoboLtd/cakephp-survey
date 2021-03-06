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
use Qobo\Survey\Model\Entity\SurveyEntry;
use Webmozart\Assert\Assert;

/**
 * Surveys Controller
 *
 * @property \Qobo\Survey\Model\Table\SurveysTable $Surveys
 * @property \Qobo\Survey\Model\Table\SurveyAnswersTable $SurveyAnswers
 * @property \Qobo\Survey\Model\Table\SurveyEntriesTable $SurveyEntries
 * @property \Qobo\Survey\Model\Table\SurveyQuestionsTable $SurveyQuestions
 * @property \Qobo\Survey\Model\Table\SurveyResultsTable $SurveyResults
 *
 * @method \Qobo\Survey\Model\Entity\Survey[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SurveysController extends AppController
{
    protected $SurveyEntries;

    protected $SurveyQuestions;

    protected $SurveyResults;

    protected $SurveyAnswers;

    protected $SurveyEntryQuestions;

    /**
     * @{inheritDoc}
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        /** @var \Qobo\Survey\Model\Table\SurveyQuestionsTable $SurveyQuestions */
        $table = TableRegistry::getTableLocator()->get('Qobo/Survey.SurveyQuestions');
        $this->SurveyQuestions = $table;

        /** @var \Qobo\Survey\Model\Table\SurveyResultsTable $SurveyResults */
        $table = TableRegistry::getTableLocator()->get('Qobo/Survey.SurveyResults');
        $this->SurveyResults = $table;

        /** @var \Qobo\Survey\Model\Table\SurveyEntriesTable $SurveyEntries */
        $table = TableRegistry::getTableLocator()->get('Qobo/Survey.SurveyEntries');
        $this->SurveyEntries = $table;

        $table = TableRegistry::getTableLocator()->get('Qobo/Survey.SurveyAnswers');
        $this->SurveyAnswers = $table;

        $table = TableRegistry::getTableLocator()->get('Qobo/Survey.SurveyEntryQuestions');
        $this->SurveyEntryQuestions = $table;
    }

    /**
     * Before Filter callback
     *
     * Preloading extra vars for all methods
     *
     * @param \Cake\Event\Event $event broadcasted.
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $categories = $this->Surveys->getSurveyCategories();
        $this->set(compact('categories'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function index()
    {
        $query = $this->Surveys->find()->order(['expiry_date' => 'DESC']);
        $surveys = $this->paginate($query);

        $this->set(compact('surveys'));
    }

    /**
     * View method
     *
     * @param string $id Survey id.
     * @return \Cake\Http\Response|void|null
     */
    public function view(string $id)
    {
        $questionTypes = $this->SurveyQuestions->getQuestionTypes();
        /** @var \Qobo\Survey\Model\Entity\Survey $survey */
        $survey = $this->Surveys->getSurveyData($id, true);

        $query = $this->SurveyEntries->find()
            ->where([
                'survey_id' => $survey->get('id'),
            ])
            ->order(['submit_date' => 'DESC']);
        $entries = $query->all();

        $this->set(compact('survey', 'questionTypes', 'entries'));
    }

    /**
     * Publish method
     *
     * @param string $id Survey id.
     * @return \Cake\Http\Response|void|null Redirects on successful add, renders view otherwise.
     */
    public function publish(string $id)
    {
        $survey = $this->Surveys->get($id);
        Assert::isInstanceOf($survey, EntityInterface::class);

        if ($this->request->is(['post', 'put', 'patch'])) {
            $validated = $this->Surveys->prepublishValidate($id, $this->getRequest());
            if (false === $validated['status']) {
                $this->Flash->error(implode("\r\n", $validated['errors']), ['escape' => false]);

                return $this->redirect($this->referer());
            }

            $data = $this->request->getData();
            $survey = $this->Surveys->patchEntity($survey, (array)$data);

            if ($this->Surveys->save($survey, ['publishSurvey' => true])) {
                $this->Flash->success((string)__d('Qobo/Survey', 'Survey was successfully published.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error((string)__d('Qobo/Survey', 'Couldn\'t publish the survey'));
        }

        $this->set(compact('survey'));
    }

    /**
     * Publish method
     *
     * @param string $id Survey id.
     * @return \Cake\Http\Response|void|null Redirects on successful add, renders view otherwise.
     */
    public function preview(string $id)
    {
        $saved = $data = [];
        $survey = $this->Surveys->getSurveyData($id, true);
        Assert::isInstanceOf($survey, EntityInterface::class);

        $this->set(compact('survey'));
    }

    /**
     * Publish method
     *
     * @param string $id Survey id.
     * @return \Cake\Http\Response|void|null Redirects on successful add, renders view otherwise.
     */
    public function duplicate(string $id)
    {
        $this->autoRender = false;

        $survey = $this->Surveys->getSurveyData($id);
        Assert::isInstanceOf($survey, EntityInterface::class);

        if ($this->request->is(['post', 'put', 'patch'])) {
            /** @var \Cake\Datasource\EntityInterface */
            $entity = $this->Surveys->duplicate($survey->get('id'));
            Assert::isInstanceOf($entity, EntityInterface::class);

            // @NOTE: saving parent_id as Duplicatable unsets origin id
            $entity = $this->Surveys->patchEntity($entity, ['parent_id' => $survey->get('id')]);
            $entity = $this->Surveys->save($entity);
            Assert::isInstanceOf($entity, EntityInterface::class);

            $sorted = $this->Surveys->setSequentialOrder($entity);
            $this->Flash->success((string)__d('Qobo/Survey', 'Survey was successfully duplicated'));

            return $this->redirect(['action' => 'view', $entity->get('id')]);
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $survey = $this->Surveys->newEntity();

        if ($this->request->is(['post', 'put', 'patch'])) {
            $survey = $this->Surveys->patchEntity($survey, (array)$this->request->getData());
            if ($this->Surveys->save($survey)) {
                $this->Flash->success((string)__d('Qobo/Survey', 'The survey has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error((string)__d('Qobo/Survey', 'The survey could not be saved. Please, try again.'));
        }
        $this->set(compact('survey'));
    }

    /**
     * Edit method
     *
     * @param string $id Survey id.
     * @return \Cake\Http\Response|void|null Redirects on successful edit, renders view otherwise.
     */
    public function edit(string $id)
    {
        $survey = $this->Surveys->getSurveyData($id);
        Assert::isInstanceOf($survey, EntityInterface::class);

        $redirect = ['controller' => 'Surveys', 'action' => 'view', $id];

        if (!empty($survey->get('publish_date'))) {
            $this->Flash->error((string)__d('Qobo/Survey', 'You cannot edit alredy published survey'));

            return $this->redirect($redirect);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $survey = $this->Surveys->patchEntity($survey, (array)$this->request->getData());
            if ($this->Surveys->save($survey)) {
                $this->Flash->success((string)__d('Qobo/Survey', 'The survey has been saved.'));

                return $this->redirect($redirect);
            }
            $this->Flash->error((string)__d('Qobo/Survey', 'The survey could not be saved. Please, try again.'));
        }
        $this->set(compact('survey'));
    }

    /**
     * Delete method
     *
     * @param string $id Survey id.
     * @return \Cake\Http\Response|void|null Redirects to index.
     */
    public function delete(string $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $survey = $this->Surveys->getSurveyData($id);
        Assert::isInstanceOf($survey, EntityInterface::class);

        if ($this->Surveys->delete($survey)) {
            $this->Flash->success((string)__d('Qobo/Survey', 'The survey has been deleted.'));
        } else {
            $this->Flash->error((string)__d('Qobo/Survey', 'The survey could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get Specific survey results based on submission id
     *
     * @param string $surveyId of the given survey
     * @param string $surveyEntryId of specific submission
     *
     * @return \Cake\Http\Response|void|null
     */
    public function viewSubmit(string $surveyId, string $surveyEntryId)
    {
        $survey = $this->Surveys->get($surveyId);

        $surveyEntry = $this->SurveyEntries->find()
            ->enableHydration(true)
            ->where([
                'id' => $surveyEntryId,
            ])
            ->contain([
                'SurveyEntryQuestions' => [
                    'SurveyQuestions',
                    'SurveyResults' => [
                        'SurveyAnswers',
                    ],
                ],
            ])
            ->first();

        $this->set(compact('survey', 'surveyEntry'));
    }

    /**
     * Submit action.
     *
     * @return \Cake\Http\Response|void|null
     */
    public function submit()
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $questions = [];

        $data = (array)$this->request->getData();
        Assert::isArray($data);

        if (!empty($data['SurveyResults'])) {
            $questions = $data['SurveyResults'];
        }

        if (empty($questions)) {
            $this->Flash->error((string)__d('Qobo/Survey', 'No questions submitted to survey'));

            return $this->redirect($this->referer());
        }

        $entry = $this->SurveyEntries->newEntity();
        $this->SurveyEntries->patchEntity($entry, $data);

        $saved = $this->SurveyEntries->save($entry);
        Assert::isInstanceOf($entry, SurveyEntry::class);

        foreach ($questions as $k => $item) {
            if (empty($item['survey_answer_id'])) {
                continue;
            }
            $questionEntry = $this->SurveyEntryQuestions->newEntity();

            $questionEntry->set('survey_entry_id', $entry->get('id'));
            $questionEntry->set('survey_question_id', $item['survey_question_id']);

            $this->SurveyEntryQuestions->save($questionEntry);

            if (!is_array($item['survey_answer_id'])) {
                $result = $this->SurveyResults->saveResultsEntity($entry, $item, $questionEntry);
            } else {
                foreach ($item['survey_answer_id'] as $answer) {
                    $tmp = $item;
                    $tmp['survey_answer_id'] = $answer;
                    $this->SurveyResults->saveResultsEntity($entry, $tmp, $questionEntry);
                }
            }
        }
        $this->Flash->success((string)__d('Qobo/Survey', 'Successfully submitted survey'));

        return $this->redirect(['controller' => 'Surveys', 'action' => 'view', $data['SurveyEntries']['survey_id']]);
    }
}
