<style type="text/css">
    .table td, .table th {
        text-align: center;
        vertical-align: middle;
    }
    #insurance_newinfo_detail_table td {
        padding: 0px;
    }
</style>
<div class="row-fluid">
    <table class="table" style="margin-bottom:0px;" id="insurance_newinfo_detail_table">
        <tr>
            <th colspan="1">险种名称</th>
            <td colspan="2">{{ info.type_name }}</td>
            <th colspan="1">保单号</th>
            <td colspan="2">{{ info.policy_no }}</td>
            <th colspan="1">提交时间</th>
            <td colspan="1">{{ info.create_date | uniform_time }}</td>
        </tr>
        <tr>
            <th>用户名</th>
            <td>{{ info.user_id }}</td>
            <th>用户姓名</th>
            <td>{{ info.user_name }}</td>
            <td></td>
            <th>联系电话</th>
            <td>{{ info.phone }}</td>
            <td></td>
        </tr>
        <tr>
            <th colspan="2">表单内容</th>
            <th colspan="3">初算保费</th>
            <th colspan="3">精算保费</th>
        </tr>
        <tr>
            <td colspan="2">
                <table class="table">
                {% for name, value in info.type_attr %}
                    <tr><th>{{name}}</th><td>{{value}}</td></tr>
                {% endfor %}    
                </table>
            </td>
            <td colspan="3">
                {% if info.type_price_type == '2' %}
                {{ info.preliminary_premium }}
                {% else %}
                <table class="table">
                {% for name, value in info.preliminary_result %}
                    <tr><th>{{name}}</th><td>{{value}}</td></tr>
                {% endfor %}
                </table>
                {% endif %}
            </td>
            <td colspan="3">
            {#固定价格就不显示精算表格#}
            {% if info.type_price_type != '2' %}
                <table class="table">
                    <tr>
                        <th>保险公司</th>
                        <td colspan="1">
                            <select name="company_id" class="input-xlarge" style="margin:0px">
                            {% for company in company_list %}
                                {% if company['companyId'] == info.company_id%}
                                <option value="{{company['companyId']}}" data-discount="{{company['discount_percent']}}" selected>{{company['companyName']}}[优惠:{{company['discount_percent']}}%]</option>
                                {% else %}
                                <option value="{{company['companyId']}}" data-discount="{{company['discount_percent']}}">{{company['companyName']}}[优惠:{{company['discount_percent']}}%]</option>
                                {% endif %}
                            {% endfor %}
                            </select>
                        </td>
                        <td>
                            {% if info.state <= 2 %}
                            <button class="btn" id="insurance_newinfo_detail_add_actuary_item_btn"><i class="iconfa-plus"></i>添加价目</button>
                            {% endif %}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <table class="table">
                                <tr>
                                    <th>险别</th>
                                    <th>标准保费</th>
                                    <th>优惠保费</th>
                                </tr>
                                <tbody id="insurance_newinfo_detail_actuary_item_container">
                                    
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th>已精算保险公司</th>
                        <td colspan="1" id="insurance_newinfo_detail_actuary_company_container"></td>
                        <td>
                            {% if info.state <=2 %}
                            <button class="btn btn-primary" id="insurance_newinfo_detail_add_actuary_btn">添加精算结果</button>
                            {% elseif info.state == 3 or info.state == 4 %}
                            <button class="btn btn-primary" id="insurance_newinfo_detail_mk_policy_btn" >出单</button>
                            {% endif %}
                        </td>
                    </tr>
                </table>
            {% else %}
            {{ info.preliminary_premium }}
            {% endif %}
            </td>
        </tr>
        <tr>
            <th>状态</th>
            <td>
                {% if info.state == '1' %}
                已初算
                {% elseif info.state == '2' %}
                待精算
                {% elseif info.state == '3' %}
                已精算
                {% elseif info.state == '4' %}
                待出单
                {% elseif info.state == '5' %}
                已出单
                {% endif %}
            </td>
            <th>出单金额</th>
            <td>{{ info.policy_fee }}</td>
            <th>支付状态</th>
            <td>
                {% if info.pay_state == '1' %}
                未支付
                {% elseif info.pay_state == '2' %}
                已支付
                {% endif %}
            </td>
            <th>支付时间</th>
            <td>{{info.pay_date}}</td>
        </tr>
    </table>
</div>
<script type="text/javascript">
    //绑定页面加载完成事件(本页面)
    $(document).one('pageLoad:insuranceNewInfoDetail', function(event){

        /**
         * 控件相关
         */

        /**
         * 事件相关
         */
        
        $('#insurance_newinfo_detail_add_actuary_item_btn').click(function(event){
            var tpl = _.template($('#insurance_newinfo_detail_actuary_item_tpl').html());
            $('#insurance_newinfo_detail_actuary_item_container').append(tpl());
        });
        

        //绑定页面离开事件(用于取消事件绑定及销毁控件)
        $(document).one('pageLeave:insuranceNewInfoDetail', function(event){

        });
    });
</script>
<script type="text/html" id="insurance_newinfo_detail_actuary_item_tpl">
    <tr>
        <td><input type="text" ></input></td>
        <td><input type="text" ></input></td>
        <td><input type="text" disabled></input></td>
    </tr>
</script>