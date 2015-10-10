<style type="text/css">
    td.illegal-editable {
        position: relative;
    }
    td.illegal-editable .illegal-info-input {
        text-align: center;
        position: absolute;
    }
</style>
<div class="row-fluid">
    <table class="table" style="margin-bottom:0px;" id="activity_user_order_detail_table">
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
        <!-- <tr id="activity_user_order_detail_info_row">
            <th style="position: relative">驾驶证号</th>
            <td style="display:none">
                <input type="hidden" name="driver_info_id" value= "{{ order.driver_info_id }}">
                <input type="hidden" name="car_info_id" value= "{{ order.car_info_id }}">
            </td>
            <td class="illegal-editable">
                <div class="illegal-info-text">{{ order.license_no }}</div>
                <input type="hidden" name="license_no" class="illegal-info-input illegal-driver-info">
            </td>
            <th>档案号</th>
            <td class="illegal-editable">
                <div class="illegal-info-text">{{ order.archive_no }}</div>
                <input type="hidden" name="archive_no" class="illegal-info-input illegal-driver-info">
            </td>
            <th>车牌号</th>
            <td>
                <div>{{ order.hphm }}</div>
                <input type="hidden" name="hphm" value="{{ order.hphm }}">
            </td>
            <th>发动机号</th>
            <td class="illegal-editable">
                <div class="illegal-info-text">{{ order.engine_no }}</div>
                <input type="hidden" name="engine_no" class="illegal-info-input illegal-car-info">
                <input type="hidden" name="old_engine_no" value="{{ order.engine_no }}">
            </td>
        </tr> -->
        <tr>
            <th>
                付款项目
            </th>
            <td colspan="7">
                <table class="table" style="margin-bottom:0px;">
                    <tr>
                        <th>名称</th>
                        <th>数量</th>
                        <th>金额</th>
                    </tr>
                    {% set sum_price = 0 %}
                    {% for item in order.items %}
                    {% set sum_price += item['price'] * item['number'] %}
                    <tr>
                        <td>{{ item['name'] }}</td>
                        <td>{{ item['number'] }}</td>
                        <td>{{ item['price'] }}</td>
                    </tr>
                    {% endfor %}
                    <tr>
                        <th>总计</th>
                        <td colspan="2">{{ sum_price }}</td>
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
                <input type="hidden" name="pay_state" value="{{ order.pay_state }}">
                {% if order.pay_state == 'TRADE_SUCCESS' or order.pay_state == 'TRADE_FINISHED' %}
                已支付
                {% else %}
                未支付
                {% endif %}
            </td>
            <td colspan="2"></td>
        </tr>
    </table>
</div>
<script type="text/javascript">
    //绑定页面加载完成事件(本页面)
    $(document).one('pageLoad:activityUserOrderDetail', function(event){

        }

        //绑定页面离开事件(用于取消事件绑定及销毁控件)
        $(document).one('pageLeave:activityUserOrderDetail', function(event){

        });
    });
</script>