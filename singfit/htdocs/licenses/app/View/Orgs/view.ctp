<div class="orgs view">
<h2><?php  echo __('Org'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($org['Org']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($org['Org']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Licenses'); ?></dt>
		<dd>
			<?php echo h($org['Org']['licenses']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Expiration'); ?></dt>
		<dd>
			<?php echo h($org['Org']['expiration']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Username'); ?></dt>
		<dd>
			<?php echo h($org['Org']['username']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Password'); ?></dt>
		<dd>
			<?php echo h($org['Org']['password']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Community'); ?></dt>
		<dd>
			<?php echo h($org['Org']['community']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Address'); ?></dt>
		<dd>
			<?php echo h($org['Org']['address']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('City'); ?></dt>
		<dd>
			<?php echo h($org['Org']['city']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('State'); ?></dt>
		<dd>
			<?php echo h($org['Org']['state']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Zip'); ?></dt>
		<dd>
			<?php echo h($org['Org']['zip']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Primary Name'); ?></dt>
		<dd>
			<?php echo h($org['Org']['primary_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Primary Phone'); ?></dt>
		<dd>
			<?php echo h($org['Org']['primary_phone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Primary Email'); ?></dt>
		<dd>
			<?php echo h($org['Org']['primary_email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Tech Name'); ?></dt>
		<dd>
			<?php echo h($org['Org']['tech_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Tech Phone'); ?></dt>
		<dd>
			<?php echo h($org['Org']['tech_phone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Tech Email'); ?></dt>
		<dd>
			<?php echo h($org['Org']['tech_email']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Org'), array('action' => 'edit', $org['Org']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Org'), array('action' => 'delete', $org['Org']['id']), null, __('Are you sure you want to delete # %s?', $org['Org']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Orgs'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Org'), array('action' => 'add')); ?> </li>
	</ul>
</div>
