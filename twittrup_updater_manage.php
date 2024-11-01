<?php 
	
	switch(get_option('twittrUpdated')) {
		case "1.0"	:	break;
		case "1.1"	:	break;
		default		:	$sL = get_option("twittrup-twitterlogin");
						$sP = get_option("twittrup-twitterlogin_encrypted");
						$sP = base64_decode($sP);
						$sP = split(":", $sP);
						$sP = base64_encode($sP[1]);
						
						delete_option("twittrup-twitterlogin");
						delete_option("twittrup-twitterlogin_encrypted");
						add_option("twittrup_accountnr", "1");
			//			add_option("twittrup_list", "1");
						add_option("twittrup-twitterlogin_username_1", $sL);
						add_option("twittrup-twitterlogin_password_1", $sP);
						add_option("twittrUpdated", "1.1");
						break;
	}
	
	wp_enqueue_script("jquery");
	if(get_option('twittrupInitialised') != '1'){
		
		update_option('twittrup-draft-created', '1');
		update_option('twittrup-draft-created-text', __('Writing a new post!','twittrup'));
		update_option('twittrup-draft-created-showlink', '1');
		
		update_option('twittrup-draft-edit', '1');
		update_option('twittrup-draft-edit-text', __('Still writing the post...','twittrup'));
		update_option('twittrup-draft-edit-showlink', '1');
		
		update_option('twittrup-draft-publish', '1');
		update_option('twittrup-draft-publish-text', __('Set a new post online: #title#','twittrup'));
		update_option('twittrup-draft-publish-showlink', '1');
		
		update_option('twittrup-post-created', '1');
		update_option('twittrup-post-created-text', __('Published a new post: #title#','twittrup'));
		update_option('twittrup-post-publish-showlink', '1');
		
		update_option('twittrup-post-edit', '1');
		update_option('twittrup-post-edit-text', __('Editing my post: #title#','twittrup'));
		update_option('twittrup-post-edit-showlink', '1');
		
		update_option('twittrupInitialised', '1');
	}
	

	if($_POST['submit-type'] == 'options'){
	
		if(isset($_POST['twittrup-draft-created'])) {
			update_option('twittrup-draft-created', '1');
		} else {
			delete_option('twittrup-draft-created');
		}
		
		if(isset($_POST['twittrup-draft-edit'])) {
			update_option('twittrup-draft-edit', '1');
		} else {
			delete_option('twittrup-draft-edit');
		}
		
		if(isset($_POST['twittrup-draft-publish'])) {
			update_option('twittrup-draft-publish', '1');
		} else {
			delete_option('twittrup-draft-publish');
		}
		
		if(isset($_POST['twittrup-post-created'])) {
			update_option('twittrup-post-created', '1');
		} else {
			delete_option('twittrup-post-created');
		}
		
		if(isset($_POST['twittrup-post-edit'])) {
			update_option('twittrup-post-edit', '1');
		} else {
			delete_option('twittrup-post-edit');
		}
		
		if(isset($_POST['twittrup-draft-created-text'])) {
			update_option('twittrup-draft-created-text', $_POST['twittrup-draft-created-text']);
		} else {
			delete_option('twittrup-draft-created-text');
		}
		
		if(isset($_POST['twittrup-draft-edit-text'])) {
			update_option('twittrup-draft-edit-text', $_POST['twittrup-draft-edit-text']);
		} else {
			delete_option('twittrup-draft-edit-text');
		}
		
		if(isset($_POST['twittrup-draft-publish-text'])) {
			update_option('twittrup-draft-publish-text', $_POST['twittrup-draft-publish-text']);
		} else {
			delete_option('twittrup-draft-publish-text');
		}
		
		if(isset($_POST['twittrup-post-created-text'])) {
			update_option('twittrup-post-created-text', $_POST['twittrup-post-created-text']);
		} else {
			delete_option('twittrup-post-created-text');
		}
		
		if(isset($_POST['twittrup-post-edit-text'])) {
			update_option('twittrup-post-edit-text', $_POST['twittrup-post-edit-text']);
		} else {
			delete_option('twittrup-post-edit-text');
		}	
	}
	
	if($_POST['submit-type'] == 'service') {
		update_option('twittrup-service', $_POST['twittrup-service']);
	}
	
	function isBase64($string) {
		$sString = $string;
		$sReturn = "";
		if(preg_match('/twittruppass:/', base64_decode($string))) {
			$sReturn = $sString;
		} else {
			$sReturn = base64_encode("twittruppass:".$sString);
		}
		return $sReturn;
	}
	
	
	if ($_POST['submit-type'] == 'login'){
		twittrup_saveaccount();
	}
	
	function twittrup_saveaccount() {
		$iSubCount = 0;
		$iAccount = 0;
		
		for($iCount = 0; $iCount < count($_POST["twittrup-twitterlogin"]); ++$iCount) {
			++$iSubCount;
			$sLogin = $_POST["twittrup-twitterlogin"][$iCount];
			
			$sPW = isBase64($_POST["twittrup-twitterpw"][$iCount]);
			
			$sInsertLogin = "twittrup-twitterlogin_username_".$iSubCount;
			$sInsertPassword = "twittrup-twitterlogin_password_".$iSubCount;
			if(($sLogin != '') && ($sPW != '')) {
				update_option($sInsertLogin, $sLogin);
				update_option($sInsertPassword, $sPW);
				++$iAccount;
			} else {
				echo("<div style='border:1px solid red; padding:20px; margin:20px; color:red;'>"._e('You need to provide your twitter login and password!','twittrup')."</div>");
			}	
		}
		update_option("twittrup_accountnr", $iAccount);
	}
	
	function twittrup_checkCheckbox($sFieldname){
		if( get_option($sFieldname) == '1'){
			echo('checked="true"');
		}
	}
	
	function twittrup_getselected($i) {
		if(get_option('twittrup-service') == $i) {
			echo 'selected="selected"';
		}
	}
	
	if($_POST && $_POST["deleteaccount"] && $_POST["deleteaccount"] == "true") {
		$sUserNew = "twittrup-twitterlogin_username_";
		$sPassNew = "twittrup-twitterlogin_password_";

		$iNr = get_option("twittrup_accountnr");
		$iCount = 0;
		$aAccounts = array();
		
		for($iCount = 1; $iCount < ($iNr+1); ++$iCount) {
			array_push($aAccounts, array(get_option($sUserNew.$iCount), get_option($sPassNew.$iCount)));
		}
		
		$iDelete = $_POST["number"];
		$iDelete = ($iDelete-1);
		$aNewAccount = array();
		$iSubCount = 1;
		for($iCount = 0; $iCount < count($aAccounts); ++$iCount) {
			if($iCount != $iDelete) {
				update_option($sUserNew.($iSubCount), $aAccounts[$iCount][0]);
				update_option($sPassNew.($iSubCount), $aAccounts[$iCount][1]);
				++$iSubCount;
			}
		}
		
		delete_option($sUserNew.$iNr);
		delete_option($sPassNew.$iNr);
		
		update_option("twittrup_accountnr", ($iNr-1));		
	}
	
