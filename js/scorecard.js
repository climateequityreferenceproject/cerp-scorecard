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
   
});

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

