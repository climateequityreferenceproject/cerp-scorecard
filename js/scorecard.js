$(function() {
    // We will override this
    $('#submit').hide();
    
    $("#settings :input").change(submit);
    
    $('#loading').hide();
    $('#loading').html('<img src="img/spinner.gif" alt="loading indicator" />');
    
    $('a.definition').hover(
        function() {
            href = $(this).attr("href");
            code = href.substr(href.lastIndexOf('#') + 1);
            // TODO: Put the definition into a popup
            $.get('glossary_array.php', {id: code}, function(definition){
                console.log(definition)
            });
        },
        function() {console.log("Leaving");}
    );

});

function submit() {
    $('#loading').show();
    $.post(
        "scorecard_results.php",
        $('#settings').serialize() + "&ajax=ajax",
        function(data) {
            $('#results').html(data);
            $('#loading').hide();
        });
}
