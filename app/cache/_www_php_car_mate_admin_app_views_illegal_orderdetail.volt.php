<div class="row-fluid">
    <table class="table" style="margin-bottom:0px;">
        <tr>
            <th colspan="2">订单号</th>
            <td colspan="2"><?php echo $order->order_no; ?></td>
            <th colspan="2">订单时间</th>
            <td colspan="2"><?php echo date('Y-m-d H:i:s', strtotime($order->create_date)); ?></td>
        </tr>
        <tr>
            <th colspan="2">交易号</th>
            <td colspan="2"><?php echo $order->trade_no; ?></td>
            <th colspan="2">交易时间</th>
            <td colspan="2"><?php echo ($order->pay_time ? (date('Y-m-d H:i:s', strtotime($order->pay_time))) : ''); ?></td>
        </tr>
        <tr>
            <th>用户名</th>
            <td><?php echo $order->user_id; ?></td>
            <th>用户姓名</th>
            <td><?php echo $order->user_name; ?></td>
            <th>联系电话</th>
            <td><?php echo $order->phone; ?></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <th>行车证号</th>
            <td><?php echo $order->license_no; ?></td>
            <th>档案号</th>
            <td><?php echo $order->archive_no; ?></td>
            <th>车牌号</th>
            <td><?php echo $order->hphm; ?></td>
            <th>发动机号</th>
            <td><?php echo $order->fdjh; ?></td>
        </tr>
        <tr>
            <th>
                违章条目
            </th>
            <td colspan="7">
                <table class="table" style="margin-bottom:0px;">
                    <tr>
                        <th>违法行为</th>
                        <th>违法扣分</th>
                        <th>处罚金额</th>
                    </tr>
                    <?php foreach ($order->items as $item) { ?>
                    <tr>
                        <td><pre style="text-align:left"><?php echo $item['des']; ?></pre></td>
                        <td><?php echo $item['wfjfs']; ?></td>
                        <td><?php echo $item['fkje']; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th>总计罚款</th>
                        <td colspan="2"><?php echo $order->sum_fkje; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th>支付方式</th>
            <td>
                <?php if ($order->pay_type == 'alipay') { ?>
                支付宝
                <?php } elseif ($order->pay_type == 'wxpay') { ?>
                微信支付
                <?php } ?>
            </td>
            <th>支付金额</th>
            <td><?php echo $order->order_fee; ?></td>
            <th>支付状态</th>
            <td>
                <?php if ($order->pay_state == 'TRADE_SUCCESS' || $order->pay_state == 'TRADE_FINISHED') { ?>
                已支付
                <?php } else { ?>
                未支付
                <?php } ?>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <th>处理结果</th>
            <td colspan="7">
                <?php if ($order->mark == 'PROCESS_SUCCESS') { ?>
                处理完成
                <?php } elseif ($order->mark == 'PROCESS_FAILED') { ?>
                因[<?php echo $order->fail_reason; ?>]而<span style="color:orangered">无法处理</span>
                <?php } else { ?>
                未处理
                <?php } ?>
            </td>
        </tr>
    </table>
</div>