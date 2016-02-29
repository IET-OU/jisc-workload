<div class="col-sm-12">
    <a href="/" class="confirm-changes" title="Go back to course overview">Go back to course overview</a>
    <?php
            echo '<br /><a class="confirm-changes" href="/workload/chart/?courseId=' . $course->courseId . '">View workload chart</a>';
    ?>

    <h2>Workload items</h2>
    <?php
        echo '<form name="workload-form" class="ajax-form" action="/workload/save/" method="post" enctype="multipart/form-data" accept-charset="UTF-8">';
        echo '<input type="hidden" name="course-id" value="' . $course->courseId . '" />';
        echo '<input type="hidden" id="deleted-items" name="deleted-items" value="" />';
        echo '<input type="hidden" id="num-rows" name="num-rows" value="" />';
    ?>
    
    <table class="workload-table" id="workload-table">
        <thead>
            <tr>
                <th rowspan="2" class="order">#</th>
                <th rowspan="2" class="item">Item</th>
                <th class="assimilative" colspan="3">Assimilative</th>
                <th rowspan="2" class="FHI popup" data-popup="<b>Finding and handling information</b><br />Use this category to allocate time to activities where students have to find or manipulate information. This can include searching for library articles or case law, engaging with an interactive database, or interpreting charts or tables">FHI<br />(mins)</th>
                <th rowspan="2" class="communication popup" data-popup="<b>Communication</b><br />Use this category to allocate time to activities where the students are asked to communicate with at least one other person, who can be a student, a tutor or someone else. Examples include forum activities such as commenting on each other&rsquo;s posts or discussing a topic">Comm.<br />(mins)</th>
                <th rowspan="2" class="productive popup" data-popup="<b>Productive</b><br />Use this category to allocate time to activities where the students are asked to produce something. This can for instance be a written answer, a diagram, a photograph, a voice recording or something else">Prod.<br />(mins)</th>
                <th rowspan="2" class="experiential popup" data-popup="<b>Experiential</b><br />Use this category to allocate time to activities where the students are learning from direct experience in a real-world situation, such as carrying out an experiment, building a model of something or applying a method in a real-life context">Exper.<br />(mins)</th>
                <th rowspan="2" class="interactive popup" data-popup="<b>Interactive/Adaptive</b><br />Use this category to allocate time to activities where students are asked to engage with a simulated environment. This can include an interactive learning game, a simulation of a phenomenon, or a role play">Int/Adap.<br />(mins)</th>
                <th rowspan="2" class="assessment popup" data-popup="<b>Assessment</b><br />Use this category to allocate time to activities which are directly assessed, either by a tutor, a peer or a computer. Assessment includes both formative and summative assessment.">Assess.<br />(mins)</th>
                <th rowspan="2" class="tuition popup" data-popup="<b>Tuition</b><br />Use this category to allocate time to tuition activities and events where students meet academic staff face-to-face.">Tuition<br />(mins)</th>
                <th rowspan="2" class="total">Total<br />(hours)</th>
                <th rowspan="2" class="actions"></th>
            </tr>
            <tr>
                <th class="assimilative">Word count</th>
                <th class="assimilative">A/V<br />(mins)</th>
                <th class="assimilative">Other<br />(mins)</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $unit = 1;
            $row = 1;
            echo CWorkloadController::printWorkloadSummaryRow($unit);
            $unit = 1;
            foreach($items as $item) {
                if($item->unit != $unit) {
                    echo CWorkloadController::printWorkloadRow(null,$row,$course);
                    echo CWorkloadController::printWorkloadSummaryRow($item->unit);
                    $unit = $item->unit;                    
                }
                echo CWorkloadController::printWorkloadRow($item,$row,$course);
                $row++;                
            }
            echo CWorkloadController::printWorkloadRow(null,$row,$course);
        ?>
        </tbody>
    </table>
    <?php
        echo '<a href="#" style="margin-top:10px;" class="btn btn-primary add-unit">Add a new unit</a>';
        echo '<button class="ajax-button" type="submit" class="btn btn-primary">Save changes</button>';
        echo '</form>';
        echo '<br /><a class="confirm-changes" href="/workload/chart/?courseId=' . $course->courseId . '">View workload chart</a>';
        
    ?>
</div>