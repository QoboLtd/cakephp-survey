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
$defaultOptions = [
    'disabled' => true,
    'escape' => false,
];

$options = [
    'type' => 'select',
    'options' => [],
    'value' => [],
    'empty' => __d('Qobo/Survey', '-- Choose Answer --'),
];

$options += $defaultOptions;

$options['options'] = $question->getAnswerOptions(['withScore' => true]);
$questionEntry = $question->getQuestionEntryResultsPerEntry($entryId);

if ($questionEntry) {
    $values = $questionEntry->getSurveyResultValues(['resultField' => 'survey_answer_id']);
    $options['value'] = array_shift($values);
}

$id = md5(serialize($options));
?>
<div class="box no-border">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?php if ($question->get('is_required')) : ?>
                <strong style="color:red;"><?= __d('Qobo/Survey', 'Required:') ?></strong>
            <?php endif; ?>
            <?= $question->get('question') ?>
        </h3>
    </div>

    <div class="box-body">
        <?php if (!empty($question->get('extras'))) : ?>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <?= $this->element('Qobo/Survey.SurveyQuestions/view_extras', ['entity' => $question, 'id' => $id, 'collapsed' => false]); ?>
            </div>
            <hr/>
        </div>
        <?php endif;?>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?= $this->Form->control('', $options); ?>
            </div>
        </div>
    </div>

    <?= $this->element(
        'Qobo/Survey.SurveyEntryQuestions/entry_pass_fail',
        [
            'key' => $key,
            'questionEntry' => $questionEntry,
            'isDisabled' => $isDisabled,
        ]
    )?>
</div>
