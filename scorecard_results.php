<?php
    session_start();
    include_once('functions.php');

    $results_default = '<p>Select a country to see how its pledge measures up to its <a class="def_link" href="glossary.php#gloss_fair" target="_blank">fair share</a> of the global cost of mitigating climate change.</p>';
    
    function get_results($post_params) {
        $pathway_ids = GDRsAPI::connection()->pathway_ids;
        $pathway_label = GDRsAPI::connection()->pathway_label;
        $params = array();
        
        $params['min_target_year'] = get_min_target_year($post_params['country'], $post_params['conditional']);
        $params['country_name'] = get_country_name($post_params['country']);
        $params['ambition'] = $pathway_label[array_search($post_params['ambition'], $pathway_ids)];
        $ambition = $params['ambition'];

        $pledge_info = get_pledge_information($post_params['country'], $post_params['conditional'], $params['min_target_year']);
        if (!$pledge_info) {
            return '<div id="summary"><p class="first"><span id="country_name">' . $params['country_name'] . '</span> has not made ' . ($post_params['conditional'] ? 'a conditional' : 'an unconditional') . ' pledge.</p></div>';
        }
        // Remove surrounding spaces and any ending punctuation: we have no control over this text, so clean it up a bit
        $details = clean_text($pledge_info['details']);
        $source_dom = clean_text($pledge_info['source']);
        $source_intl = clean_text($pledge_info['intl_source']);
        
        $effort_array = get_gdrs_information($pledge_info, $post_params['ambition']);
        $effort_val = $effort_array['dom_pledge'] + $effort_array['intl_pledge'];
        $effort = number_format($effort_val);

        $intl = nice_number($effort_array['intl_pledge']);
        $dom = nice_number($effort_array['dom_pledge']);
        $gap = nice_number($effort_array['gap']);
        $pledge_over_bau = nice_number($effort_array['pledge_over_bau']);
        
        $iso3 = $post_params['country'];
        
        $condition_string = $post_params['conditional'] ? 'conditionally' : 'unconditionally';

        $retval = '<div id="summary">';
        $retval .= '<p class="first"><span id="country_name">' . $params['country_name'] . '</span> ';
        $retval .= 'has pledged ' . $condition_string . ' to do <span id="commitment"';
        if ($effort_val < 0) {
            $retval .= ' class="negative"';
        }
        $retval .= '>' . $effort . '%</span> ';
        $retval .= 'of its <a class="def_link" href="glossary.php#gloss_fair" target="_blank">fair share</a> ';
        $retval .= 'in ' . $params['min_target_year'] . ', '; 
        $retval .= 'assuming the ' . $ambition . ' pathway.</p>';
        $retval .= '</div>';
        if ($effort_val < 0) {
            $retval .= '<p>The level of effort is negative because ' . $params['country_name'];
            $retval .= ' has pledged emissions in  ' . $params['min_target_year'] . '  that exceed ';
            $retval .= '<a class="def_link" href="glossary.php#gloss_bau" target="_blank">business-as-usual emissions</a> by ';
            $retval .= $pledge_over_bau . '%.</p>';
        } else {
            $retval .= '<div id="graph" class="group">';
            $retval .= '<img src="img/grid.gif" alt=" " />';
            $retval .= '<p id="fair-share">100% of fair share</p>';
            $retval .= draw_graph($effort_array['intl_pledge'],'intl',$effort_array['dom_pledge'],'dom'); 
            $retval .= '</div><!-- end #graph -->';
        }
        
        $retval .= '<div id="key" class="group">';
        $retval .= '<p><span class="intl"></span> ' . $intl . '% <a class="def_link" href="glossary.php#gloss_intl" target="_blank">pledged international support</a></p>';
        $retval .= '<p><span class="dom"></span> ' . $dom . '% <a class="def_link" href="glossary.php#gloss_dom" target="_blank">pledged domestic effort</a></p>';
        if ($effort_val < 100) {
            $retval .= '<p><span class="gap"></span> ' . $gap . '% <a class="def_link" href="glossary.php#gloss_gap" target="_blank">gap</a></p></div><!-- end #key -->';
        }
        $calc_url = '"' . get_calc_url($iso3) . '"';
$retval .= <<<EOHTML1
            <h2 id="more_options"><a href=$calc_url target="_blank">more results for this country &#187;</a></h2>
    <div id="details">
                <h2>Details about this pledge</h2>
EOHTML1;
        $retval .= '<p class="first">' . $effort_array['pledge_description'];
        // Not reporting these details
/*        if ($details) {
            $retval .= ' This pledge assumes: ' . $details . '.</p>';
        }*/
        if ($source_dom) {
            $retval .= '<p class="source">Source for domestic effort: ' . $source_dom . '.</p>';            
        }
        if ($source_intl) {
            $retval .= '<p class="source">Source for international support: ' . $source_intl . '.</p>';            
        }
        $retval .= '</div>';

return $retval;
}

if (isset($_POST['ajax']) ) {
     if ($_POST['country']!=='none') {
        echo get_results($_POST, $pathway_ids, $pathway_label);
    } else {
        echo $results_default;
    }
}

?>