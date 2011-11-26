<?php
    $intl = $effort_int['intl_pledge'];
    $dom = $$effort_int['dom_pledge'];
    $gap = $effort_int['gap'];
?>

<div id="graph" class="group">';
    <?php draw_graph($intl,'intl',$dom,'dom',$gap); ?>
</div><!-- end #graph -->

