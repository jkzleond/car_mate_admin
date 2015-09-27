<div class="row-fluid">
    <table class="table">
        <tr>
            <th>用户名</th>
            <td><?php echo $order_info->user_id; ?></td>
            <th>姓名</th>
            <td><?php echo $order_info->user_name; ?></td>
            <th>联系方式</th>
            <td><?php echo $order_info->phone; ?></td>
        </tr>
        <tr>
            <th>订单号</th>
            <td><?php echo $order_info->order_no; ?></td>
            <th>交易号</th>
            <td><?php echo $order_info->trade_no; ?></td>
            <th>交易方式</th>
            <td>
                <?php if ($order_info->pay_type == 'alipay') { ?>
                支付宝
                <?php } elseif ($order_info->pay_type == 'wxpay') { ?>
                微信支付
                <?php } elseif ($order_info->pay_type == 'offline') { ?>
                线下支付
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th>交易时间</th>
            <td><?php echo date('Y-m-d H:i:s', strtotime($order_info->pay_time)); ?></td>
            <th>交易金额</th>
            <td><?php echo $order_info->order_fee; ?></td>
            <th>交易状态</th>
            <td>
                <?php if ($order_info->state == 'TRADE_SUCCESS' || $order_info->state == 'TRADE_FINISHED') { ?>
                交易完成
                <?php } else { ?>
                待支付
                <?php } ?>
            </td>
        </tr>
    </table>
</div>