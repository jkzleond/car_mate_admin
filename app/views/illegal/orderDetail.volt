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
    <table class="table" style="margin-bottom:0px;" id="illegal_order_detail_table">
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
        <tr id="illegal_order_detail_info_row">
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
                        <th>滞纳金</th>
                    </tr>
                    {% for item in order.items %}
                    <tr>
                        <td><pre style="text-align:left">{{ item['des'] }}</pre></td>
                        <td>{{ item['wfjfs'] }}</td>
                        <td>{{ item['fkje'] }}</td>
                        <td>{{ item['znj'] }}</td>
                    </tr>
                    {% endfor %}
                    <tr>
                        <th>总计罚款</th>
                        <td colspan="3">{{ order.sum_fkje }}</td>
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
<script type="text/javascript">
    //绑定页面加载完成事件(本页面)
    $(document).one('pageLoad:illegalOrderDetail', function(event){

        /**
         * 控件相关
         */

        /**
         * 事件相关
         */
        //已支付的情况下才绑定相关事件
        var pay_state = $('#illegal_order_detail_table [name="pay_state"]').val();

        if(pay_state == 'TRADE_SUCCESS' || pay_state == 'TRADE_FINISHED')
        {
            //绑定驾驶员信息及车辆信息表列双击事件
            $('#illegal_order_detail_table .illegal-editable').dblclick(function(event){
                var $info_text = $(this).find('.illegal-info-text');
                var info_text_width = $info_text.width();
                var info_text_height = $info_text.height();
                var container_width = $(this).innerWidth();
                var container_height = $(this).innerHeight();

                var info_input_left = (container_width - info_text_width) / 2;
                var info_input_top = (container_height - info_text_height -10) / 2;

                $(this).find('.illegal-info-input')
                    .attr('type', 'input')
                    .val($info_text.text())
                    .outerWidth(info_text_width)
                    .outerHeight(info_text_height + 10)
                    .css('top', info_input_top + 'px')
                    .css('left', info_input_left + 'px')
                    .focus();
            });

            //驾驶员信息及车辆信息input失去焦点事件,及改变事件
            $('#illegal_order_detail_table .illegal-editable .illegal-info-input').focusout(function(event){
                $(this).attr('type', 'hidden');
                $(this).parent().find('.illegal-info-text').show();
            }).change(function(event){
                var new_value = $(this).val();
                $(this).attr('data-has-change', 'true');

                var reuqest_url = '';
                var request_data = {};
                var key = $(this).attr('name');
                var value = $(this).val();
                var update_target_str = null;

                if($(this).hasClass('illegal-driver-info'))
                {
                    request_url = '/illegal/driverInfo/' + $('#illegal_order_detail_table [name="driver_info_id"]').val() + '.json';
                    request_data.data = {};
                    request_data.data[key] = value;
                    update_target_str = '驾驶员信息';
                }
                else if($(this).hasClass('illegal-car-info'))
                {
                    update_target_str = '车辆信息';
                    var car_info_id = $('#illegal_order_detail_table [name="car_info_id"]').val();
                    if(car_info_id)
                    {
                        request_url = '/illegal/carInfo/' + car_info_id + '.json';
                        request_data.criteria = {};
                        request_data.criteria[key] = value;
                    }
                    else
                    {
                        //兼容老版本,车和人的信息没有分开
                        request_url = '/illegal/driverInfo/' + $('#illegal_order_detail_table [name="driver_info_id"]').val() + '.json';
                        request_data.data = {};
                        request_data.data[key] = value;
                        request_data.criteria = {};
                        request_data.criteria['user_id'] = $('#illegal_order_detail_table [name="user_id"]').val();
                        request_data.criteria['hphm'] = $('#illegal_order_detail_table [name="hphm"]').val();
                        request_data.criteria['engine_no'] = $('#illegal_order_detail_table [name="old_engine_no"]').val();
                    }
                }

                var $this = $(this);

                $.ajax({
                    url: request_url,
                    method: 'PUT',
                    data: request_data,
                    global: true
                }).done(function(data){
                    if(!data.success)
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: update_target_str + '更新失败'
                        });
                    }
                    else
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: update_target_str + '更新成功'
                        });
                        //更新成功后更改表列内text内容
                        $this.parent().find('.illegal-info-text').text(new_value);
                        $this.parent().css('backgroundColor', 'orangered').fadeOut(function(event){
                            $(this).css('backgroundColor', 'white').show();
                        });
                    }
                });

            });
        }

        //绑定页面离开事件(用于取消事件绑定及销毁控件)
        $(document).one('pageLeave:illegalOrderDetail', function(event){

        });
    });
</script>