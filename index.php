<?php
require_once('config.php');
global $CFG, $USER, $DB;

$userid = $USER->id;
$errors = array();
$success = isset($_GET['success']) && $_GET['success'] == 1;

// Handle form submission and backend validation
if (isset($_POST) && !empty($_POST)) {
    if (isset($_POST["survey_submit"])) {
        // Capture submitted inputs
        $q1_val = isset($_POST["question_1"]) ? (int)$_POST["question_1"] : 0;
        $q2_item1 = isset($_POST["q2_item1"]) ? trim($_POST["q2_item1"]) : '';
        $q2_item2 = isset($_POST["q2_item2"]) ? trim($_POST["q2_item2"]) : '';
        $q3_val = isset($_POST["question_3"]) ? (int)$_POST["question_3"] : 0;
        $q4_item1 = isset($_POST["q4_item1"]) ? trim($_POST["q4_item1"]) : '';
        $q4_item2 = isset($_POST["q4_item2"]) ? trim($_POST["q4_item2"]) : '';
        $q5_val = isset($_POST["question_5"]) ? (int)$_POST["question_5"] : 0;
        $q6_item1 = isset($_POST["q6_item1"]) ? trim($_POST["q6_item1"]) : '';
        $q6_item2 = isset($_POST["q6_item2"]) ? trim($_POST["q6_item2"]) : '';

        // Validation for Question 1 (Likert Scale 1-5)
        if ($q1_val < 1 || $q1_val > 5) {
            $errors['question_1'] = true;
        }

        // Validation for Question 2 (Two textboxes: empty check, 3+ repeated characters, IDK spam)
        if ($q2_item1 === '' || preg_match('/(.)\1{2,}/', $q2_item1) || preg_match('/IDK/i', $q2_item1)) {
            $errors['q2_item1'] = true;
        }

        if ($q2_item2 === '' || preg_match('/(.)\1{2,}/', $q2_item2) || preg_match('/IDK/i', $q2_item2)) {
            $errors['q2_item2'] = true;
        }

        // Validation for Question 3 (Likert Scale 1-5)
        if ($q3_val < 1 || $q3_val > 5) {
            $errors['question_3'] = true;
        }

        // Validation for Question 4 (Two textboxes)
        if ($q4_item1 === '' || preg_match('/(.)\1{2,}/', $q4_item1) || preg_match('/IDK/i', $q4_item1)) {
            $errors['q4_item1'] = true;
        }

        if ($q4_item2 === '' || preg_match('/(.)\1{2,}/', $q4_item2) || preg_match('/IDK/i', $q4_item2)) {
            $errors['q4_item2'] = true;
        }

        // Validation for Question 5 (Likert Scale 1-5)
        if ($q5_val < 1 || $q5_val > 5) {
            $errors['question_5'] = true;
        }

        // Validation for Question 6 (Two textboxes)
        if ($q6_item1 === '' || preg_match('/(.)\1{2,}/', $q6_item1) || preg_match('/IDK/i', $q6_item1)) {
            $errors['q6_item1'] = true;
        }

        if ($q6_item2 === '' || preg_match('/(.)\1{2,}/', $q6_item2) || preg_match('/IDK/i', $q6_item2)) {
            $errors['q6_item2'] = true;
        }

        // If no validation errors, proceed with MariaDB insertion
        if (empty($errors)) {
            $survey_answers = array(
                1 => (string)$q1_val,
                2 => json_encode(array($q2_item1, $q2_item2)),
                3 => (string)$q3_val,
                4 => json_encode(array($q4_item1, $q4_item2)),
                5 => (string)$q5_val,
                6 => json_encode(array($q6_item1, $q6_item2))
            );

            foreach ($survey_answers as $key => $ans) {
                $record = new stdClass();
                $record->questionid = $key;
                $record->answer = trim($ans);
                $record->userid = $userid;
                $record->createdat = date("Y-m-d h:i:s");
                $DB->insert_record('custom_user_survey', $record);
            }

            header("Location: index.php?success=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="viewport" content="user-scalable=no, width=device-width" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ExQ - User Dashboard</title>
  <!-- Global stylesheets -->

  <link href="assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
  <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css">
  <link href="assets/css/components.css" rel="stylesheet" type="text/css">
  <link href="assets/css/custom.css" rel="stylesheet" type="text/css">
  <link href="survey.css" rel="stylesheet" type="text/css">

</head>

<body class="navbar-bottom inner_bg">
  <div class="container">
    <!-- Main navbar -->

    <div class="col-md-12 col-sm-12 page-header-bg">

      <div class="col-md-3 col-sm-3">
        <a class="navbar-logo" href="#"><img src="assets/images/ExQLogo.png" alt="" /></a>
      </div>


      <div class="col-md-7 col-sm-7">
        <div class="header-navigaiton">
          <a href="dasboard.html" class="btn btn-link btn-float text-size-small has-text page-header-btn"><i
              class="icon-home font-size-30"></i><span class="pageicon-title active">Home</span></a>
          <a href="exq-profile.html" class="btn btn-link btn-float text-size-small has-text page-header-btn"><i
              class="icon-menu-profile font-size-30 text-header"></i><span class="pageicon-title">My Brain</span></a>
          <a href="road_to_excellence.html" class="btn btn-link btn-float text-size-small has-text page-header-btn"><i
              class="icon-road-to-excellence font-size-30 text-header"></i><span class="pageicon-title">TIPS</span></a>
          <a href="exq-progress.html" class="btn btn-link btn-float text-size-small has-text page-header-btn"><i
              class="icon-menu-progress font-size-30 text-header"></i><span class="pageicon-title">Progress</span></a>
        </div>
      </div>


      <div class="col-md-2 col-sm-2">
        <div class="navbar-collapse collapse" id="navbar-mobile">
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown dropdown-user"> <a class="dropdown-toggle" data-toggle="dropdown"> <img
                  src="assets/images/placeholder.jpg" alt=""> <span class="text-exq profile-name ">SUCHETA KAMATH<i
                    class="icon-logout userprofile-arrow"></i></span> </a>
              <ul class="dropdown-menu dropdown-menu-right">
                <li><a href="#"><i class="ico-user-plus"></i> My Account</a></li>
                <li><a href="#"><i class="ico-cog5"></i> Account settings</a></li>
                <li><a href="#"><i class="ico-switch2"></i> Logout</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>

    </div>

  </div>
  <!-- /main navbar -->
  </div>

  <!-- Page container -->
  <div class="page-container">
    <!-- Page content -->
    <div class="container">
      <!-- Main content -->
      <div class="content-wrapper">
        <!-- Date picker -->
        <div class="panel panel-flat">
          <div class="page-toppanel-bg">
            <h4 class="panel-title"><i class="icon-home position-left text-white font-size-25"></i><span
                class="text-semibold">Welcome to ExQ</span></h4>
          </div>

          <div class="panel-body survey-cont">
            <?php if ($success) { ?>
            <div class="alert alert-success alert-styled-left alert-arrow-left" style="background-color: #f0fdf4; border-color: #22c55e; color: #15803d; margin-bottom: 25px; border-radius: 4px; padding: 15px 20px;">
              <h4 style="margin: 0 0 5px 0; font-weight: 700; font-size: 15px;"><i class="icon-checkmark4 position-left"></i> Survey Submitted!</h4>
              <p style="margin: 0; font-size: 13px;">Your responses have been saved to the database.</p>
            </div>
            <?php } ?>

            <form action="index.php" id="surveyform" method="post">
              <input type="hidden" name="survey_submit" id="survey_submit" value="1"/>

              <div class="row no-margin">
                <div class="col-md-6">
                  <div class="coach-survey">
                    <img src="assets/images/Bearded Man in Orange Sweater.png" alt="Coach">
                    <div class="coach-stand-info">
                      <p>I’m very proud of your work and effort during the EF Benchmark Assessment Process.
                        Take a minute to pause and reflect on the benefits of learning about yourself.</p>
                    </div>
                  </div>
                  <div class="survey-box selfcheck-radio">
                    <h5>1.  <span> How easy was it to navigate and use ExQ during the Benchmark Assessment? </span></h5>
                    <ul>
                      <li> 
                        <div class="self-radio-wpr">
                            <p class="inactive survey-one">
                              <input class="radio-check row2" type="radio" id="q1_1" name="question_1" value="1" row="row2" <?php echo (isset($_POST['question_1']) && $_POST['question_1'] == 1) ? 'checked=""' : ''; ?>>
                              <label for="q1_1"> 1</label>
                            </p>
                            <p class="inactive survey-two">
                              <input class="radio-check row2" type="radio" id="q1_2" name="question_1" value="2" row="row2" <?php echo (isset($_POST['question_1']) && $_POST['question_1'] == 2) ? 'checked=""' : ''; ?>>
                              <label for="q1_2"> 2 </label>
                            </p>
                            <p class="inactive survey-three">
                              <input class="radio-check row2" type="radio" id="q1_3" name="question_1" value="3" row="row2" <?php echo (isset($_POST['question_1']) && $_POST['question_1'] == 3) ? 'checked=""' : ''; ?>>
                              <label for="q1_3"> 3 </label>
                            </p>
                            <p class="inactive survey-four">
                              <input class="radio-check row2" type="radio" id="q1_4" name="question_1" value="4" row="row2" <?php echo (isset($_POST['question_1']) && $_POST['question_1'] == 4) ? 'checked=""' : ''; ?>>
                              <label for="q1_4"> 4 </label>
                            </p>
                            <p class="inactive survey-five">
                              <input class="radio-check row2" type="radio" id="q1_5" name="question_1" value="5" row="row1" <?php echo (isset($_POST['question_1']) && $_POST['question_1'] == 5) ? 'checked=""' : ''; ?>>
                              <label for="q1_5"> 5 </label>
                            </p>

                            <div class="survey-diff-easy">
                            <span class="diff-left">Very Difficult</span>
                            <span class="easy-right">Very Easy</span>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>

                  <div class="survey-box selfcheck-radio">
                    <h5>2.  <span> What were <u>two</u> things that were challenging for you during the ExQ Benchmark Assessment? </span></h5>
                    <div class="survey-textbox">
                      <div class="survey-input-wrap" style="float: left; width: 180px; margin: 0 15px; position: relative;">
                        <input type="text" class="questions <?php echo isset($errors['q2_item1']) ? 'show-border' : ''; ?>" name="q2_item1" id="q2_item1" placeholder="First item..." maxlength="150" value="<?php echo (!$success && isset($_POST['q2_item1'])) ? htmlspecialchars($_POST['q2_item1']) : ''; ?>" style="margin: 0; width: 100%;">
                      </div>
                      <div class="survey-input-wrap" style="float: left; width: 180px; margin: 0 15px; position: relative;">
                        <input type="text" class="questions <?php echo isset($errors['q2_item2']) ? 'show-border' : ''; ?>" name="q2_item2" id="q2_item2" placeholder="Second item..." maxlength="150" value="<?php echo (!$success && isset($_POST['q2_item2'])) ? htmlspecialchars($_POST['q2_item2']) : ''; ?>" style="margin: 0; width: 100%;">
                      </div>
                    </div>
                    <div style="clear: both;"></div>
                  </div>

                  <div class="survey-box selfcheck-radio">
                    <h5>3.  <span> How useful do you think your ExQ Benchmark results will be in helping you understand yourself as a learner?</span></h5>
                    <ul>
                      <li> 
                        <div class="self-radio-wpr">
                            <p class="inactive survey-one">
                              <input class="radio-check row2" type="radio" id="q3_1" name="question_3" value="1" row="row2" <?php echo (isset($_POST['question_3']) && $_POST['question_3'] == 1) ? 'checked=""' : ''; ?>>
                              <label for="q3_1"> 1</label>
                            </p>
                            <p class="inactive survey-two">
                              <input class="radio-check row2" type="radio" id="q3_2" name="question_3" value="2" row="row2" <?php echo (isset($_POST['question_3']) && $_POST['question_3'] == 2) ? 'checked=""' : ''; ?>>
                              <label for="q3_2"> 2 </label>
                            </p>
                            <p class="inactive survey-three">
                              <input class="radio-check row2" type="radio" id="q3_3" name="question_3" value="3" row="row2" <?php echo (isset($_POST['question_3']) && $_POST['question_3'] == 3) ? 'checked=""' : ''; ?>>
                              <label for="q3_3"> 3 </label>
                            </p>
                            <p class="inactive survey-four">
                              <input class="radio-check row2" type="radio" id="q3_4" name="question_3" value="4" row="row2" <?php echo (isset($_POST['question_3']) && $_POST['question_3'] == 4) ? 'checked=""' : ''; ?>>
                              <label for="q3_4"> 4 </label>
                            </p>
                            <p class="inactive survey-five">
                              <input class="radio-check row2" type="radio" id="q3_5" name="question_3" value="5" row="row1" <?php echo (isset($_POST['question_3']) && $_POST['question_3'] == 5) ? 'checked=""' : ''; ?>>
                              <label for="q3_5"> 5 </label>
                            </p>

                            <div class="survey-diff-easy">
                            <span class="diff-left">Not Useful</span>
                            <span class="easy-right">Very Useful</span>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="survey-box selfcheck-radio survey-marg">
                    <h5>4.  <span> What are <u>two</u> things you learned about yourself from completing the ExQ Executive Function Benchmark?</span></h5>
                    <div class="survey-textbox">
                      <div class="survey-input-wrap" style="float: left; width: 180px; margin: 0 15px; position: relative;">
                        <input type="text" class="questions <?php echo isset($errors['q4_item1']) ? 'show-border' : ''; ?>" name="q4_item1" id="q4_item1" placeholder="First item..." maxlength="150" value="<?php echo (!$success && isset($_POST['q4_item1'])) ? htmlspecialchars($_POST['q4_item1']) : ''; ?>" style="margin: 0; width: 100%;">
                      </div>
                      <div class="survey-input-wrap" style="float: left; width: 180px; margin: 0 15px; position: relative;">
                        <input type="text" class="questions <?php echo isset($errors['q4_item2']) ? 'show-border' : ''; ?>" name="q4_item2" id="q4_item2" placeholder="Second item..." maxlength="150" value="<?php echo (!$success && isset($_POST['q4_item2'])) ? htmlspecialchars($_POST['q4_item2']) : ''; ?>" style="margin: 0; width: 100%;">
                      </div>
                    </div>
                    <div style="clear: both;"></div>
                  </div>

                  <div class="survey-box selfcheck-radio">
                    <h5>5.  <span> How likely are you to use what you learned about yourself in school or everyday life?</span></h5>
                    <ul>
                      <li> 
                        <div class="self-radio-wpr">
                            <p class="inactive survey-one">
                              <input class="radio-check row2" type="radio" id="q5_1" name="question_5" value="1" row="row2" <?php echo (isset($_POST['question_5']) && $_POST['question_5'] == 1) ? 'checked=""' : ''; ?>>
                              <label for="q5_1"> 1</label>
                            </p>
                            <p class="inactive survey-two">
                              <input class="radio-check row2" type="radio" id="q5_2" name="question_5" value="2" row="row2" <?php echo (isset($_POST['question_5']) && $_POST['question_5'] == 2) ? 'checked=""' : ''; ?>>
                              <label for="q5_2"> 2 </label>
                            </p>
                            <p class="inactive survey-three">
                              <input class="radio-check row2" type="radio" id="q5_3" name="question_5" value="3" row="row2" <?php echo (isset($_POST['question_5']) && $_POST['question_5'] == 3) ? 'checked=""' : ''; ?>>
                              <label for="q5_3"> 3 </label>
                            </p>
                            <p class="inactive survey-four">
                              <input class="radio-check row2" type="radio" id="q5_4" name="question_5" value="4" row="row2" <?php echo (isset($_POST['question_5']) && $_POST['question_5'] == 4) ? 'checked=""' : ''; ?>>
                              <label for="q5_4"> 4 </label>
                            </p>
                            <p class="inactive survey-five">
                              <input class="radio-check row2" type="radio" id="q5_5" name="question_5" value="5" row="row1" <?php echo (isset($_POST['question_5']) && $_POST['question_5'] == 5) ? 'checked=""' : ''; ?>>
                              <label for="q5_5"> 5 </label>
                            </p>

                            <div class="survey-diff-easy">
                            <span class="diff-left">Very Unlikely</span>
                            <span class="easy-right">Very Likely</span>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>

                  <div class="survey-box selfcheck-radio">
                    <h5>6.  <span> What are <u>two</u> reasons you think other students could benefit from completing the ExQ Benchmark?</span></h5>
                    <div class="survey-textbox">
                      <div class="survey-input-wrap" style="float: left; width: 180px; margin: 0 15px; position: relative;">
                        <input type="text" class="questions <?php echo isset($errors['q6_item1']) ? 'show-border' : ''; ?>" name="q6_item1" id="q6_item1" placeholder="First item..." maxlength="150" value="<?php echo (!$success && isset($_POST['q6_item1'])) ? htmlspecialchars($_POST['q6_item1']) : ''; ?>" style="margin: 0; width: 100%;">
                      </div>
                      <div class="survey-input-wrap" style="float: left; width: 180px; margin: 0 15px; position: relative;">
                        <input type="text" class="questions <?php echo isset($errors['q6_item2']) ? 'show-border' : ''; ?>" name="q6_item2" id="q6_item2" placeholder="Second item..." maxlength="150" value="<?php echo (!$success && isset($_POST['q6_item2'])) ? htmlspecialchars($_POST['q6_item2']) : ''; ?>" style="margin: 0; width: 100%;">
                      </div>
                    </div>
                    <div style="clear: both;"></div>
                  </div>

                  <div class="survey-submit" style="clear: both; margin-top: 30px; margin-bottom: 35px; padding-right: 110px; text-align: right;">
                    <div class="box-error-message" id="warn1" style="<?php echo !empty($errors) ? 'display: inline-block;' : 'display: none;'; ?> position: static; bottom: auto; right: auto; background: #ef5f2d; border: 1px solid #fff; max-width: 320px; padding: 8px 12px; margin-bottom: 12px; text-align: left; border-radius: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                      <p style="margin: 0; color: #fff; font-size: 13px; line-height: 1.35; font-weight: 500;">Oops! Looks like you forgot something. Please answer all the questions.</p>
                    </div>
                    <div style="clear: both;"></div>
                    <button type="submit" name="survey_submit" value="1" style="position: static; float: right;"> Submit</button>
                    <div style="clear: both;"></div>
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- /date picker -->
    </div>
    <!-- /main content -->

    <!-- /page content -->
  </div>
  <!-- /page container -->
  <!-- Footer -->
  <div class="container">
    <div class="navbar navbar-fixed-bottom footer text-center">
      <div id="footer">
        <div class="text-center ">ExQ, LLC &copy; 2025. &nbsp <a href="privacy-policy.html" class="footer_links"
            target="_blank">Privacy Policy</a> &nbsp|&nbsp <a href="terms-of-use.html" class="footer_links"
            target="_blank">Terms of Use</a></div>
      </div>
    </div>
  </div>

  <!-- Core JS files -->
  <script type="text/javascript" data-pace-options='{"ajax": false}'
    src="assets/js/plugins/loaders/pace.min.js"></script>
  <script type="text/javascript" src="assets/js/core/libraries/jquery.min.js"></script>
  <script type="text/javascript" src="assets/js/core/libraries/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/js/plugins/loaders/blockui.min.js"></script>
  <!-- /core JS files -->
  <!-- Theme JS files -->
  <script type="text/javascript" src="assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
  <script type="text/javascript" src="assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
  <script type="text/javascript" src="assets/js/core/libraries/jquery_ui/effects.min.js"></script>
  <script type="text/javascript" src="assets/js/plugins/extensions/mousewheel.min.js"></script>
  <script type="text/javascript" src="assets/js/core/app.js"></script>
  <script type="text/javascript" src="assets/js/pages/jqueryui_forms.js"></script>
  <script type="text/javascript" src="assets/js/plugins/ui/ripple.min.js"></script>
  <script type="text/javascript" src="assets/js/pages/rating.js"></script>
  <!-- /theme JS files -->

</body>

</html>
