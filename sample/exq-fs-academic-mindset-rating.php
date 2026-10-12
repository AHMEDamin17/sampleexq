<?php
require_once('../config.php');
global $CFG,$USER;
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("ExQ Future-Self Academic Mindset");
$PAGE->set_heading("Blank page");
$PAGE->set_url($CFG->wwwroot.'/cm/exq-fs-academic-midset-rating.php');
$userId = $USER->id;
require_once('header.php');

$userId = $USER->id;
if (isset($_POST) && !empty($_POST)) {
    $mindset_questition=$DB->get_records_sql("SELECT * FROM mdl_custom_academic_mindset ORDER BY orderid");
    $resArray=array();
    foreach($mindset_questition as $question){
        $id=$question->id;
        $resArray[$id]=$_POST["rating_simple$id"];
    }
    $result = $DB->get_record_sql("SELECT id,mindset_rating FROM mdl_custom_user_futureself_completion WHERE userid=$USER->id");
	if(!empty($result)){
        $Record = new stdClass();
        $Record->id = $result->id;
        $Record->mindset_rating = json_encode($resArray);
        //$Record->updatedat = date("Y-m-d h:i:s");
        //die(json_encode($resArray));
        $updated=$DB->update_record('custom_user_futureself_completion', $Record);
        if($updated){
            redirect($CFG->wwwroot . '/cm/index.php');
        }
    }
}
?>
<body class="navbar-bottom">
<?php require_once('navigation_exqflow.php'); ?>
<!-- Page container -->
<div class="page-container"> 
	<!-- Page content -->
	<div class="container">
		<!-- Main content -->
		<div class="content-wrapper">
			<!-- Date picker -->
			<div class="panel panel-flat">
				<div class="page-toppanel-bg">
					<h4 class="panel-title font-change">
						<span class="text-semibold">Part III: My Future-Self</span>
					</h4>
				</div>
				<div class="panel-body">          
					<div class="exq-profile-view-wrapper steps-road my-future-self-new mindset-rating">
						<div class="my-feature-self-wpr">
							<div class="my-feature-coach-right">
								<div class="my-feature-coach-content">
									<p>Student's <span class="emphasized">Academic Mindset </span>attitudes are beliefs that students have 
									about themselves in relation to their schoolwork. Positive mindsets 
									motivate them to persist and help improve their performance. </p>
									<p>How much do these four mindsets mean to you?</p>                        
								</div>
								<div class="road-coach-info fs-coach">
									<figure>
										<img src="<?php echo $CFG->wwwroot;?>/cm/assets/images/choose_coach/sucheta-happy.png" alt="" title="" />
									</figure>
								</div>                    
							</div>
							<div class="my-feature-content-right">
								<div class="my-feature-content">
									<form name="academic_minset" id="academic_minset" method="post" action="<?php echo $CFG->wwwroot; ?>/cm/exq-fs-academic-mindset-rating.php">
										<div class="feature-self-select">
											<div class="new-fea-self">
												<div class="new-fea-self-left">
												<h3>Four Academic Mindsets</h3>													
													<?php 
													$mindsets= $DB->get_records_sql("SELECT * FROM mdl_custom_academic_mindset order by id");
													if(!empty($mindsets)){
														$mindset_count=count($mindsets);
														$no=1;
														foreach($mindsets as $key=>$mindset){
															$mindsetId=$no;
															$mindset=$mindset->mindset; ?>
															<p><span id="<?php echo $key;?>"><?php echo $mindset; ?></span></p>				  
													<?php } 
													} ?>
												</div>
												<div class="new-fea-self-right">
													<h3>How strongly I believe in this mindset</h3>
													<div class="key-list-scroll">
														<ul class="key-select-list" style="font-family: 'Avenir-Book';">
														<?php 
													   $mindsets= $DB->get_records_sql("SELECT * FROM mdl_custom_academic_mindset order by id");
													   if(!empty($mindsets)){
														$mindset_count=count($mindsets);
														$no=1;
														foreach($mindsets as $mindset){?>
															<li>                                                
																<div class="equal-width radio-habit">
																	<div id="sl_<?php echo $mindset->id;?>" class="sl_<?php echo $mindset->id;?> rating_simple">
																		<input name="rating_simple<?php echo $mindset->id;?>" value="" id="rating_simple<?php echo $mindset->id;?>" class="rating_simple_habit" type="hidden" />
																	</div>
																</div>
															</li>
															<?php }
													       }?>
														</ul>
													</div>
												</div>
												<div class="next-btn">
													<p class="err-msg" style="display:none;">Please rate all the above.</p>
													<?php if(in_array($USER->id, $CFG->teacherdemoUsers)){?>
            											<a href="<?php echo $CFG->wwwroot.'/cm/exq-fs-academic-mindset-values.php';?>" class="">NEXT</a>
            										<?php }else{?>
													<button type="button" class="btn btn-primary play_btn legitRipple btn" id="submitmindset" href="javascript:void(0);">NEXT</button>
													<?php }?>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<!-- /date picker -->
    </div>
    <!-- /main content -->
</div>
<!-- /page content -->  
</div>
<!-- /page container -->
<script type="text/javascript" src="assets/js/rating.js"></script>
<script>
$(document).ready(function () {
	<?php for ($x = 1; $x <= 4; $x++) {?>
	$("#rating_simple<?php echo $x; ?>").webwidget_rating_simple({
		rating_star_length: '5',
		rating_initial_value: '',
		rating_function_name: '', //this is function name for click
		directory: 'assets/images/'
		//tooltip: ['', 'This is not helpful', 'This is somewhat helpful', 'This is super helpful']
	});
	<?php } ?>
	$(".err-msg").hide();
	var rate1 =rate2 =rate3 =rate4 = 0;
	$('.rating_simple').each(function(){
		$('.webwidget_rating_simple li').click(function (e) {
			$('.err-msg').hide();
			<?php for ($x = 1; $x <= 4; $x++) {?>
			rate<?php echo $x; ?> = $("#rating_simple<?php echo $x; ?>").val();
			<?php } ?>
		});
    });
	$('#submitmindset').click(function (e) {
		e.preventDefault();
        if(rate1!=0 && rate2!=0 && rate3!=0 && rate4!=0) {
        	$(".err-msg").hide();
            $("#academic_minset").submit();
        } else {
            $(".err-msg").show();
       }
    });
});
</script>
<?php require_once('footer.php'); ?>