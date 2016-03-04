/*!
  Jisc/OU Student Workload Tool.
  License: GPL-3.0+ | Jitse van Ameijde | © 2015 The Open University.
*/

'use strict';

var studySpeeds=[35,70,120];
var defaultStudySpeed = 1;
var unsavedChanges = false;
var globalVars = {};

var ajaxResponseHandler = {
    handleResponse: function(response) {
        if(response.globalVars) {
            $.extend(globalVars,response.globalVars);
        }
        if(response.script) {
            for(var i = 0; i < response.script.length; i++) eval(response.script[i]);
        }
    }
}

function calculateSummary($summaryRow) {
    var totals = {wordcount:0,av:0,other:0,FHI:0,communication:0,productive:0,experiential:0,interactive:0,assessment:0,tuition:0,total:0};
    $row = $summaryRow.next();
    while($row.length == 1 && !$row.hasClass('summary')) {
        var total = 0;

        var wordcount = parseInt($row.find('input.wordcount').val());
        var studySpeed = parseInt($row.find('select').val());
        var av = parseInt($row.find('input.av').val());
        var other = parseInt($row.find('input.other').val());
        var FHI = parseInt($row.find('input.FHI').val());
        var communication = parseInt($row.find('input.communication').val());
        var productive = parseInt($row.find('input.productive').val());
        var experiential = parseInt($row.find('input.experiential').val());
        var interactive = parseInt($row.find('input.interactive').val());
        var assessment = parseInt($row.find('input.assessment').val());
        var tuition = parseInt($row.find('input.tuition').val());

        if(!isNaN(wordcount)) {
            totals.wordcount += wordcount;
            total += wordcount / studySpeeds[studySpeed];
        }
        if(!isNaN(av)) {
            totals.av += av;
            total += av;
        }
        if(!isNaN(other)) {
            totals.other += other;
            total += other;
        }
        if(!isNaN(FHI)) {
            totals.FHI += FHI;
            total += FHI;
        }
        if(!isNaN(communication)) {
            totals.communication += communication;
            total += communication;
        }
        if(!isNaN(productive)) {
            totals.productive += productive;
            total += productive;
        }
        if(!isNaN(experiential)) {
            totals.experiential += experiential;
            total += experiential;
        }
        if(!isNaN(interactive)) {
            totals.interactive += interactive;
            total += interactive;
        }
        if(!isNaN(assessment)) {
            totals.assessment += assessment;
            total += assessment;
        }
        if(!isNaN(tuition)) {
            totals.tuition += tuition;
            total += tuition;
        }
        totals.total += total;
        total = total / 60;
        $row.find('.total').text(total.toFixed(2));
        $row = $row.next();
    }
    $summaryRow.find('.wordcount').text(totals.wordcount);
    $summaryRow.find('.av').text((totals.av / 60).toFixed(2) + ' h');
    $summaryRow.find('.other').text((totals.other / 60).toFixed(2) + ' h');
    $summaryRow.find('.FHI').text((totals.FHI / 60).toFixed(2) + ' h');
    $summaryRow.find('.communication').text((totals.communication / 60).toFixed(2) + ' h');
    $summaryRow.find('.productive').text((totals.productive / 60).toFixed(2) + ' h');
    $summaryRow.find('.experiential').text((totals.experiential / 60).toFixed(2) + ' h');
    $summaryRow.find('.interactive').text((totals.interactive / 60).toFixed(2) + ' h');
    $summaryRow.find('.assessment').text((totals.assessment / 60).toFixed(2) + ' h');
    $summaryRow.find('.tuition').text((totals.tuition / 60).toFixed(2) + ' h');
    $summaryRow.find('.total').text((totals.total / 60).toFixed(2) + ' h');
}

