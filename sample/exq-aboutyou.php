<?php
require_once('../config.php');
global $CFG,$USER;
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("ExQ About You");
$PAGE->set_heading("Blank page");
$PAGE->set_url($CFG->wwwroot.'/cm/exq-aboutyou.php');
$CFG->page_url = "about_you";
//$context = get_context_instance (CONTEXT_SYSTEM);
$context = context_system::instance();
$roles = get_user_roles($context, $USER->id, false);
$role = key($roles);
$roleid = $roles[$role]->roleid;
//revisit restriction after completion
$query = $DB->get_record_sql("SELECT * FROM {custom_exqprofileinfo} where userid=$USER->id");
if(!empty($query)&& ($query->aboutyou==1) && !in_array($USER->id, $CFG->teacherdemoUsers)){
    redirect($CFG->wwwroot.'/cm/index.php');
}
$userid=$USER->id;
if(isset($_POST) && !empty($_POST)){
    if($_POST["aboutyou"] == 1){
        //pre($_POST,1);
        $aboutyou_answers=array(
            1=>$_POST["question_1"],
            2=>$_POST["question_2"],
            3=>$_POST["question_3"],
            4=>$_POST["question_4"],
            5=>$_POST["question_5"],
			6=>json_encode(array($_POST["personname"],$_POST["question_6"]))
        );
        
        foreach($aboutyou_answers as $key=>$aboutyou){
            $record = new stdClass();
            $record->questionid= $key;
            $record->answer = trim($aboutyou);
            $record->userid =$USER->id;
            $record->createdat =date("Y-m-d h:i:s");
            $updated = $DB->insert_record('custom_user_aboutyou', $record);
        }
        
        // For Start Training Update
        $tips=$_POST["aboutyou"];
        $userId = $USER->id;
        $query = $DB->get_record_sql("SELECT id,domain_order FROM {custom_exqprofileinfo} where userid=$USER->id");
        $id = $query->id;
        if ($id != '') {
            $record = new stdClass();
            $record->id = $id;
            $record->aboutyou= $tips;
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
::spelling-error {
  text-decoration: wavy red underline;
}
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
                <div class="panel-body envision-main-wrapper  survay-coach">
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
                                	<p>The self-optimization process is a journey! As adults, we lead dynamic lives while being committed to multi-faceted goals. As you reflect on your life's goals, answer these questions as authentically as you can. (Please note, students receive a "student version" of these questions) </p>
                                <?php }else{?>
                                    <p>Learning is a journey, and the learner mindset is critical to your effort in personal growth and the success you will obtain.</p>
									<p>Answer these important questions about your learner mindset and your approach to difficulties thoughtful and clearly. Please write in complete sentences.</p>
                                  <?php }?>
                                </div>
                            </div>
                            <div class="about-you-content">
                                <form action="<?php echo $CFG->wwwroot;?>/cm/exq-aboutyou.php" name="aboutyouform" id="aboutyouform" method ="post">
                                    <?php 
                                    $questions = $DB->get_records_sql("SELECT * FROM {custom_aboutyou_new_exd} order by orderid");
                                   // pre($questions);
								   $q=0;
								   $ques_inp=array(6);
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
                                    ?> 
                                    <div class="about-you-box  <?php echo $multi_line_class;?>">
                                        <h3><?php echo $questions_data;?></h3>
                                        <div class="about-area-wpr">
											<?php if(in_array($question->id,$ques_inp) && $roleid==$CFG->studentrole){ ?>
											<input type="text" class="person-input questions" spellcheck="true" name="personname" id="question_<?php echo $question->id;?>"
                                                placeholder="<?php if($question->hovertext!="" && ($roleid==$CFG->studentrole || $question->id==2)){echo $question->hovertext; }?>"  maxlength="100" minlength="2" />
												
											 <div class="because-block">
                                                <h5><?php echo $question->because;?></h5>
                                                <textarea class="becauseReason questions question_<?php echo $question->id;?>" spellcheck="true" name="question_<?php echo $question->id;?>" id="question_<?php echo $question->id;?>" maxlength="200"
                                                    minlength="2"></textarea>
                                            </div>
											<?php }else{ ?>
                                            <textarea name="question_<?php echo $question->id;?>" spellcheck="true" class="questions question_<?php echo $question->id;?>" placeholder="<?php if($question->hovertext!="" && ($roleid==$CFG->studentrole || $question->id==2)){echo $question->hovertext; }?>" id="question_<?php echo $question->id;?>" maxlength="200" minlength="10"></textarea>
											<?php }?>
											<div class="repeatedword-control" id="question_<?php echo $question->id;?>_repeat"style="display:none;">
												<span>Please enter a real response.</span>
											</div>
                                        </div>
                                    </div>
                                    <?php 
									$q++;
										if($q==6){
											 break;
										}
									}?>
                                    <div class="about-you-box penbox">
                                       <div class="next-btn">
                                        	<?php if(in_array($userid, $CFG->teacherdemoUsers)){?>
                                				<a href="<?php echo $CFG->wwwroot.'/cm/exq-aboutyou-exp.php';?>" class="legitripple">NEXT</a>
                                			<?php }else{?>
                                            <a class="legitripple aboutyousubmit" href="javascript:void(0)" class="aboutyou_submit">NEXT</a>
                                            <?php }?>
                                        </div>  
                                        <div class="box-error-message" id="warn1">
                                            <p>Oops! Looks like you forgot something. Please answer all the questions.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="aboutyou" id="aboutyou" value="0"/> 
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
    $(".aboutyousubmit").click(function(){
    	var res=true;
    	$(".questions").each(function(){
    		var ans=$(this).val();
    		ans=ans.trim();
    		//console.log(ans);
			const regexlargeworddata = /(.)\1{2,}/;
			const founddata = ans.match(regexlargeworddata);
			const founddata1 = ans.match(/IDK/i);
			if(founddata || founddata1){
				$(".repeatedword-control").hide();
		    }
    		if(ans==''){
    			res=false;
    			$(this).addClass("show-border");
    		}else{
    			$(this).removeClass("show-border");
    		}
    	});
    	//console.log(res);
    	if(res==true){
    		$("#warn1").hide();
    		$("#aboutyou").val(1);
    		$("#aboutyouform").submit();
    	}else{
    		$("#warn1").show();
    	}
	});
});
</script>
<?php require_once('footer.php'); ?>