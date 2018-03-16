<?php
$options = [];
foreach ($entity->survey_answers as $item) {
    $options[$item->id] = $item->answer;
}
?>
<?= $this->Form->create($entity)?>
<?= $this->Form->hidden('SurveyResults.survey_id', ['value' => $entity->survey_id]);?>
<?= $this->Form->hidden('SurveyResults.survey_question_id', ['value' => $entity->id]);?>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <?= $this->Form->select('SurveyResults.survey_answer_id', $options, ['multiple' => 'checkbox']); ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
    <?= $this->Form->submit(__('Proceed'));?>
    </div>
</div>
<?= $this->Form->end();?>