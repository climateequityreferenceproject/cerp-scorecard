$(function() {
    // We will override this
    $('#submit').hide();
    
    $("#settings :input").change(submit);
    
    $('#loading').hide();
    $('#loading').html('<img src="img/spinner.gif" alt="loading indicator" />');
    
    $('a.def_link').click(
        get_def_by_id
    );
        
   $('#popup').hide();
        
});

function get_def_by_id(e) {
    href = $(e.currentTarget).attr("href");
    def_id = href.substr(href.lastIndexOf('#') + 1);
    
    $.getJSON('glossary_array.php', {id: def_id}, function(definition){
       $('#popup').html(definition.text).dialog({
            autoOpen: false,
            //position: ['right','bottom'],
            //width: 460
            title: definition.label
       });
       
       $('#popup').dialog('open');

    });
    //return false;
    e.preventDefault();
}

function submit() {
    $('#loading').show();
    $.post(
        "scorecard_results.php",
        $('#settings').serialize() + "&ajax=ajax",
        function(data) {
            $('#results').html(data);
            $('#loading').hide();
            $('a.def_link').click(
                get_def_by_id
            );
    });
}

