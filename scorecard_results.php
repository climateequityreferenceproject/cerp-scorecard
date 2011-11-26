<?php
    include_once('functions.php');
    
    // TODO: this isn't the best style, to have "bare" variables like this: wrap in a function
    $pathway_id = get_pathways(array('low'=>'IPCC_likely', 'med'=>'AOSIS', 'high'=>'Hansen'));
    $pathway_label = array(
        'low' => 'Low',
        'med' => 'Moderate',
        'high' => 'High'
    );
    $results_default = '<p>Select a country or group to see how its pledge measures up to its <a class="definition" href="#">fair share</a> of the global cost of mitigating climate change.</p>';
    
    function get_results($post_params, $pathway_id, $pathway_label) {
        $params = array();

        $params['min_target_year'] = get_min_target_year($post_params['country'], $post_params['conditional']);
        $params['country_name'] = get_country_name($post_params['country']);
        $params['ambition'] = $pathway_label[array_search($post_params['ambition'], $pathway_id)];
        $ambition = strtolower($params['ambition']);

        $pledge_info = get_pledge_information($post_params['country'], $post_params['conditional'], $params['min_target_year']);
        if (!$pledge_info) {
            return '<div id="summary"><p class="first"><span id="country_name">' . $params['country_name'] . '</span> has not made ' . ($post_params['conditional'] ? 'a conditional' : 'an unconditional') . ' pledge.</p></div>';
        }
        $details = $pledge_info['details'];
        $effort_array = get_gdrs_information($pledge_info, $post_params['ambition']);
        $effort_val = $effort_array['dom_pledge'] + $effort_array['intl_pledge'];
        $effort = number_format($effort_val);
        if ($effort_val < 0) {
            $retval = '<div id="summary">';
            $retval .= '<p class="first"><span id="country_name">' . $params['country_name'] . '</span> ';
            $retval .= 'has pledged emissions in  ' . $params['min_target_year'] . '  that exceed expected, ';
            $retval .= 'business-as-usual, emissions by <span id="commitment">';
            $retval .= number_format(100.0 * $effort_array['pledge_over_bau']) . '%</span></p></div>';
            return $retval;
        }
        foreach ($effort_array as $key => $val) {
            $effort_int[$key] = number_format($val);
        }
        $iso3 = $post_params['country'];
        
        $condition_string = $post_params['conditional'] ? 'conditionally' : 'unconditionally';

        $retval = '<div id="summary">';
        $retval .= '<p class="first"><span id="country_name">' . $params['country_name'] . '</span> ';
        $retval .= 'has pledged ' . $condition_string . ' to do <span id="commitment">' . $effort . '%</span> ';
        $retval .= 'of its <a class="definition" href="#">fair share</a> in ' . $params['min_target_year'] . ', '; 
        $retval .= 'assuming ' . $ambition . ' global ambition.</p></div>';
        
        $retval .= '<div id="graph" class="group">';
        $retval .= '<div id="international" class="international" style="width:' . $effort_int['intl_pledge'] . '%"></div> ';
        $retval .= '<div id="domestic" class="domestic" style="width:' . $effort_int['dom_pledge'] . '%"></div> ';
        $retval .= '<div id="gap" class="gap" style="width:' . $effort_int['gap'] . '%"></div></div><!-- end #graph -->';
        
        $retval .= '<div id="key" class="group">';
        $retval .= '<p><span class="international"></span> ' . $effort_int['intl_pledge'] . '% <a class="definition" href="#">pledged international support</a></p>';
        $retval .= '<p><span class="domestic"></span> ' . $effort_int['dom_pledge'] . '% <a class="definition" href="#">pledged domestic effort</a></p>';
        $retval .= '<p><span class="gap"></span> ' . $effort_int['gap'] . '% <a class="definition" href="#">gap</a></p></div><!-- end #key -->';
$retval .= <<<EOHTML1
            <p id="more_options"><a href="http://gdrights.org/calculator_dev/?iso3=$iso3">more results for this country &#187;</a></p>
EOHTML1;
if ($details !== "") {
$retval .= <<<EOHTML2
    <div id="details">
                <h2>Details about this pledge</h2>
                <p class="first">This pledge assumes: $details</p>
            </div>
EOHTML2;
}

return $retval;
}

if (isset($_POST['ajax']) ) {
     if ($_POST['country']!=='none') {
        echo get_results($_POST, $pathway_id, $pathway_label);
    } else {
        echo $results_default;
    }
}

?>