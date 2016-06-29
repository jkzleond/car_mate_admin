<style type="text/css">
    td.move_car-editable {
        position: relative;
    }
    td.move_car-editable .move_car-info-input {
        text-align: center;
        position: absolute;
    }
</style>
<div class="row-fluid">
    <table class="table" style="margin-bottom:0px;" id="move_car_order_detail_table">
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
            <td>
                {{ order.user_id }}
                <input type="hidden" name="user_id" value= "{{ order.user_id }}">
            </td>
            <th>用户姓名</th>
            <td>{{ order.user_name }}</td>
            <th>联系电话</th>
            <td>{{ order.phone }}</td>
            <td colspan="2"></td>
        </tr>
        <tr id="move_car_order_detail_info_row">
            <th style="position: relative">挡路车牌号</th>
            <td class="move_car-editable">
                <div class="move_car-info-text">{{ order.record['hphm'] }}</div>
                <input type="hidden" name="license_no" class="move_car-info-input move_car-driver-info">
            </td>
        </tr>
        <tr>
            <th>
                相关车主
            </th>
            <td colspan="7">
                <table class="table" style="margin-bottom:0px;">
                    <tr>
                        <th>来源</th>
                        <th>用户名</th>
                        <th>电话</th>
                    </tr>
                    {% for car_owner in car_owners %}
                    <tr>
                        <td>
                            {% if(car_owner['source']) == 'cm' %}
                            用户输入
                            {% else %}
                            交管数据
                            {% endif %}
                        </td>
                        <td>
                            {% if(car_owner['source']) == 'cm' %}
                                {{ car_owner['user_id'] }}
                            {% endif %}
                        </td>
                        <td>{{ car_owner['phone'] }}</td>
                    </tr>
                    {% endfor %}
                </table>
            </td>
        </tr>
        <tr>
            <th>
                通话清单
            </th>
            <td colspan="7">
                <table class="table" style="margin-bottom:0px;">
                    <tr>
                        <th>主叫</th>
                        <th>被叫</th>
                        <th>时长(秒)</th>
                        <th>话费</th>
                        <th>状态</th>
                    </tr>
                    {% set sum_duration = 0 %}
                    {% set sum_bill = 0 %}
                    {% for call_record in call_records %}
                        {% set sum_duration += call_record['duration'] %}
                        {% set sum_bill += call_record['bill'] %}
                    <tr>
                        <td>{{ call_record['caller'] }}</td>
                        <td>{{ call_record['called'] }}</td>
                        <td>{{ call_record['duration'] }}</td>
                        <td>{{ call_record['bill'] }}</td>
                        <td>
                            {% if call_record['is_link'] == 1 %}
                                接通
                            {% else %}
                                未接通
                            {% endif %}
                        </td>
                    </tr>
                    {% endfor %}
                    <tr>
                        <th>总计时长(秒)</th>
                        <td colspan="1">{{ sum_duration }}</td>
                        <th>总计话费</th>
                        <td colspan="2">{{ sum_bill }}</td>
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
            <td>{{ order.total_fee }}</td>
            <th>支付状态</th>
            <td>
                {% if order.pay_state == 'TRADE_SUCCESS' or order.pay_state == 'TRADE_FINISHED' %}
                已支付
                {% elseif order.pay_state == 'ORDER_FREE' %}
                免费
                {% else %}
                未支付
                {% endif %}
            </td>
            {% if not (order.ticket is empty) %}
            <th>票券</th>
            <td>
                {{ order.ticket['title'] }}:{{ order.ticket['value'] }}
            </td>
            {% else %}
            <td colspan="2"></td>
            {% endif %}
        </tr>
        {% if not (feed_back is empty) %}
        <tr>
            <th>
                用户反馈
            </th>
            <td colspan="7">
                <table class="table" style="margin-bottom:0px;">
                    <tr>
                        <th>问题</th>
                        <th>建议</th>
                    </tr>
                    <tr>
                        <td style="text-align: left">
                            1.遇到的问题: <br>
                            {% if feed_back['q1_1'] == 1 %}
                                &nbsp;&nbsp;语音拨通了,但对方不是车主 <br>
                            {% elseif feed_back['q1_2'] == 1 %}
                                &nbsp;&nbsp;多次尝试,没有收到电话回拨 <br>
                            {% elseif feed_back['q1_3'] == 1 %}
                                &nbsp;&nbsp;多次尝试,没有收到电话回拨 <br>
                            {% elseif feed_back['q1_4'] == 1 %}
                                &nbsp;&nbsp;多次尝试,没有收到电话回拨 <br>
                            {% elseif feed_back['q1_5'] == 1 %}
                                &nbsp;&nbsp;多次尝试,没有收到电话回拨 <br>
                            {% elseif feed_back['q1_6'] == 1 %}
                                &nbsp;&nbsp;多次尝试,没有收到电话回拨 <br>
                            {% elseif feed_back['q1_7'] %}
                                &nbsp;&nbsp;{{ feed_back['q1_7'] }} <br>
                            {% endif %}
                            2.收费10元是否愿意继续使用: <br>
                            {% if feed_back['q2'] == 1 %}
                                &nbsp;&nbsp;愿意 <br>
                            {% elseif feed_back['q2'] == 2 %}
                                &nbsp;&nbsp;再便宜点可以接受 <br>
                            {% elseif feed_back['q2'] == 3 %}
                                &nbsp;&nbsp;收费就不用了 <br>
                            {% endif %}
                        </td>
                        <td>{{ feed_back['advise'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        {% endif %}
        {% if not (appeal is empty) %}
        <tr>
            <th>
                申诉内容
            </th>
            <td colspan="7">
                <table class="table" style="margin-bottom:0px;">
                    <tr>
                        <th>问题</th>
                        <th>建议</th>
                    </tr>
                    <tr>
                        <td>
                            {% if appeal['problem'] == 1 %}
                            语音拨通了,但对方不是车主
                            {% elseif appeal['problem'] == 2 %}
                            多次尝试,没有收到电话回拨
                            {% elseif appeal['problem'] == 3 %}
                            {{ appeal['addition'] }}
                            {% endif %}
                        </td>
                        <td>{{ appeal['advise'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        {% endif %}
        {% if not (appeal is empty) %}
        <tr>
            <th>申诉处理结果</th>
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
        {% endif %}
    </table>
</div>