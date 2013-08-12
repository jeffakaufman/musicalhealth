<div class="orgs form">
<?php echo $this->Form->create('Org', array('name' => 'orgForm')); ?>
	<fieldset>
		<legend><?php echo __('Add Organization'); ?></legend>
		<div class="org_info">
	<?php
		echo $this->Form->input('name', array('label' => 'Company Name'));
		echo $this->Form->input('community', array('label' => 'Community Name'));
		?>
		</div>
				<br />
				<div class="license_info">
				
        <?php
		echo $this->Form->input('username');
		echo $this->Form->input('password', array('type' => 'text', 'class' => 'orgPassword', 'div' => 'passwordBlock'));   
		echo $this->Form->button('Generate Password', array('onClick' => 'GeneratePassword(); return false;'));     
		echo $this->Form->input('licenses');
		echo $this->Form->input('expiration');
		?>
				</div>
		<div class="org_address">
		<?php
		echo $this->Form->input('address');
		echo $this->Form->input('city');
		echo $this->Form->input('state', array('class' => 'state_field'));
		echo $this->Form->input('zip', array('class' => 'zip_field'));
		?>
		</div>
		<br />
				<br />
		<div class="p_contact">
		<?php
		echo $this->Form->input('primary_name', array('label' => 'Primary Contact Name'));
		echo $this->Form->input('primary_phone', array('label' => 'Primary Contact Name'));
		echo $this->Form->input('primary_email', array('label' => 'Primary Contact Email'));
		?>
		</div>
		<div class="t_contact">
		<?php
		echo $this->Form->input('tech_name', array('label' => 'Technical Contact Name'));
		echo $this->Form->input('tech_phone', array('label' => 'Technical Contact Phone'));
		echo $this->Form->input('tech_email', array('label' => 'Technical Contact Email'));
		?>
		</div>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Organizations'), array('action' => 'index')); ?></li>
	</ul>
</div>
