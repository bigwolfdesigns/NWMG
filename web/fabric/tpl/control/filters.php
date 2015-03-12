<br />
<div style='width:56%;float:left;padding-top:50px'>
    <a style="float:left" href="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => lc('uri')->get(CLASS_KEY), TASK_KEY => 'add')) ?>">Add a <?php echo $display_table ?></a>
    <form action="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => lc('uri')->get(CLASS_KEY), TASK_KEY => 'manage')) ?>" method="GET" id='edit_form'>
        <h2>Filters:</h2>
        <table style='background-color: antiquewhite;' width="100%" border="0" cellspacing="8" id='edit_table'>
			<?php foreach($_config as $k => $v){ ?>
				<tr>
					<td style="min-width:150px">
						<div align="right" style='float:right'> <?php echo $this->get_filter_operator($v['form']['type']); ?></div>
						<div align="right" style='float:right; padding-right:5px'><?php echo $v['display']; ?></div>
					</td>
					<td>
						<?php
						echo $this->make_filter_field($k, $v['form'], $this->get_form_value($k, isset($v['form']['default'])?$v['form']['default']:''));
						?>

					</td>
				</tr>
			<?php } ?>
            <tr>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <input type="submit" name="filter_submit" value="Filter"/>
                </td>
            </tr>
        </table>
    </form>
</div>