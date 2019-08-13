<?php
$surveyId = !empty($survey->get('slug')) ? $survey->get('slug') : $survey->get('id');

?>
<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="pull-right">
            <div class="btn-group btn-group-sm" role="group">
                <?php if (empty($survey->publish_date)) : ?>
                    <?= $this->Html->link(
                        '<i class="fa fa-plus"></i> ' . __('Add Question'),
                        ['controller' => 'SurveyQuestions', 'action' => 'add', $surveyId],
                        ['class' => 'btn btn-default', 'escape' => false]
                    )?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<table class="table table-hover table-condensed table-vertical-align table-datatable" width="100%">
    <thead>
        <tr>
            <th scope="col"><?= __('Order') ?></th>
            <th scope="col"><?= __('Question') ?></th>
            <th scope="col"><?= __('Type') ?> </th>
            <th scope="col"><?= __('Active') ?></th>
            <th scope="col"><?= __('Required') ?></th>
            <th scope="col" class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($section->get('survey_questions') as $surveyQuestion) : ?>
        <tr>
            <td><?= h($surveyQuestion->order) ?></td>
            <td><?= h($surveyQuestion->question) ?></td>
            <td><?= h($questionTypes[$surveyQuestion->get('type')]) ?> </td>
            <td><?= h($surveyQuestion->active ? __('Yes') : __('No')) ?></td>
            <td><?= h($surveyQuestion->is_required ? __('Yes') : __('No')) ?></td>
            <td class="actions">
                <div class="btn-group btn-group-xs">
                <?php if (empty($survey->publish_date)) : ?>
                    <?= $this->Form->postLink('<i class="fa fa-arrow-up"></i>', ['controller' => 'SurveyQuestions', 'action' => 'move', $surveyQuestion->id, 'up'], ['escape' => false, 'class' => 'btn btn-default']);?>
                    <?= $this->Form->postLink('<i class="fa fa-arrow-down"></i>', ['controller' => 'SurveyQuestions', 'action' => 'move', $surveyQuestion->id, 'down'], ['escape' => false, 'class' => 'btn btn-default']);?>
                <?php endif; ?>
                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['controller' => 'SurveyQuestions', 'action' => 'view', $surveyId, $surveyQuestion->id], ['escape' => false, 'class' => 'btn btn-default', 'title' => __('View')]) ?>
                <?php if (empty($survey->publish_date)) : ?>
                    <?= $this->Html->link('<i class="fa fa-pencil"></i>', ['controller' => 'SurveyQuestions', 'action' => 'edit', $surveyId, $surveyQuestion->id], ['escape' => false, 'class' => 'btn btn-default']) ?>
                    <?= $this->Form->postLink('<i class="fa fa-trash"></i>', ['controller' => 'SurveyQuestions', 'action' => 'delete', $surveyId, $surveyQuestion->id], ['escape' => false, 'class' => 'btn btn-default', 'confirm' => __('Are you sure you want to delete # {0}?', $surveyQuestion->id)]) ?>
                <?php endif;?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
