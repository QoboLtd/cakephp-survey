<?php
namespace Qobo\Survey\Test\TestCase\Model\Table;

use Cake\ORM\Association\BelongsTo;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Qobo\Survey\Model\Table\SurveyResultsTable;

/**
 * Qobo\Survey\Model\Table\SurveyResultsTable Test Case
 */
class SurveyResultsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Qobo\Survey\Model\Table\SurveyResultsTable
     */
    public $SurveyResults;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.qobo/survey.survey_results',
        'plugin.qobo/survey.surveys',
        'plugin.qobo/survey.survey_questions',
        'plugin.qobo/survey.survey_answers',
        'plugin.qobo/survey.survey_sections',
        'plugin.qobo/survey.users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Qobo\Survey.SurveyResults') ? [] : ['className' => SurveyResultsTable::class];
        /**
         * @var \Qobo\Survey\Model\Table\SurveyResultsTable $table
         */
        $table = TableRegistry::get('SurveyResults', $config);
        $this->SurveyResults = $table;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SurveyResults);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertInstanceOf(SurveyResultsTable::class, $this->SurveyResults);

        $this->assertEquals('survey_results', $this->SurveyResults->getTable());
        $this->assertEquals('id', $this->SurveyResults->getPrimaryKey());
        $this->assertEquals('id', $this->SurveyResults->getDisplayField());

        $this->assertTrue($this->SurveyResults->hasBehavior('Timestamp'));

        $this->assertInstanceOf(BelongsTo::class, $this->SurveyResults->getAssociation('SurveyAnswers'));
        $this->assertInstanceOf(BelongsTo::class, $this->SurveyResults->getAssociation('SurveyEntries'));
        $this->assertInstanceOf(BelongsTo::class, $this->SurveyResults->getAssociation('SurveyQuestions'));
        $this->assertInstanceOf(BelongsTo::class, $this->SurveyResults->getAssociation('Surveys'));
        $this->assertInstanceOf(BelongsTo::class, $this->SurveyResults->getAssociation('Users'));
    }
}
