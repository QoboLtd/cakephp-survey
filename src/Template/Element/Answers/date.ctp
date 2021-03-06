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
echo $this->Html->css('AdminLTE./bower_components/bootstrap-daterangepicker/daterangepicker');
echo $this->Html->script([
    'AdminLTE./bower_components/moment/min/moment.min',
    'AdminLTE./bower_components/bootstrap-daterangepicker/daterangepicker',
    'Qobo/Survey.init',
    ], [
        'block' => 'scriptBottom'
    ]);

$answer = $entity->survey_answers[0];
$key = (isset($key) ? $key . '.' : '');
$id = md5($answer);

$options = [
    'label' => !empty($answer->get('comment')) ? $answer->get('comment') : $entity->get('question'),
    'type' => 'text',
    'class' => 'form-control',
    'data-provide' => 'datetimepicker',
    'data-format' => 'YYYY-MM-DD',
    'required' => true,
    'templates' => [
        'input' => '<div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="{{type}}" name="{{name}}"{{attrs}}/>
        </div>'
    ]
];

echo $this->element('Qobo/Survey.SurveyQuestions/view_extras', ['entity' => $entity, 'id' => $id, 'collapsed' => $collapsed]);
?>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <?= $this->Form->hidden('SurveyResults.' . $key . 'survey_question_id', ['value' => $entity->get('id')]);?>
        <?= $this->Form->hidden('SurveyResults.' . $key . 'survey_answer_id', ['value' => $answer->get('id')]);?>
        <?= $this->Form->control('SurveyResults.' . $key . 'result', $options) ?>
    </div>
</div>