?>
<style type="text/css">
	fieldset{margin:20px 0; 
	border:1px solid #cecece;
	padding:15px;
	}
</style>
<script type="text/javascript"> 
			/* <![CDATA[*/
			
			var count_twittrup_count = <?php echo get_option("twittrup_accountnr") ?>;
			
		
			function create_twittrup_account() {
			
				// Create Elements
				var twittrup_div = document.createElement("div");
				var twittrup_p1 = document.createElement("p");
				var twittrup_label1 = document.createElement("label");
				var twittrup_inputacc = document.createElement("input");
				var twittrup_acc_desc = document.createTextNode("<?php echo _e('Your email address registered at Twitter:','twittrup');?> ");
				var twittrup_p2 = document.createElement("p");
				var twittrup_label2 = document.createElement("label");
				var twittrup_inputpass = document.createElement("input");
				var twittrup_pw_desc = document.createTextNode("<?php echo _e('Your Twitter password:','twittrup');?> ");
				var twittrup_p3 = document.createElement("p");
				var twittrup_inputrem = document.createElement("input");
				
				count_twittrup_count++;
				
				//div
				twittrup_div.setAttribute('id', "twittrupaccounts_" + (count_twittrup_count+1));
				
				//label
				twittrup_label1.setAttribute('for', "twittrup-twitterlogin_" + (count_twittrup_count+1));
				twittrup_label2.setAttribute('for', "twittrup-twitterpw_" + (count_twittrup_count+1));
				// Elements - Input
				twittrup_inputacc.setAttribute('type', "text");
				twittrup_inputacc.setAttribute('name', "twittrup-twitterlogin[]");
				twittrup_inputacc.setAttribute('id', "twittrup-twitterlogin_" + (count_twittrup_count+1));
				
				twittrup_inputpass.setAttribute('type', "password");
				twittrup_inputpass.setAttribute('name', "twittrup-twitterpw[]")
				twittrup_inputpass.setAttribute('id', "twittrup-twitterpw_" + (count_twittrup_count+1));
				
				twittrup_inputrem.setAttribute('type', "Button");
				twittrup_inputrem.setAttribute('name', "removelogins");
				twittrup_inputrem.setAttribute('value', "I don't use this account");
				twittrup_inputrem.setAttribute('onclick', "remove_twittrup_acc('"+(count_twittrup_count+1)+"');");
				
				
				// Appending To Elements
				twittrup_div.appendChild(twittrup_p1);
				twittrup_p1.appendChild(twittrup_label1);
				twittrup_p1.appendChild(twittrup_acc_desc);
				twittrup_p1.appendChild(twittrup_inputacc);
				
				twittrup_div.appendChild(twittrup_p2);
				twittrup_p2.appendChild(twittrup_label2);
				twittrup_p2.appendChild(twittrup_pw_desc);
				twittrup_p2.appendChild(twittrup_inputpass);
				
				twittrup_div.appendChild(twittrup_p3);
				twittrup_p3.appendChild(twittrup_inputrem);
				
				document.getElementById("twittrup_logins").appendChild(twittrup_div);

			}
			
			function remove_twittrup_acc(id) {
				jQuery(document).ready(function($) {
					$("#twittrupaccounts_"+id).empty();
				});
				document.deleteit.elements["number"].value=id;
				//document.deleteit.elements["updateaccount"].value="true";
				document.deleteit.submit();
				
			}
			
			

			
			/* ]]> */
		</script> 