function processWorkloadTable() {
    var row = 1;
    var unit = 0;
    $('#workload-table tbody tr').each(function(e) {
        if(!$(this).hasClass('summary') && !$(this).next().hasClass('summary') && !$(this).next().length == 0) {
            $(this).find('input.item-id').attr('name','row-' + row + '-item-id');
            $(this).find('input.unit').attr('name','row-' + row + '-unit').val(unit);
            $(this).find('input.title').attr('name','row-' + row + '-title');
            $(this).find('input.wordcount').attr('name','row-' + row + '-wordcount');
            $(this).find('select.wpm').attr('name','row-' + row + '-wpm');
            $(this).find('input.av').attr('name','row-' + row + '-av');
            $(this).find('input.other').attr('name','row-' + row + '-other');
            $(this).find('input.FHI').attr('name','row-' + row + '-FHI');
            $(this).find('input.communication').attr('name','row-' + row + '-communication');
            $(this).find('input.productive').attr('name','row-' + row + '-productive');
            $(this).find('input.experiential').attr('name','row-' + row + '-experiential');
            $(this).find('input.interactive').attr('name','row-' + row + '-interactive');
            $(this).find('input.assessment').attr('name','row-' + row + '-assessment');
            $(this).find('input.tuition').attr('name','row-' + row + '-tuition');
            row++;
        }
        if($(this).hasClass('summary')) unit++;
    });
    $('#num-rows').val('' + (row - 1));
}
$(document).ready(function(e) {
        $('tr.summary').each(function(e) {
            calculateSummary($(this));
        });

        $('a.confirm-changes').on('click',function(e) {
            var result = true;
            if(unsavedChanges == true) result = confirm('You have unsaved changes. Click cancel to stay on the page or OK to continue without saving your changes');
            if(result == true) window.location.href = $(this).attr('href');
            else return false;
        });

        $('body').on('mouseover', '.popup',function(e) {
            var offset = $(this).offset();
            var width = $(this).width();
            var height = $('th.FHI').height();
            if($(document).scrollTop() < $('.workload-table').offset().top) height += $('.workload-table').offset().top - $(document).scrollTop();
            $('<div class="popup-window" style="padding: 5px; border: 1px solid black; background-color:#dddddd; width: 300px; position:fixed; top:' + (height + 12) + 'px; left:' + (offset.left) + 'px;"></div>').append($(this).attr('data-popup')).appendTo('body');
        });
        $('body').on('mouseout', '.popup',function(e) {
            $('.popup-window').remove();
        });

        $fixedTable = $('<div style="display:none; overflow:hidden;position:fixed;"><table class="fixed-table"></table></div>');
        $fixedTable.appendTo('body').find('table').append($('.workload-table thead').clone());
        $fixedTable.find('table').append($('.workload-row').eq(0).clone());
        $fixedTable.find('table').css({width:$('.workload-table').outerWidth() + 'px'});

        $('body').on('keyup','.autocomplete',function(e) {
            var target = $(this).attr('data-target');
            var source = $(this).attr('data-source');
            var input = $(this).val().toUpperCase();
            var $input = $(this);
            var id = $(this).attr('id');
            var $popup = $('#' + id + '-popup');
            $popup.empty();
            if($popup.length == 0) {
                $popup = $('<div id="' + id + '-popup" class="select-popup"></div>').appendTo('body');
            }
            if(input == '') {
                $popup.remove();
                return;
            }
            var list = [];
            for(var i in globalVars[source]) {
                if(globalVars[source][i].text.toUpperCase().indexOf(input) != -1) {
                    list.push(globalVars[source][i]);
                }
                if(globalVars[source][i].text.toUpperCase() == input) {
                    $(target).val(globalVars[source][i].value);
                }
            }
            if(list.length > 0 && list.length < 10) {
                for(var i in list) {
                    $('<div class="item" data-value="' + list[i].value + '"></div>').text(list[i].text).appendTo($popup);
                }
                var offset = $(this).offset();
                var width = $(this).outerWidth();
                var height = $(this).outerHeight();
                $popup.css({top:offset.top + height,left:offset.left,width:width});
                $popup.off('click').one('click','.item',function(e) {
                    $input.val('');
                    $popup.remove();
                    var id = $(this).attr('data-value');
                    if($('#collaborator-' + id).length == 0) {
                        $input.parent().append('<div class="collaborator" id="collaborator-' + id + '"><span class="name">' + $(this).text() + '</span> <a href="#" class="remove-collaborator" data-value="' + id + '">x</a></div>');
                        var elements = $(target).val();
                        if(elements == '') $(target).val('' + id);
                        else $(target).val(elements + ',' + id);
                    }
                });
                $popup.one('clickoutside',function() {
                   $popup.remove();
                });
            }
        });

        $('body').on('click','.remove-collaborator',function(e) {
            var id = $(this).attr('data-value');
            $(this).parent().remove();
            var collaborators = $('#collaborators').val().split(',');
            var first = true;
            var newCollaborators = '';
            for(var i in collaborators) {
                if(collaborators[i] != id) {
                    if(first == false) newCollaborators += ',';
                    newCollaborators += collaborators[i];
                    first = false;
                }
            }
            $('#collaborators').val(newCollaborators);
            return false;
        });


        //Add an event listener for dragging rows around
        $('body').on('mousedown','.drag-handle', function(e) {
            var prevY = e.pageY;
            var $tr = $(this).closest('tr');
            //We don't allow moving the last item in a week or the last item in the table (which is always an empty row)
            if(!$tr.next().hasClass('summary') && !$tr.is(':last-child')) {
                //Highlight the row so that it's clear it's being dragged
                $tr.addClass('highlight');
                //Prevent the pesky browser from starting a select operation
                document.onselectstart = function () { return false; }
                //We need to add an event listener for when the mouse is moved
                $(document).bind('mousemove.dragndrop', function(e) {
                    //Get the row element associated with the current mouse position
                    var $target = $(e.target).closest('tr');
                    //Make sure the target element and the dragging element aren't the same one
                    if($target[0] != $tr[0] && ($target.hasClass('workload-row') || $target.hasClass('summary'))) {
                        if($target.length == 1) {
                            //If the mouse is moving upwards, we want to insert the item before the item under the mouse position
                            if(e.pageY < prevY) {
                                if(!$target.is(':first-child') && !$target.hasClass('summary')) {
                                    $tr.insertBefore($target);
                                    unsavedChanges = true;
                                }
                            }
                            //Otherwise we want to move it underneath the item under the mouse position
                            else {
                                if(!$target.is(':last-child') && !$target.next().hasClass('summary')) {
                                    $tr.insertAfter($target);
                                    unsavedChanges = true;
                                }
                            }
                        }
                    }
                    prevY = e.pageY;
                });
                //Add an event listener for the mouse button release
                $(document).bind('mouseup.dragndrop',function(e) {
                    $(document).unbind('mousemove.dragndrop');
                    $tr.removeClass('highlight');
                    processWorkloadTable();
                    $('.summary').each(function(e) {
                        calculateSummary($(this));
                    });
                    //Let the browser resume its own business when a user starts a selection
                    document.onselectstart = function () { return true; }
                });
            }
        });


        $(document).on('scroll',function(e) {
            if($('.workload-table').length == 0) return true;
            var offset = $('.workload-table').offset();
            if($(document).scrollTop() > offset.top) {
                if(!$fixedTable.is(':visible')) {
                    var $rows = $('.workload-table thead tr');
                    var height = $rows.eq(0).height() + $rows.eq(1).height() + 1;
                    $fixedTable.css({left:offset.left,top:0,height:height}).show();
                }
            }
            else {
                $fixedTable.hide();
            }
        });


        $('body').on('click','.confirm',function(e) {
            var result = confirm($(this).attr('data-confirm'));
            if(result == false) {
                e.preventDefault();
                return false;
            }
            return true;
        });

        $('body').on('click','.ajax-form button[type="submit"]',function(e) {
            var $form = $(this).closest('form');
            var href = $form.attr('action');
            var $button = $(this);
            $.post(href,$form.serialize(),function(response) {
                ajaxResponseHandler.handleResponse(response);
            },'json').error(function() {
                alert('Error making AJAX server request');
            });
            return false;
        });


        $('body').on('change','#workload-table input,#workload-table select',function(e) {
            unsavedChanges = true;
            var $row = $(this).closest('tr');
            if(!$(this).hasClass('title')) {
                var $summaryRow = $row.prev();
                while(!$summaryRow.hasClass('summary')) $summaryRow = $summaryRow.prev();
                calculateSummary($summaryRow);
            }
            if($row.next().hasClass('summary') || $row.next().length == 0) {
                var $newRow = $row.clone();
                $newRow.find('input').val('');
                $newRow.find('.total').text('');
                $newRow.find('input,select').attr('name','temp');
                $newRow.insertAfter($row);
                processWorkloadTable();
            }
        });
        $('body').on('click','.remove-row',function(e) {
            var $row = $(this).closest('tr');
            if($row.next().length > 0 && !$row.next().hasClass('summary')) {
                var itemId = $row.find('.item-id').val();
                if(itemId != '') {
                    if($('#deleted-items').val() == '') $('#deleted-items').val(itemId);
                    else $('#deleted-items').val($('#deleted-items').val() + ',' + itemId);
                }
                $summaryRow = $row.prev();
                while(!$summaryRow.hasClass('summary')) $summaryRow = $summaryRow.prev();
                $row.remove();
                processWorkloadTable();
                calculateSummary($summaryRow);
                unsavedChanges = true;
            }
            return false;
        });
        $('body').on('click','.insert-row',function(e) {
            var $row = $(this).closest('tr');
            var $newRow = $row.clone();
            $newRow.find('input').val('');
            $newRow.find('.total').text('');
            $newRow.insertAfter($row);
            processWorkloadTable();
            unsavedChanges = true;
            return false;
        });

        $('body').on('click','.add-unit',function(e) {
            var $rows = $('#workload-table').find('tr.summary');
            var $newRow = $('#workload-table tbody tr:last').clone();
            $newRow.find('input').val('');
            var unit = $rows.length + 1;
            var $newSummaryRow = $rows.eq(0).clone();
            $newSummaryRow.find('.unit-title').text('Unit ' + unit);
            $('#workload-table tbody').append($newSummaryRow);
            $('#workload-table tbody').append($newRow);
            unsavedChanges = true;
            return false;
        });
});
