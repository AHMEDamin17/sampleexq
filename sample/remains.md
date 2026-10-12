# Preserved Backend & Validation Scripts (`remains.md`)

This file safely preserves all completed PHP backend processing logic, frontend placeholder retrieval blocks, and senior jQuery client-side validation scripts. They are documented here so you can showcase `config.php` as your standalone output, and later restore or plug these scripts back into `index.php`.

---

## 1. Top PHP Backend & Database Handler
This block was placed at the very top of `index.php`. It handles form submission, composite array packing via `json_encode(array(...))`, 12-hour timestamp formatting (`date("Y-m-d h:i:s")`), insertion into `{custom_user_survey}` via `$DB->insert_record()`, and retrieval of the latest survey responses for the placeholder box:

```php
<?php
require_once('config.php');
global $CFG, $USER, $DB;

$userid = $USER->id;
$is_submitted = false;

// Handle form submission
if (isset($_POST) && !empty($_POST)) {
    if (isset($_POST["survey_submit"]) && $_POST["survey_submit"] == 1) {
        //pre($_POST, 1);
        $survey_answers = array(
            1 => isset($_POST["question_1"]) ? $_POST["question_1"] : "1",
            2 => json_encode(array(
                isset($_POST["q2_item1"]) ? trim($_POST["q2_item1"]) : "",
                isset($_POST["q2_item2"]) ? trim($_POST["q2_item2"]) : ""
            )),
            3 => isset($_POST["question_3"]) ? $_POST["question_3"] : "1",
            4 => json_encode(array(
                isset($_POST["q4_item1"]) ? trim($_POST["q4_item1"]) : "",
                isset($_POST["q4_item2"]) ? trim($_POST["q4_item2"]) : ""
            )),
            5 => isset($_POST["question_5"]) ? $_POST["question_5"] : "1",
            6 => json_encode(array(
                isset($_POST["q6_item1"]) ? trim($_POST["q6_item1"]) : "",
                isset($_POST["q6_item2"]) ? trim($_POST["q6_item2"]) : ""
            ))
        );

        foreach ($survey_answers as $key => $ans) {
            $record = new stdClass();
            $record->questionid = $key;
            $record->answer = trim($ans);
            $record->userid = $userid;
            $record->createdat = date("Y-m-d h:i:s");
            $DB->insert_record('custom_user_survey', $record);
        }
        $is_submitted = true;
    }
}

// Retrieve saved survey records for this user (Feature 4 - Frontend Placeholder Box)
$saved_records = $DB->get_records_sql("SELECT * FROM {custom_user_survey} WHERE userid = $userid ORDER BY id DESC LIMIT 6");
$existing_answers = array();
if (!empty($saved_records)) {
    foreach ($saved_records as $row) {
        if (!isset($existing_answers[$row->questionid])) {
            $existing_answers[$row->questionid] = $row->answer;
        }
    }
    ksort($existing_answers);
}

// Unpack composite answers for Question 2, 4, 6
$q2_data = isset($existing_answers[2]) ? json_decode($existing_answers[2], true) : array('', '');
$q4_data = isset($existing_answers[4]) ? json_decode($existing_answers[4], true) : array('', '');
$q6_data = isset($existing_answers[6]) ? json_decode($existing_answers[6], true) : array('', '');
$q1_val = isset($existing_answers[1]) ? (int)$existing_answers[1] : 1;
$q3_val = isset($existing_answers[3]) ? (int)$existing_answers[3] : 1;
$q5_val = isset($existing_answers[5]) ? (int)$existing_answers[5] : 1;
?>
```

---

## 2. Frontend Placeholder Summary Box
This HTML/PHP block was rendered right above `<form action="index.php" id="surveyform">` inside `.panel-body.survey-cont`. It appears whenever `$existing_answers` is populated from the database:

