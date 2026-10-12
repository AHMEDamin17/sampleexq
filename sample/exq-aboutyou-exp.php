<?php
require_once('../config.php');
global $CFG,$USER;
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("ExQ About You");
$PAGE->set_heading("Blank page");
$PAGE->set_url($CFG->wwwroot.'/cm/exq-aboutyou-exp.php');
$CFG->page_url = "about_you";
//$context = get_context_instance (CONTEXT_SYSTEM);
$context = context_system::instance();
$roles = get_user_roles($context, $USER->id, false);
$role = key($roles);
$roleid = $roles[$role]->roleid;
//revisit restriction after completion
$query = $DB->get_record_sql("SELECT * FROM {custom_exqprofileinfo} WHERE userid = $USER->id");
if(!empty($query)&& ($query->aboutyou_exp==1) && !in_array($USER->id, $CFG->teacherdemoUsers)){
    redirect($CFG->wwwroot.'/cm/index.php');
}
$userid = $USER->id;
if(isset($_POST) && !empty($_POST)){
    if($_POST["aboutyou_exp"] == 1){
        //pre($_POST,1);
        $aboutyou_answers=array(
            //6=>json_encode(array($_POST["rate"],$_POST["question_6"])),
            7=>$_POST["question_7"],
            8=>$_POST["question_8"],
            9=>json_encode(array($_POST["personRating"],$_POST["question_9"])),
            10=>$_POST["question_10"],
			11=>$_POST["question_11"],
            12=>$_POST["question_12"],
            13=>json_encode(array($_POST["helppersonName"],$_POST["question_13"])),
            14=>$_POST["question_14"]
           
        );
        foreach($aboutyou_answers as $key=>$aboutyou){
            $record = new stdClass();
            $record->questionid= $key;
            $record->answer = trim($aboutyou);
            $record->userid = $USER->id;
            $record->createdat = date("Y-m-d h:i:s");
            $updated = $DB->insert_record('custom_user_aboutyou', $record);
		}
		$query = $DB->get_record_sql("SELECT id FROM {custom_exqprofileinfo} WHERE userid = $USER->id");
		$id = $query->id;
        if ($id != '') {
            $record = new stdClass();
            $record->id = $id;
            $record->aboutyou_exp = 1;
            $record->updatedby = $USER->id;
            $record->updatedat = time();
            $updated = $DB->update_record('custom_exqprofileinfo', $record);
            if($updated){
                redirect($CFG->wwwroot.'/cm/index.php');
            }
        }
	}
}
?>
</head>
<style>
.rate-pic-error { 
	box-shadow: 0px 0px 0px 2px #ef5f2d;
    border: none;
	margin-bottom: 2px;
}
.rate-pic-error .awful-text { left: 3px; }
.rate-pic-error .best-text { right: 3px; }
</style>
<?php 
require_once('header.php');
?>
<body class="navbar-bottom">
<!-- Main navbar -->
<?php require_once('navigation_exqflow.php');?>
<!-- /main navbar -->
<!-- Page container -->
<div class="page-container">
    <div class="container">
        <div class="content-wrapper">
            <div class="panel panel-flat">
                <div class="page-toppanel-bg">
                    <h4 class="panel-title font-change"><span class="text-white">Part I: My <?php if($roleid!=$CFG->studentrole){?>Self-<?php }else{?>Student <?php }?>Portrait</span></h4>
                </div>
                <div class="panel-body envision-main-wrapper  survay-coach survay-coach-next">
                    <div class="row">
                        <div class="col-md-3 col-sm-3">
                            <div class="welcome-coach-wrapper aboutyou-coach">
                                <img src="<?php echo $CFG->wwwroot;?>/cm/assets/images/choose_coach/sucheta1.png" alt="" title="" />
                            </div>
                        </div>
                        <div class="col-md-9 col-sm-9 pad-right-20">
                        	<div class="welcome-content-wrapper">
                                <div class="welcome-content-coach">
                                <?php if($roleid!=$CFG->studentrole){?>
                                	<p>The self-optimization process is a journey! As adults, we lead dynamic lives while being committed to multi-faceted goals.</p> 
									<p>Answer these remaining important questions thoughtfully by describing your learning environment, support, and available help.</p>
                                <?php }else{?>
                                    <p>Your school experience is shaped by many learning opportunities, experts, peers, and mentors and the learning journey is made enjoyable and tolerable by those who offer you help along the way.</p>
									<p>Answer these remaining important questions thoughtfully by describing your learning environment, support, and available help.</p>
                                  <?php }?>
                                </div>
                            </div>
                            <div class="about-you-content">
                                <form action="<?php echo $CFG->wwwroot;?>/cm/exq-aboutyou-exp.php" name="aboutyouexpform" id="aboutyouexpform" method ="post">
                                    <?php 
                                    $questions = $DB->get_records_sql("SELECT * FROM {custom_aboutyou_new_exd} where orderid>6 order by orderid ");
                                   // pre($questions);
                                    foreach($questions as $question){
                                        if($roleid!=$CFG->studentrole){
                                            $questions_data=$question->teacher_question;
                                        }else{
                                            $questions_data=$question->question;
                                        }
                                        if(strlen($questions_data)>65){
                                            $multi_line_class="multiline-content";
                                        }else{
                                            $multi_line_class="";
                                        }
										$box_rate = "";
										if($question->id == 9){
											$box_rate = "box-rate";
										}
										$font_size_class="";
										if(strlen($questions_data)>90){
											$font_size_class="style='font-size:14px'";
										}
									?> 
                                    <div class="about-you-box  <?php echo $box_rate;?> <?php echo $multi_line_class;?> next-qs">
                                        <h3 <?php echo $font_size_class;?> ><?php echo $questions_data;?></h3>
										
                                        <div class="about-area-wpr">
											<?php if($question->id == 9){ ?>
                                            <div class="survey-coach ">
                                                <div class="rating-input-wrapper">
                                                    <input type="text" class="rating-input questions " name="personRating" 
                                                        id="personRating" maxlength="2" />
                                                    <span class="out-of-text">out of 10</span>
                                                </div>
                                            </div><br>
                                            <div class="because-block">
                                                <h5><?php echo $question->because;?></h5>
                                                <textarea style="min-height:99px;" name="question_<?php echo $question->id;?>" placeholder="<?php if($question->hovertext!="" && $roleid==$CFG->studentrole){echo $question->hovertext; }?>" class="nexttextareaexd becauseReason questions question_<?php echo $question->id;?>" id="question_<?php echo $question->id;?>" maxlength="200" minlength="10"></textarea>
                                            </div>
											<?php }elseif($question->id == 13 && $roleid==$CFG->studentrole){ ?>
                                            <div class="survey-coach">
                                                <div class="rating-input-wrapper">
                                                    <input type="text" class="person-input questions" name="question_<?php echo $question->id;?>" id="question_<?php echo $question->id;?>" placeholder="<?php if($question->hovertext!="" && $roleid==$CFG->studentrole){echo $question->hovertext; }?>" name="helppersonName" id="helppersonName"
													maxlength="50" minlength="2" />
                                                </div>
                                            </div>
                                            <div class="because-block">
                                                <h5><?php echo $question->because;?></h5>
                                                <textarea name="question_<?php echo $question->id;?>" placeholder="<?php if($question->hovertext!="" && $roleid==$CFG->studentrole){echo $question->hovertext; }?>" class="nexttextareaexd becauseReason questions question_<?php echo $question->id;?>" id="question_<?php echo $question->id;?>" maxlength="200" minlength="10"></textarea>
                                            </div>
											<?php }else{ ?>
											<textarea name="question_<?php echo $question->id;?>" placeholder="<?php if($question->hovertext!="" && $roleid==$CFG->studentrole){echo $question->hovertext; }?>" class="nexttextareaexd questions question_<?php echo $question->id;?>" id="question_<?php echo $question->id;?>" maxlength="200" minlength="10"></textarea>
											<?php }?>
											<div class="repeatedwordexp-control" id="question_<?php echo $question->id;?>_repeat"style="display:none;">
												<span>Please enter valid data.</span>
											</div>
                                        </div>
                                    </div>
                                    <?php }?>
                                    <div class="about-you-box penbox">
                                        <div class="next-btn">
                                        	<?php if(in_array($userid, $CFG->teacherdemoUsers)){?>
                                				<a href="<?php echo $CFG->wwwroot.'/cm/exq-ideas-recommendations.php';?>" class="legitripple">NEXT</a>
                                			<?php }else{?>
                                            <a class="legitripple aboutyousubmit" href="javascript:void(0)" class="aboutyou_submit">NEXT</a>
                                            <?php }?>
                                        </div>  
                                        <div class="box-error-message" id="warn1">
                                            <p>Oops! Looks like you forgot something. Please answer all the questions and Rate your last school year.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="aboutyou_exp" id="aboutyou_exp" value="0"/> 
                                   </form>                                                                                                                                                                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
