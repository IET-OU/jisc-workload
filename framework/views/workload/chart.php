<div class="col-sm-12">
    <?php
    echo '<a href="/workload/view/?courseId=' . $course->courseId . '" class="confirm-changes" title="Go back to workload view">Go back to workload view</a>';
    echo '<h2>Workload chart</h2>';
        $delta = 700 / $max;
        echo '<div class="chart">';
        foreach($totals as $unit => $total) {
            echo '<div class="row clearfix clearboth">';
            echo '<div class="unit">Unit ' . $unit . '</div>';
            if($total['assimilative'] > 0) {
                $width = $total['assimilative'] * $delta;
                echo '<div class="segment assimilative" style="width:' . $width . 'px">';
                if($width > 15) echo round($total['assimilative'] / 60,1);
                echo '</div>';
            }
            if($total['FHI'] > 0) {
                $width = $total['FHI'] * $delta;
                echo '<div class="segment FHI" style="width:' . $width . 'px">';
                if($width > 15) echo round($total['FHI'] / 60,1);
                echo '</div>';
            }
            if($total['communication'] > 0) {
                $width = $total['communication'] * $delta;
                echo '<div class="segment communication" style="width:' . $width . 'px">';
                if($width > 15) echo round($total['communication'] / 60,1);
                echo '</div>';
            }
            if($total['productive'] > 0) {
                $width = $total['productive'] * $delta;
                echo '<div class="segment productive" style="width:' . $width . 'px">';
                if($width > 15) echo round($total['productive'] / 60,1);
                echo '</div>';
            }
            if($total['experiential'] > 0) {
                $width = $total['experiential'] * $delta;
                echo '<div class="segment experiential" style="width:' . $width . 'px">';
                if($width > 15) echo round($total['experiential'] / 60,1);
                echo '</div>';
            }
            if($total['interactive'] > 0) {
                $width = $total['interactive'] * $delta;
                echo '<div class="segment interactive" style="width:' . $width . 'px">';
                if($width > 15) echo round($total['interactive'] / 60,1);
                echo '</div>';
            }
            if($total['assessment'] > 0) {
                $width = $total['assessment'] * $delta;
                echo '<div class="segment assessment" style="width:' . $width . 'px">';
                if($width > 15) echo round($total['assessment'] / 60,1);
                echo '</div>';
            }
            if($total['tuition'] > 0) {
                $width = $total['tuition'] * $delta;
                echo '<div class="segment tuition" style="width:' . $width . 'px">';
                if($width > 15) echo round($total['tuition'] / 60,1);
                echo '</div>';
            }
            echo '<div class="segment total">[' . round($total['total'] / 60,1) . ']</div>';
            echo '</div>';
        }
        echo '<div class="row legend">';
        echo '<div class="unit">Legend</div>';
        echo '<div class="segment assimilative" style="width:90px">Assimilative</div>';
        echo '<div class="segment FHI" style="width:90px">FHI</div>';
        echo '<div class="segment communication" style="width:100px">Communication</div>';
        echo '<div class="segment productive" style="width:90px">Productive</div>';
        echo '<div class="segment experiential" style="width:90px">Experiential</div>';
        echo '<div class="segment interactive" style="width:90px">Interactive</div>';
        echo '<div class="segment assessment" style="width:90px">Assessment</div>';
        echo '<div class="segment tuition" style="width:90px">Tuition</div>';
        echo '</div>';
        echo '</div>';
    ?>
</div>