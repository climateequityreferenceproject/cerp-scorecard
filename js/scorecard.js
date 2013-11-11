$(function() {
    // We will override this
    $('#submit').hide();
    
    $("#settings :input").change(update_pledge_controls);
    $("#pledge_controls").change(submit);
    $("#switch_view").click(function() {
        submit("switch_view=yes");
        return false;
    });
    
    $('#loading').hide();
    $('#loading').html('<img src="img/spinner.gif" alt="loading indicator" />');
    
    $('a.def_link, a[target="_self"]').click(
        get_def_by_id
    );

    $('#switch_links a').click(function() {
        $('#switch_view').click();
    });

    $('#popup').hide();
    
    // Equity settings panel
    $("a[id|='cbdr']").click(cbdr_grid_select);
    
    $("#equity_settings_button a").click(function() {
        window.location = window.location.pathname;
    });
    
    $('#dev-low, #dev-med').click(function() {
        $('#equity_progressivity').val(0);
        cbdr_select();
    });
    $('#dev-high').click(function() {
        $('#equity_progressivity').val(1);
        cbdr_select();
    });
    
    $('#r100, #r50c50, #c100').click(cbdr_select);
    
    $('#equity_reset, #equity_reset_top').click(function() {
        $('#equity_progressivity').val(0);
        $('#ambition-high').attr('checked','checked');
        $('#r50c50').attr('checked','checked');
        $('#dev-med').attr('checked','checked');
        $('#d1990').attr('checked','checked');
        cbdr_select();
        // Short-circuit form submission
        return false;
    })

   
});

function cbdr_grid_select() {
    var id_match = /\d+/.exec($(this).attr('id'));
    var id = parseInt(id_match[0]);
    
    var rvsc = Math.floor((id - 1)/3);
    var prog = (id - 1) % 3;
    
    switch (rvsc.toString()) {
        case '0':
            $('#r100').attr('checked',true);
            break;
        case '1':
            $('#r50c50').attr('checked',true);
            break;
        case '2':
            $('#c100').attr('checked',true);
            break;
        default:
            ;
    }

    switch (prog.toString()) {
        case '0':
            $('#dev-low').attr('checked',true);
            break;
        case '1':
            $('#dev-med').attr('checked',true);
            break;
        case '2':
            $('#dev-high').attr('checked',true);
            break;
        default:
            ;
    }
    
    for (var i = 1; i <= 9; i++) {
        var istring = '#cbdr-' + i;
        if (i === id) {
            $(istring).addClass('selected');
        } else {
            $(istring).removeClass('selected');
        }
    }
}

function cbdr_select() {
    switch ($('#equity_settings input[name=r_wt]:checked').attr("id")) {
        case 'r100':
            id = 0;
            break;
        case 'r50c50':
            id = 3;
            break;
        case 'c100':
            id = 6;
            break;
        default:
            id = -10;
    }
    
    switch ($('#equity_settings input[name=dev_thresh]:checked').attr("id")) {
        case 'dev-low':
            id += 1;
            break;
        case 'dev-med':
            id += 2;
            break;
        case 'dev-high':
            id +=3;
            break;
        default:
            id = -10;
    }
    for (var i = 1; i <= 9; i++) {
        var istring = '#cbdr-' + i;
        if (i === id) {
            $(istring).addClass('selected');
        } else {
            $(istring).removeClass('selected');
        }
    }
}

function get_def_by_id(e) {
    href = $(e.currentTarget).attr("href");
    def_id = href.substr(href.lastIndexOf('#') + 1);
    
    $.getJSON('glossary_array.php', {id: def_id}, function(definition){
       $('#popup').html(definition.text).dialog({
            autoOpen: false,
            title: definition.label,
            // The -20 takes care of the border
            width: Math.min(500, screen.width - 20),
            height: Math.min(300, screen.height - 20)
       });
       
       $('#popup').dialog('open');
       
       $('#popup').find('a').each(function() {
            if ($(this).attr('target') == '_self') {
                $(this).addClass('def_link');
                $(this).click(get_def_by_id);
            }
        });
        
    });
    e.preventDefault();
}

function update_pledge_controls() {
    // Update pledge controls
    $.post(
        'pledge_control.php',
        $('#settings').serialize(),
        function(data){
            $('#pledge_controls').html(data);
            submit();
        }
    );
}

function submit(cmds) {
    cmds = cmds || ""; // If nothing is passed for additional commands, then just use an empty string
    if (cmds != "") {
        cmds = "&" + cmds;
    }
    
    $('#loading').show();
    
    // Get new results
    $.post(
        "scorecard_results.php",
        $('#settings').serialize() + cmds + "&ajax=ajax",
        function(data) {
            $('#results').html(data);
            $('#loading').hide();
            $('a.def_link').click(
                get_def_by_id
            );
            $('#switch_links a').click(function() {
                $('#switch_view').click();
            });
            $("#switch_view").click(function() {
                submit("switch_view=yes");
                return false;
            });
    });
}

