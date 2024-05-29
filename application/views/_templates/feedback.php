<?php

// get the feedback (they are arrays, to make multiple positive/negative messages possible)
$feedback_positive = Session::get('feedback_positive');
$feedback_negative = Session::get('feedback_negative');

// echo out positive messages
if (isset($feedback_positive)) {
    foreach ($feedback_positive as $feedback) {
        echo <<<EOF
<script>
$(document).ready(function () {
    $.gritter.add({
        text: '<div class="alert alert-success alert-block fade in"><button data-dismiss="alert" class="close close-sm gritter-close" type="button"><i class="fa fa-times"></i></button> <strong>Sucesso !</strong> {$feedback}</div>',
        sticky: true
    });
});
</script>
EOF;
        //echo '<div class="feedback success">'.$feedback.'</div>';
    }
}

// echo out negative messages
if (isset($feedback_negative)) {
    foreach ($feedback_negative as $feedback) {
        echo <<<EOF
<script>
$(document).ready(function () {
    $.gritter.add({
        text: '<div class="alert alert-block alert-danger fade in"><button data-dismiss="alert" class="close close-sm gritter-close" type="button"><i class="fa fa-times"></i></button> <strong>Erro</strong> {$feedback}</div>',
        sticky: true
    });
});
</script>
EOF;
        //echo '<div class="feedback error">'.$feedback.'</div>';
    }
}
