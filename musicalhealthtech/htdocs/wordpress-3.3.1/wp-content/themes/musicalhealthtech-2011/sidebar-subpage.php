<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 */
?>

<div id="right_column">
	<div id="secondary" class="widget-area">
		<div id="children">
			<?php
			  if($post->post_parent)
			  $children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");
			  else
			  $children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
			  if ($children) { ?>
			  <ul>
			  <h3>More Information</h3>
			  <?php echo $children; ?>
			  </ul>
			  <?php } ?>
		</div>
	</div>
	<hr />
    <div id="Join">
    	<h3>Sign up to stay connected</h3>
		<form method="post" id="signup" name="signup" action="https://app.e2ma.net/app/view:Join/signupId:1366331/acctId:1363136">
		<input type="hidden" name="member_id" value="" /><input type="hidden" name="prev_member_email" value="" /><input type="hidden" name="signup_name" value="none" />
		<table width="180" border="0" cellspacing="0" cellpadding="0">
			<tr><td valign="top">
				<table width="100%" border="0" cellspacing="2" cellpadding="2">
					<tr><td class="emmaFormLabel"><b>first name</b></td><td class="emmaFormLabel" valign="top"><input name="emma_member_name_first" type="text" /></td></tr>
					<tr><td class="emmaFormLabel"><b>last name</b></td><td class="emmaFormLabel" valign="top"><input name="emma_member_name_last" type="text" /></td></tr>
					<tr><td class="emmaFormLabel"><span class="emmaFormLabelSmall">*</span><b>email</b><br /><span class="emmaFormLabelSmall">required</span></td><td class="emmaFormLabel" valign="top"><input name="emma_member_email" type="text" /></td></tr>
				</table><input type="hidden" name="public_set" value="1" />
			</td></tr>
			<tr><td valign="top">
				<table width="100%" border="0" cellpadding="2" cellspacing="0" class="emmaFormLabel">
					<tr><td>&nbsp;</td></tr>
					<tr class="emmaSpace"><td colspan="2"><b>Select your interests by checking (or un-checking) the options below:</b></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr class="emmaFormLabel"><td width="17"><input type="checkbox" name="groups[]" value="207311669" /></td><td>Clinical Practitioners</td></tr>
					<tr class="emmaFormLabel"><td width="17"><input type="checkbox" name="groups[]" value="207311670" /></td><td>Health Care Administrators</td></tr>
					<tr class="emmaFormLabel"><td width="17"><input type="checkbox" name="groups[]" value="207311668" /></td><td>Parents and Caregivers</td></tr>
					<tr class="emmaFormLabel"><td width="17"><input type="checkbox" name="groups[]" value="207267731" /></td><td>Press</td></tr>
					<tr class="emmaFormLabel"><td width="17"><input type="checkbox" name="groups[]" value="207267711" checked="checked" /></td><td>SingFit Singers</td></tr>
				</table>
			</td></tr>
			<tr><td align="center" valign="top"><input type="submit" name="Submit" class="submit" value="Submit" /></td></tr>
		</table></form>
	</div>
	<div class="clear"></div>
	<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
	<div id="tertiary" class="widget-area">
		<?php dynamic_sidebar( 'sidebar-2' ); ?>
	</div><!-- #tertiary .widget-area -->
</div><!-- #right column -->
<?php endif; ?>