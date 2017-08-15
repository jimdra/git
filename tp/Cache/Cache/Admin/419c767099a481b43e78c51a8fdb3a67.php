<?php if (!defined('THINK_PATH')) exit();?><div class="J_tablelist table_list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
                <th align="center"><?php echo L('building_name');?></th>
                <th align="center"><?php echo L('building_level');?></th>
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
                <td align="center"><?php echo ($val["build_name"]); ?></td>
                <td align="center"><?php echo ($val["build_level"]); ?></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
</div>