```html
<?php if (!empty($existing_answers)) { ?>
<!-- Placeholder Box (Feature 4 - Saved Data Display) -->
<div class="alert alert-styled-left alert-arrow-left" style="background-color: #f7f6fb; border-color: #43416a; color: #353457; margin-bottom: 25px; border-radius: 4px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
  <h4 style="margin-top: 0; margin-bottom: 12px; font-weight: 700; color: #43416a; font-size: 16px;">
    <i class="icon-checkmark4 position-left" style="color: #50bfa5;"></i> Saved Benchmark Reflections
  </h4>
  <p style="margin-bottom: 14px; font-size: 13px; color: #555;">Your reflection responses have been successfully recorded in the database. Below is a summary of your saved answers:</p>
  <div class="row" style="font-size: 13px;">
    <div class="col-md-6" style="border-right: 1px solid #e5e5e5;">
      <p style="margin-bottom: 8px;"><strong>1. Benchmark Navigation Ease:</strong> <span class="badge" style="background-color: #43416a;"><?php echo htmlspecialchars($existing_answers[1] ?? '1'); ?> / 5</span></p>
      <p style="margin-bottom: 4px;"><strong>2. Challenging Items:</strong></p>
      <ul style="padding-left: 20px; margin-bottom: 12px; color: #444;">
        <li><?php echo htmlspecialchars(!empty($q2_data[0]) ? $q2_data[0] : 'None provided'); ?></li>
        <li><?php echo htmlspecialchars(!empty($q2_data[1]) ? $q2_data[1] : 'None provided'); ?></li>
      </ul>
      <p style="margin-bottom: 8px;"><strong>3. Usefulness as a Learner:</strong> <span class="badge" style="background-color: #43416a;"><?php echo htmlspecialchars($existing_answers[3] ?? '1'); ?> / 5</span></p>
    </div>
    <div class="col-md-6" style="padding-left: 25px;">
      <p style="margin-bottom: 4px;"><strong>4. Learned About Self:</strong></p>
      <ul style="padding-left: 20px; margin-bottom: 12px; color: #444;">
        <li><?php echo htmlspecialchars(!empty($q4_data[0]) ? $q4_data[0] : 'None provided'); ?></li>
        <li><?php echo htmlspecialchars(!empty($q4_data[1]) ? $q4_data[1] : 'None provided'); ?></li>
      </ul>
      <p style="margin-bottom: 8px;"><strong>5. Likelihood to Use in School/Life:</strong> <span class="badge" style="background-color: #43416a;"><?php echo htmlspecialchars($existing_answers[5] ?? '1'); ?> / 5</span></p>
      <p style="margin-bottom: 4px;"><strong>6. Reasons Others Could Benefit:</strong></p>
      <ul style="padding-left: 20px; margin-bottom: 12px; color: #444;">
        <li><?php echo htmlspecialchars(!empty($q6_data[0]) ? $q6_data[0] : 'None provided'); ?></li>
        <li><?php echo htmlspecialchars(!empty($q6_data[1]) ? $q6_data[1] : 'None provided'); ?></li>
      </ul>
    </div>
  </div>
</div>
<!-- /Placeholder Box -->
<?php } ?>
```

---

## 3. Senior Client-Side jQuery Validation Script
This script was placed right before `</body>` in `index.php`. It binds to `change keyup paste` to monitor input fields in real time, flags low-effort inputs via `/(.)\1{2,}/` and `/IDK/i`, toggles `.show-border` and `#warn1`, and intercepts the submit button by toggling the hidden `#survey_submit` input from `0` to `1`:

```html
<!-- Senior jQuery Validation & Form Handler -->
<script type="text/javascript">
$(document).ready(function(){
    $("input[type=text]").on('change keyup paste', function () {
        var inputdata = $(this).val();
        if ($.trim(inputdata) != "") {
            $(this).removeClass('show-border');
            if ($('.show-border').length == 0) {
                $('#warn1').hide();
            }
        } else {
            $(this).addClass('show-border');
            $('#warn1').show();
        } 
        var inputboxid = $(this).attr('id');
        const inputdataval = $.trim(inputdata);
        const regexlargewordinput = /(.)\1{2,}/;
        const foundinput = inputdataval.match(regexlargewordinput);
        const foundinput1 = inputdataval.match(/IDK/i);
        var messageText1 = inputboxid + "_repeat";
        if (foundinput || foundinput1) {
            $("#" + messageText1).show();
            $(this).addClass("show-border");
        } else {
            $("#" + messageText1).hide();
        }		
    });

    $(".survey-submit button").click(function(e){
        e.preventDefault();
        var res = true;
        $(".questions").each(function(){
            var ans = $(this).val();
            ans = ans.trim();
            const regexlargeworddata = /(.)\1{2,}/;
            const founddata = ans.match(regexlargeworddata);
            const founddata1 = ans.match(/IDK/i);
            if (founddata || founddata1) {
                res = false;
                $(this).addClass("show-border");
            } else if (ans == '') {
                res = false;
                $(this).addClass("show-border");
            } else {
                $(this).removeClass("show-border");
            }
        });
        //console.log(res);
        if (res == true) {
            $("#warn1").hide();
            $("#survey_submit").val(1);
            $("#surveyform").submit();
        } else {
            $("#warn1").show();
        }
    });
});
</script>
```
