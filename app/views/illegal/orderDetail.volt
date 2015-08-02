<div class="row-fluid">
    <table class="table" style="margin-bottom:0px;">
        <tr>
            <th colspan="2">订单号</th>
            <td colspan="2">{{ order.order_no }}</td>
            <th colspan="2">订单时间</th>
            <td colspan="2">{{ order.create_date | uniform_time }}</td>
        </tr>
        <tr>
            <th colspan="2">交易号</th>
            <td colspan="2">{{ order.trade_no }}</td>
            <th colspan="2">交易时间</th>
            <td colspan="2">{{ order.pay_time ? (order.pay_time | uniform_time) : '' }}</td>
        </tr>
        <tr>
            <th>用户名</th>
            <td>{{ order.user_id }}</td>
            <th>用户姓名</th>
            <td>{{ order.user_name }}</td>
            <th>联系电话</th>
            <td>{{ order.phone }}</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <th>行车证号</th>
            <td>{{ order.license_no }}</td>
            <th>档案号</th>
            <td>{{ order.archive_no }}</td>
            <th>车牌号</th>
            <td>{{ order.hphm }}</td>
            <th>发动机号</th>
            <td>{{ order.fdjh }}</td>
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
                    {% for item in order.items %}
                    <tr>
                        <td><pre style="text-align:left">{{ item['des'] }}</pre></td>
                        <td>{{ item['wfjfs'] }}</td>
                        <td>{{ item['fkje'] }}</td>
                    </tr>
                    {% endfor %}
                    <tr>
                        <th>总计罚款</th>
                        <td colspan="2">{{ order.sum_fkje }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th>支付方式</th>
            <td>
                {% if order.pay_type == 'alipay' %}
                支付宝
                {% elseif order.pay_type == 'wxpay' %}
                微信支付
                {% endif %}
            </td>
            <th>支付金额</th>
            <td>{{ order.order_fee }}</td>
            <th>支付状态</th>
            <td>
                {% if order.pay_state == 'TRADE_SUCCESS' or order.pay_state == 'TRADE_FINISHED' %}
                已支付
                {% else %}
                未支付
                {% endif %}
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <th>处理结果</th>
            <td colspan="7">
                {% if order.mark == 'PROCESS_SUCCESS' %}
                处理完成
                {% elseif order.mark == 'PROCESS_FAILED' %}
                因[{{ order.fail_reason }}]而<span style="color:orangered">无法处理</span>
                {% else %}
                未处理
                {% endif %}
            </td>
        </tr>
    </table>
</div>