<div class="wrap">
	<h2><?php echo _e('Your Twitter update options','twittrup');?></h2>

	<form method="post">
	<div>
		<fieldset>
			<legend><?php echo _e('New draft created', 'twittrup'); ?></legend>
			<p>
				<input type="checkbox" name="twittrup-draft-created" id="twittrup-draft-created" value="1" <?php twittrup_checkCheckbox('twittrup-draft-created'); ?> />
				<label for="twittrup-draft-created"><?php echo _e('Update Twitter when a draft is created', 'twittrup'); ?></label>
			</p>
			<p>
				<label for="twittrup-draft-created-text"><?php echo _e('Text for this Twitter update ( use #title# as placeholder for the title )', 'twittrup'); ?></label><br />
				<input type="text" name="twittrup-draft-created-text" id="twittrup-draft-created-text" size="60"maxlength="146" value="<?php echo get_option('twittrup-draft-created-text'); ?>" />
				&nbsp;&nbsp;
				<input type="checkbox" name="twittrup-draft-created-showlink" id="twittrup-draft-created-showlink" value="1" <?php twittrup_checkCheckbox('twittrup-draft-created-showlink')?> />
				<label for="twittrup-draft-created-showlink"><?php echo _e('Link title to blog?','twittrup');?></label>
			</p>
		</fieldset>
		
		<fieldset>
			<legend><?php echo _e('Edit draft', 'twittrup'); ?></legend>
			<p>
				<input type="checkbox" name="twittrup-draft-edit" id="twittrup-draft-edit" value="1" <?php twittrup_checkCheckbox('twittrup-draft-edit'); ?> />
				<label for="twittrup-draft-edit"><?php echo _e('Update Twitter when a draft is edited', 'twittrup'); ?></label>
			</p>
			<p>
				<label for="twittrup-draft-edit-text"><?php echo _e('Text for this Twitter update ( use #title# as placeholder for the title )', 'twittrup'); ?></label><br />
				<input type="text" name="twittrup-draft-edit-text" id="twittrup-draft-edit-text" size="60"maxlength="146" value="<?php echo(get_option('twittrup-draft-edit-text')); ?>" />
				&nbsp;&nbsp;
				<input type="checkbox" name="twittrup-draft-edit-showlink" id="twittrup-draft-edit-showlink" value="1" <?php twittrup_checkCheckbox('twittrup-draft-edit-showlink')?> />
				<label for="twittrup-draft-edit-showlink"><?php echo _e('Link title to blog?','twittrup');?></label>
			</p>
		</fieldset>
		
		<fieldset>
			<legend><?php echo _e('Draft is published', 'twittrup'); ?></legend>
			<p>
				<input type="checkbox" name="twittrup-draft-publish" id="twittrup-draft-publish" value="1" <?php twittrup_checkCheckbox('twittrup-draft-publish'); ?> />
				<label for="twittrup-draft-publish"><?php echo _e('Update Twitter when a draft is published', 'twittrup'); ?></label>
			</p>
			<p>
				<label for="twittrup-draft-publish-text"><?php echo _e('Text for this Twitter update ( use #title# as placeholder for the title )', 'twittrup'); ?></label><br />
				<input type="text" name="twittrup-draft-publish-text" id="twittrup-draft-publish-text" size="60"maxlength="146" value="<?php echo(get_option('twittrup-draft-publish-text')); ?>" />
				&nbsp;&nbsp;
				<input type="checkbox" name="twittrup-draft-publish-showlink" id="twittrup-draft-publish-showlink" value="1" <?php twittrup_checkCheckbox('twittrup-draft-publish-showlink')?> />
				<label for="twittrup-draft-publish-showlink"><?php echo _e('Link title to blog?','twittrup');?></label>
			</p>
		</fieldset>
		
		<fieldset>
			<legend><?php echo _e('New post published', 'twittrup'); ?></legend>
			<p>
				<input type="checkbox" name="twittrup-post-created" id="twittrup-post-created" value="1" <?php twittrup_checkCheckbox('twittrup-post-created'); ?> />
				<label for="twittrup-post-created"><?php echo _e('Update Twitter when a post is published', 'twittrup'); ?></label>
			</p>
			<p>
				<label for="twittrup-post-created-text"><?php echo _e('Text for this Twitter update ( use #title# as placeholder for the title )', 'twittrup'); ?></label><br />
				<input type="text" name="twittrup-post-created-text" id="twittrup-post-created-text" size="60"maxlength="146" value="<?php echo(get_option('twittrup-post-created-text')); ?>" />
				&nbsp;&nbsp;
				<input type="checkbox" name="twittrup-post-publish-showlink" id="twittrup-post-publish-showlink" value="1" <?php twittrup_checkCheckbox('twittrup-post-publish-showlink')?> />
				<label for="twittrup-post-publish-showlink"><?php echo _e('Link title to blog?','twittrup');?></label>
			</p>
		</fieldset>
		
		<fieldset>
			<legend><?php echo _e('Post is edited', 'twittrup'); ?></legend>
			<p>
				<input type="checkbox" name="twittrup-post-edit" id="twittrup-post-edit" value="1" <?php twittrup_checkCheckbox('twittrup-post-edit'); ?> />
				<label for="twittrup-post-edit"><?php echo _e('Update Twitter when a post is edited', 'twittrup'); ?></label>
			</p>
			<p>
				<label for="twittrup-post-edit-text"><?php echo _e('Text for this Twitter update ( use #title# as placeholder for the title )', 'twittrup'); ?></label><br />
				<input type="text" name="twittrup-post-edit-text" id="twittrup-post-edit-text" size="60"maxlength="146" value="<?php echo(get_option('twittrup-post-edit-text')); ?>" />
				&nbsp;&nbsp;
				<input type="checkbox" name="twittrup-post-edit-showlink" id="twittrup-post-edit-showlink" value="1" <?php twittrup_checkCheckbox('twittrup-post-edit-showlink')?> />
				<label for="twittrup-post-edit-showlink"><?php echo _e('Link title to blog?','twittrup');?></label>
			</p>
		</fieldset>
		<input type="hidden" name="submit-type" value="options">
		<input type="submit" name="submit" value="save options" />
	</div>
	</form>
