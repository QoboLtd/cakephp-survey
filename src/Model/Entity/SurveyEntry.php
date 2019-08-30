<?php
namespace Qobo\Survey\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * SurveyEntry Entity
 *
 * @property string $id
 * @property string $survey_id
 * @property string $user_id
 * @property string|null $status
 * @property int|null $grade
 * @property string|null $context
 * @property string|null $comment
 * @property \Cake\I18n\Time|null $submit_date
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time|null $trashed
 *
 * @property \Qobo\Survey\Model\Entity\Survey $survey
 * @property \Qobo\Survey\Model\Entity\User $user
 */
class SurveyEntry extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * Return array of the resource, that contains it's url and displayfield
     *
     * @return mixed[] $result
     */
    protected function _getResourceUser() : array
    {
        $result = [];
        $table = TableRegistry::getTableLocator()->get($this->get('resource'));

        if ($table instanceof Table) {
            $user = $table->get($this->get('resource_id'));
        }

        $result = [
            'displayField' => $user->get($table->displayField()),
            'url' => [
                'plugin' => false,
                'controller' => $this->get('resource'),
                'action' => 'view',
                $this->get('resource_id')
            ]
        ];

        return $result;
    }

    /**
     * Calculate Total Score of the Survey Entry
     *
     * Calculating only `pass` status survey_results record.
     *
     * @return int|float $result of the score.
     */
    public function getTotalScore()
    {
        $result = 0;

        $results = TableRegistry::getTableLocator()->get('Qobo/Survey.SurveyResults');

        $query = $results->find()
            ->where([
                'submit_id' => $this->get('id')
            ]);

        foreach ($query as $entity) {
            if ($entity->get('status') !== 'fail') {
                $result += $entity->get('score');
            }
        }

        return $result;
    }
}
