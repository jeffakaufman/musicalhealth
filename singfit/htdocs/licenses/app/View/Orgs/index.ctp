<div class="orgs index">
	<h2><?php echo __('Licenses'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name', 'Company Name'); ?></th>
			<th><?php echo $this->Paginator->sort('community', 'Community Name'); ?></th>			
			<th><?php echo $this->Paginator->sort('remaining', 'Remaining Licenses'); ?></th>
			<th><?php echo $this->Paginator->sort('expiration', 'License Expires'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($orgs as $org): ?>
	<tr>
		<td><?php echo h($org['Org']['name']); ?>&nbsp;</td>
		<td><?php echo h($org['Org']['community']); ?>&nbsp;</td>		
		<td><?php echo h($org['Org']['remaining']); ?>&nbsp;</td>
		<td><?php echo h($org['Org']['expiration']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $org['Org']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $org['Org']['id']), null, __('Are you sure you want to delete # %s?', $org['Org']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Organization'), array('action' => 'add')); ?></li>
	</ul>
</div>