</div>
<div class="wrap">
	<h2><?php echo _e('Select your Shortener Service','twittrup');?></h2>
	<form method="post">
		<fieldset>
			<legend><?php echo _e('Shortener Service','twittrup');?></legend>
			<p>
				<select name="twittrup-service" id="twittrup-service">
					<option value="0" <?php echo twittrup_getselected('0'); ?> >Select one</option>
					<option value="1" <?php echo twittrup_getselected('1'); ?>>Tinyurl.com <?php echo _e('(standard)','twittrup');?></option>
					<option value="2" <?php echo twittrup_getselected('2'); ?>>is.gd</option>
					<option value="3" <?php echo twittrup_getselected('3'); ?>>bit.ly</option>
					<option value="4" <?php echo twittrup_getselected('4'); ?>>snipr.com</option>
					<option value="5" <?php echo twittrup_getselected('4'); ?>>zz.gd</option>
				</select>
		</fieldset>
		<input type="hidden" name="submit-type" value="service">
		<input type="submit" name="submit" value="save service" />
	</form>
</div>
<div class="wrap">
	<h2><?php echo _e('Your Twitter account details','twittrup');?></h2>
	
	<form name="twittrup_accounts" method="post" >
	<div>
		<div id="twittrup_logins">
		<?php 
			$iNr = get_option("twittrup_accountnr");
			for($iCount = 1; $iCount < ($iNr+1); ++$iCount) {
		?>
			<div id="twittrupaccounts_<?php echo $iCount;?>">
			<p>
			<label for="twittrup-twitterlogin<?php echo $iCount;?>"><?php echo _e('Your email address registered at Twitter:','twittrup');?></label>
			<input type="text" name="twittrup-twitterlogin[]" id="twittrup-twitterlogin<?php echo $iCount;?>" value="<?php $sLogin = 'twittrup-twitterlogin_username_'.$iCount; echo get_option($sLogin); ?>" />
			</p>
			<p>
			<label for="twittrup-twitterpw<?php echo $iCount;?>"><?php echo _e('Your Twitter password:','twittrup');?></label>
			<input type="password" name="twittrup-twitterpw[]" id="twittrup-twitterpw<?php echo $iCount;?>" value="<?php $sPW = 'twittrup-twitterlogin_password_'.$iCount; echo get_option($sPW); ?>" />
			</p>
			<?php 
				if($iCount >= 2) {
					?>
					<p><input type="button" name="remove_twittrupacc" value="I don't need this account" onclick="remove_twittrup_acc('<?php echo $iCount; ?>');" /></p>
				<?php
				}
				?></div><?php
			}
		?>
		
		</div>
		<input type="button" name="morelogins"  id="morelogins" value="I've more than one twitter account" onclick="create_twittrup_account();" />
		<input type="hidden" name="submit-type" value="login">
		<p><input type="submit" name="submit" value="save login" />
		</p>
	</div>
	</form>

	<form name="deleteit" method="post">
		<input type="hidden" id="deleteaccount" name="deleteaccount" value="true" />
		<input type="hidden" name="number" value="" />
	</form>
</div>