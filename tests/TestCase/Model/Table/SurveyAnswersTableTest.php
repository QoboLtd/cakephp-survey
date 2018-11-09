<?php
namespace Qobo\Survey\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Qobo\Survey\Model\Table\SurveyAnswersTable;

/**
 * Qobo\Survey\Model\Table\SurveyAnswersTable Test Case
 */
class SurveyAnswersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Qobo\Survey\Model\Table\SurveyAnswersTable
     */
    public $SurveyAnswers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.qobo/survey.survey_answers',
        'plugin.qobo/survey.survey_questions',
        'plugin.qobo/survey.surveys',
        'plugin.qobo/survey.survey_results',
        'plugin.qobo/survey.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Survey.SurveyAnswers') ? [] : ['className' => SurveyAnswersTable::class];
        /**
         * @var \Qobo\Survey\Model\Table\SurveyAnswersTable $table
         */
        $table = TableRegistry::get('Survey.SurveyAnswers', $config);
        $this->SurveyAnswers = $table;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SurveyAnswers);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
