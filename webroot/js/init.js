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
(function ($) {
    $('[data-provide="datetimepicker"]').each(function () {
        var options = {
            singleDatePicker: true,
            showDropdowns: true,
            timePicker: true,
            drops: 'down',
            timePicker24Hour: true,
            timePickerIncrement: 5,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD HH:mm:ss',
                firstDay: 1
            }
        };

        if ($(this).data('format')) {
            options.locale.format = $(this).data('format');
            if ('YYYY-MM-DD' == options.locale.format) {
                options.timePicker = false;
            }
        }

        var defaultValue = $(this).data('default-value');
        if (!this.value && undefined !== defaultValue) {
            options.startDate = moment().format(defaultValue);
        } else {
            options.autoUpdateInput = false;
        }

        // date range picker (used for datetime fields)
        $(this).daterangepicker(options);

        $(this).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format(options.locale.format));
        });

        $(this).on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
    });

    $('.survey-question-results').each(function () {
        var that = this;
        $.ajax({
            dataType: 'json',
            method: 'POST',
            url: '/api/v1.0/surveys/view-results/',
            data: {
                survey_question_id: $(this).data('id'),
                survey_id: $(this).data('survey-id')
            },
            headers: {
                Authorization: 'Bearer ' + apiToken
            }
        }).then(function (response) {

            if (!response.success) {
                return;
            }

            let questionId = $(that).data('id');
            let graphContainerId = 'graph-' + questionId;
            let graphData = [];

            for (var answerId in response.data.answers) {
                let filter = 'li[data-answer-id="' + answerId + '"]';

                if (['textarea', 'input'].includes(response.data.question.type)) {
                    filter = 'li:first';
                }

                let element = response.data.answers[answerId];
                $(that).find(filter).find('.answer-stats').text(element.results);
            }

            if (['textarea', 'input'].includes(response.data.question.type)) {
                return;
            }

            for (var answerId in response.data.answers) {
                var element = response.data.answers[answerId];
                graphData.push({
                    y: element.entity.answer,
                    x: parseInt(element.results)
                });
            }

            if (window.Morris !== undefined) {
                Morris.Bar({
                    "element": graphContainerId,
                    "data": graphData,
                    xkey: 'y',
                    ykeys: ['x'],
                    labels: ['Count']
                });
            }
        });
    });
})(jQuery);
