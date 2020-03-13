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
namespace Qobo\Survey\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Qobo\Survey\Model\Entity\SurveyEntry;
use Qobo\Survey\Model\Entity\SurveyEntryQuestion;
use Webmozart\Assert\Assert;

/**
 * SurveyResults Model
 *
 * @property \Qobo\Survey\Model\Table\SurveysTable|\Cake\ORM\Association\BelongsTo $Surveys
 * @property \Qobo\Survey\Model\Table\SurveyQuestionsTable|\Cake\ORM\Association\BelongsTo $SurveyQuestions
 * @property \Cake\ORM\Association\BelongsTo $SurveyAnswers
 * @property \Qobo\Survey\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \Qobo\Survey\Model\Entity\SurveyResult get($primaryKey, $options = [])
 * @method \Qobo\Survey\Model\Entity\SurveyResult newEntity($data = null, array $options = [])
 * @method \Qobo\Survey\Model\Entity\SurveyResult[] newEntities(array $data, array $options = [])
 * @method \Qobo\Survey\Model\Entity\SurveyResult|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Qobo\Survey\Model\Entity\SurveyResult patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Qobo\Survey\Model\Entity\SurveyResult[] patchEntities($entities, array $data, array $options = [])
 * @method \Qobo\Survey\Model\Entity\SurveyResult findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SurveyResultsTable extends Table
{

    /**
     * Initialize method
     *
     * @param mixed[] $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('survey_results');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Surveys', [
            'foreignKey' => 'survey_id',
            'joinType' => 'INNER',
            'className' => 'Qobo/Survey.Surveys',
        ]);

        //@FIXME: get rid off user s in survey_results
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'Qobo/Survey.Users',
        ]);

        $this->belongsTo('SurveyQuestions', [
            'foreignKey' => 'survey_question_id',
            'joinType' => 'INNER',
            'className' => 'Qobo/Survey.SurveyQuestions',
        ]);

        $this->belongsTo('SurveyAnswers', [
            'foreignKey' => 'survey_answer_id',
            'joinType' => 'INNER',
            'className' => 'Qobo/Survey.SurveyAnswers',
        ]);

        $this->belongsTo('SurveyEntries', [
            'foreignKey' => 'submit_id',
            'joinType' => 'INNER',
            'className' => 'Qobo/Survey.SurveyEntries',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->uuid('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('result')
            ->maxLength('result', 4294967295)
            ->allowEmpty('result');

        $validator
            ->dateTime('trashed')
            ->allowEmpty('trashed');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['survey_question_id'], 'SurveyQuestions'));
        $rules->add($rules->existsIn(['survey_answer_id'], 'SurveyAnswers'));

        return $rules;
    }

    /**
     * Save Results Entity
     *
     * @param \Qobo\Survey\Model\Entity\SurveyEntry $entry instance
     * @param mixed[] $data with post data
     * @param \Qobo\Survey\Model\Entity\SurveyEntryQuestion $questionEntry instance
     * @param mixed[] $options for entity options
     *
     * @return \Qobo\Survey\Model\Entity\SurveyResult|bool $result
     */
    public function saveResultsEntity(SurveyEntry $entry, array $data, SurveyEntryQuestion $questionEntry, array $options = [])
    {
        $result = false;
        $score = 0;
        if (empty($data)) {
            return $result;
        }

        $answersTable = TableRegistry::getTableLocator()->get('Qobo/Survey.SurveyAnswers');

        $answerEntity = $answersTable->find()
            ->enableHydration(true)
            ->where([
                'id' => $data['survey_answer_id'],
            ])
            ->first();

        Assert::isInstanceOf($answerEntity, EntityInterface::class);
        $score = $answerEntity->get('score');

        $entity = $this->newEntity(null, $options);

        if (!empty($data['id']) && !empty($options['accessibleFields'])) {
            $entity->set('id', $data['id']);
        }

        $entity->set('survey_entry_question_id', $questionEntry->get('id'));
        $entity->set('survey_question_id', $questionEntry->get('survey_question_id'));
        $entity->set('result', (!empty($data['result']) ? $data['result'] : null));
        $entity->set('survey_answer_id', $data['survey_answer_id']);
        $entity->set('score', $score);

        $result = $this->save($entity);

        if (!$result) {
            Log::error(print_r($entity->getErrors(), true));
        }

        return $result;
    }
}