<!-- /page container -->
<script>
$(document).ready(function(){
	var rate = 0;
	$('.rate-pic span').click(function(){
		$('.rate-pic span').removeClass('active span-rate');
		$(this).addClass('active span-rate');
		rate=document.querySelector(".span-rate").innerHTML;
		$("#rate").val(rate);
		$('.rate-pic').removeClass('rate-pic-error');
		//$("#warn1").hide();
	});
	$('#personRating').on('keypress', function (e) {
		const char = String.fromCharCode(e.which);
		const val = $(this).val();
	    if (!/^\d$/.test(char)) return e.preventDefault();
		if (val === '') return;
		if (val === '1' && char !== '0') return e.preventDefault();
		if (val.length >= 1 && val !== '1') return e.preventDefault();
	});
	 
	$('#personRating').on('input', function () {
		const cleaned = this.value.replace(/\D/g, '');
		const num = parseInt(cleaned, 10);
		this.value = isNaN(num) ? '' : Math.min(num, 10);
	});
 
	$("input").on('change keyup paste', function () {
		var inputdata=$(this).val();
		if($.trim(inputdata)!=""){
			$(this).removeClass('show-border');
			$('#warn1').hide();
			if(inputdata.length!=0){
				if($('.show-border').length > 0){
					$('#warn1').show();
				} else {
					$('#warn1').hide();
				}
            } 
		}else{
			$(this).addClass('show-border');
			$('#warn1').show();
		} 
		var inputboxid=$(this).attr('id');
		const inputdataval =$.trim(inputdata);
		const regexlargewordinput = /(.)\1{2,}/;
		const foundinput = inputdataval.match(regexlargewordinput);
		const foundinput1 = inputdataval.match(/IDK/i);
		var messageText1 = inputboxid + "_repeat";
		if(foundinput || foundinput1){
			$("#"+messageText1).show();
			$(this).addClass("show-border");
			console.log("match");
		}else{
			$(this).removeClass('show-border');
			$("#"+messageText1).hide();
			console.log("no match");
		}	
	});
	$("textarea").on('change keyup paste', function () {
		var text=$(this).val();
		if($.trim(text)!=""){
            $(this).removeClass('show-border');
            if(text.length!=0){
				if($('.show-border').length > 0){
					$('#warn1').show();
				} else {
					$('#warn1').hide();
				}
            } 
		}else{
			$(this).addClass('show-border');
			$('.rate-pic').addClass('rate-pic-error');
			$('#warn1').show();
		}     
		 
		var textboxid=$(this).attr('id');
		const paragraph =$.trim(text);
		const regexlargeword = /(.)\1{2,}/;
		const found = paragraph.match(regexlargeword);
		const found1 = paragraph.match(/IDK/i);
		var messageText = textboxid + "_repeat";
		if(found || found1){
			$("#"+messageText).show();
			$(this).addClass("show-border");
			console.log("match");
		}else{
			$(this).removeClass('show-border');
			$("#"+messageText).hide();
			console.log("no match");
		}	
    });
	var ratedata="";
	
	$(".aboutyousubmit").click(function(){
		ratedata=$("#personRating").val();
		
    	var res=true;
    	$(".questions").each(function(){
    		var ans=$(this).val();
    		ans=ans.trim();
    		//console.log(ans);
			const regexlargeworddata = /(.)\1{2,}/;
			const founddata = ans.match(regexlargeworddata);
			const founddata1 = ans.match(/IDK/i);
			if(founddata || founddata1){
				$(".repeatedwordexp-control").hide();
		    }
			if(ans==''){
    			res=false;
    			$(this).addClass("show-border");
				$('.rate-pic').addClass('rate-pic-error');
    		}else{
    			$(this).removeClass("show-border");
				$('.rate-pic').removeClass('rate-pic-error');
    		}
    	});
		if(parseInt(ratedata)>= 1 && parseInt(ratedata) <= 10 && ratedata!=""){
			$(".rating-input").removeClass("show-border");
		}else{
			$(".rating-input").addClass("show-border");
		}
    	//console.log(res);
    	if(res==true && parseInt(ratedata)>= 1 && parseInt(ratedata) <= 10 && ratedata!=""){
    		$("#warn1").hide();
			$('.rate-pic').removeClass('rate-pic-error');
    		$("#aboutyou_exp").val(1);
    		$("#aboutyouexpform").submit();
    	}else{
    		$("#warn1").show();
			$('.rate-pic').addClass('rate-pic-error');
    	}
	});
});
</script>
<?php require_once('footer.php'); ?>