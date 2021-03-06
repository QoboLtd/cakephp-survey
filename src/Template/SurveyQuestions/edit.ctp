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

$surveyId = empty($survey->slug) ? $survey->id : $survey->slug;

$options['title'] = __d('Qobo/Survey',
    '{0} &raquo; {1} &raquo; Edit Question',
    $this->Html->link(__d('Qobo/Survey', 'Surveys'), ['controller' => 'Surveys', 'action' => 'index']),
    $this->Html->link($survey->get('name'), ['controller' => 'Surveys', 'action' => 'view', $surveyId])
);
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?= $options['title'] ?></h4>
        </div>
    </div>
</section>
<section class="content">
    <?= $this->Form->create($surveyQuestion) ?>
    <?= $this->Form->hidden('survey_id', ['value' => $survey->id]);?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= __d('Qobo/Survey', 'Details');?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?= $this->Form->control('question', ['type' => 'text']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?= $this->Form->textarea('extras', ['class' => 'tinymce']); ?>
                    <?= $this->element('Qobo/Survey.tinymce');?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?= $this->Form->control('active', ['checked' => true]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?= $this->Form->control('is_required', ['checked' => true]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?= $this->Form->control('survey_section_id', ['type' => 'select', 'options' => $sections]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?= $this->Form->control('type', ['options' => $questionTypes]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?= $this->Form->control('order');?>
                </div>
            </div>

        </div>
    </div>
    <?= $this->Form->button(__d('Qobo/Survey', 'Save')) ?>
    <?= $this->Form->end() ?>
</section>
