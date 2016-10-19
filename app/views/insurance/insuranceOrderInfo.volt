<div class="row-fluid">
    <table class="table">
        <tr>
            <th>用户名</th>
            <td>{{ order_info.user_id }}</td>
            <th>姓名</th>
            <td>{{ order_info.user_name }}</td>
            <th>联系方式</th>
            <td>{{ order_info.phone }}</td>
        </tr>
        <tr>
            <th>订单号</th>
            <td>{{ order_info.order_no }}</td>
            <th>交易号</th>
            <td>{{ order_info.trade_no }}</td>
            <th>交易方式</th>
            <td>
                {% if order_info.pay_type == 'alipay' %}
                支付宝
                {% elseif order_info.pay_type == 'wxpay' %}
                微信支付
                {% elseif order_info.pay_type == 'offline' %}
                线下支付
                {% endif %}
            </td>
        </tr>
        <tr>
            <th>交易时间</th>
            <td>{{ order_info.pay_time | uniform_time }}</td>
            <th>交易金额</th>
            <td>{{ order_info.order_fee }}</td>
            <th>交易状态</th>
            <td>
                {% if order_info.state == 'TRADE_SUCCESS' or order_info.state == 'TRADE_FINISHED' %}
                交易完成
                {% else %}
                待支付
                {% endif %}
            </td>
        </tr>
    </table>
</div>