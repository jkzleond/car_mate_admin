<style type="text/css" rel="stylesheet">
    .insurance-company-box {
        margin: 5px;
        padding: 5px;
        border-radius: 0.5rem;
        font-size: 16px;
        font-weight: bold;
        color: white;
        background-color: steelblue;
        display: inline-block;
        height: 42px;
        line-height: 42px;
        cursor: pointer;
    }
    .insurance-company-box:hover {
        background-color: slateblue;
    }
    .insurance-company-box.selected {
        background-color: orange;
    }
</style>
<div>
    <div style="width: 100%;">
        <form action="" method="post" id="insurance_result_form">
            <table class="table">
                <tr style="display: none;" id="insurance_no_input">
                    <th>保单号</th>
                    <td bgcolor="#FFDDCC" colspan="5"><!-- #FFC8B4 #FFDDCC -->
                        <input type="text" name="insurance_no" style="width: 250px;"/>
                    </td>
                </tr>
                <tr>
                    <th>车牌号</th>
                    <td bgcolor="#EEE">{{ info.c_hphm }}</td>
                    <th>车主姓名</th>
                    <td bgcolor="#EEE">{{ info.userName }}</td>
                    <th>联系方式</th>
                    <td bgcolor="#EEE">{{ info.phoneNo }}</td>
                </tr>
                <tr>
                    <th>车架号</th>
                    <td bgcolor="#EEE">{{ info.c_frameNumber }}</td>
                    <th>行驶省</th>
                    <td bgcolor="#EEE">{{ info.c_provinceName }}</td>
                    <th>行驶城市</th>
                    <td bgcolor="#EEE">{{ info.c_cityName }}</td>
                </tr>
                <tr>
                    <th>车价</th>
                    <td bgcolor="#EEE">{{ insurance.carPrice }}<input type="hidden" name="car_price" value="{{ insurance.carPrice }}"/></td>
                    <th>座位</th>
                    <td bgcolor="#EEE">{{ insurance.carSeat }}<input type="hidden" name="car_seat" value="{{ insurance.carSeat }}"/></td>
                    <th>车主邮箱</th>
                    <td bgcolor="#EEE">{{ info.emailAddr }}</td>
                </tr>
                <tr>
                    <th>整年、月参数</th>
                    <td bgcolor="#EEE">
                        {{ result.roundYear }}|{{ result.roundMonth }}
                        <input type="hidden" name="round_year" value="{{ result.roundYear }}"/>
                        <input type="hidden" name="round_month" value="{{ result.roundMonth }}"/>
                    </td>
                    <th>初登年月</th>
                    <td bgcolor="#EEE">
                        {{ insurance.firstYear }}.{{ insurance.firstMonth }}
                        <input type="hidden" name="first_year" value="{{ insurance.firstYear }}"/>
                        <input type="hidden" name="first_month" value="{{ insurance.firstMonth }}"/>
                    </td>
                    <th>起保年月</th>
                    <td bgcolor="#EEE">
                        {{ insurance.insuranceYear }}.{{ insurance.insuranceMonth }}
                        <input type="hidden" name="insurance_year" value="{{ insurance.insuranceYear }}"/>
                        <input type="hidden" name="insurance_month" value="{{ insurance.insuranceMonth }}"/>
                    </td>
                </tr>
                <tr height="25px">
                    <th>联系地址</th>
                    <td bgcolor="#EEE">{{ info.a_address }}</td>
                    <th>发动机号</th>
                    <td bgcolor="#EEE">{{ info.c_fdjh }}</td>
                    <th>品牌型号</th>
                    <td bgcolor="#EEE">{{ info.c_autoname }}</td>
                </tr>
                <tr>
                    <th>身份证号</th>
                    <td bgcolor="#EEE">{{ info.sfzh }}</td>
                    {% if info.state_id == 7 %}
                    <td >无法精算理由</td>
                    <td bgcolor="#EEE">{{ info.failureReason }}</td>
                    {% endif %}
                </tr>
                {% if attach is empty is false %}
                <tr>
                    <th>驾驶证号</th>
                    <td bgcolor="#EEE">
                        <img class="insurance-result-attach-img" style="width:50px; height:50px" src="data:image/png;base64,{{ attach.driving_license_a }}" alt="驾驶证正面">
                        <img class="insurance-result-attach-img" style="width:50px; height:50px" src="data:image/png;base64,{{ attach.driving_license_b }}" alt="驾驶证背面">
                    </td>
                    <th>身份证号</th>
                    <td bgcolor="#EEE">
                        <img class="insurance-result-attach-img" style="width:50px; height:50px" src="data:image/png;base64,{{ attach.idcard }}" alt="身份证">
                    </td>
                    <th>保险卡</th>
                    <td bgcolor="#EEE">
                        {% if attach.insurance_card is empty is false %}
                        <img class="insurance-result-attach-img" style="width:50px; height:50px" src="data:image/png;base64,{{ attach.insurance_card }}" alt="保险卡">
                        {% endif %}
                    </td>
                </tr>
                {% endif %}
                <tr>
                    <th>保险公司及折扣</th>
                    <td bgcolor="#CCDDEE" colspan="5">
                        <select name="discount_company_id" id="insurance_discount_select_btn" class="input-xlarge">
                            {% for discount in discount_list %}
                                {% if insurance.d_companyId == discount.companyId%}
                            <option value="{{ discount.companyId }}" data-discount="{{ discount.discount }}" selected>{{ discount.companyName }}(折扣:{{ discount.discount }})</option>
                                {% else %}
                            <option value="{{ discount.companyId }}" data-discount="{{ discount.discount }}">{{ discount.companyName }}(折扣:{{ discount.discount }})</option>
                                {% endif %}
                            {% endfor %}
                        </select>
                    </td>
                </tr>
                <tr height="30px">
                    <th width="10%">险别</th>
                    <th width="25%">保险金额</th>
                    <th width="20%">标准保费</th>
                    <th width="20%">优惠后保费</th>
                    <th bgcolor="#EFEFEF" colspan="2">单项不计免赔</th>
                </tr>
                <tr>
                    <th bgcolor="#EFEFEF">交强险</th>
                    <td >
                        <select name="compulsory_id">
                            {% for compulsory_state in compulsory_state_list %}
                                {% if insurance.compulsory_id == compulsory_state.id %}
                            <option value="{{ compulsory_state.id }}" selected>{{ compulsory_state.status }}</option>
                                {% else %}
                            <option value="{{ compulsory_state.id }}">{{ compulsory_state.status }}</option>
                                {% endif %}
                            {% endfor %}
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_compulsory_insurance" id="standard_compulsory_insurance" value="{{ result.standardCompulsoryInsurance | number_format(2) }}" readonly="readonly"/></td>
                    <td><input class="after-discount-value" data-rel="#standard_compulsory_insurance" name="after_discount_compulsory_insurance" id="after_discount_compulsory_insurance" value="{{ result.afterDiscountCompulsoryInsurance | number_format(2) }}"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_compulsory_insurance" id="single_not_deductible_compulsory_insurance" value="{{ result.singleNotDeductibleCompulsoryInsurance | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th>车损险</th>
                    <td>
                        <select name="damage_id">
                            {% for insurance_status in insurance_status_list %}
                                {% if insurance.damage_id == insurance_status.id%}
                            <option value="{{ insurance_status.id }}" selected>{{ insurance_status.status }}</option>
                                {% endif %}
                            <option value="{{ insurance_status.id }}">{{ insurance_status.status }}</option>
                            {% endfor %}
                        </select>
                    </td>
                    <td ><input class="insurance-number-disabled" name="standard_damage_insurance" id="standard_damage_insurance" value="{{ result.standardDamageInsurance | number_format(2) }}" readonly="readonly"/></td>
                    <td ><input class="after-discount-value" data-rel="#standard_damage_insurance" name="after_discount_damage_insurance" id="after_discount_damage_insurance" value="{{ result.afterDiscountDamageInsurance | number_format(2) }}"/></td>
                    <td  colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_damage_insurance" id="single_not_deductible_damage_insurance" value="{{ result.singleNotDeductibleDamageInsurance | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th>第三者</th>
                    <td ><input class="insurance-number-int" type="text" name="third" value="{{ insurance.third | number_format(2) }}"/></td>
                    <td ><input class="insurance-number-disabled" name="standard_third" id="standard_third" value="{{ result.standardThird | number_format(2) }}" readonly="readonly"/></td>
                    <td ><input class="after-discount-value" data-rel="#standard_third" name="after_discount_third" id="after_discount_third" value="{{ result.afterDiscountThird | number_format(2) }}"/></td>
                    <td  colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_third" id="single_not_deductible_third" value="{{ result.singleNotDeductibleThird | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th rowspan="2">座位</th>
                    <td>驾驶员（1）: <input class="insurance-number-int" name="driver" value="{{ insurance.driver | number_format(2) }}"/></td>
                    <td><input class="insurance-number-disabled" name="standard_driver" id="standard_driver" value="{{ result.standardDriver | number_format(2) }}" readonly="readonly"/></td>
                    <td><input class="after-discount-value" data-rel="#standard_driver" name="after_discount_driver" id="after_discount_driver" value="{{ result.afterDiscountDriver | number_format(2) }}"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_driver" id="single_not_deductible_driver" value="{{ result.singleNotDeductibleDriver | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <td>乘客（{{ insurance.carSeat - 1 }}） : <input class="insurance-number-int" name="passenger" value="{{ insurance.passenger | number_format(2) }}"/></td>
                    <td><input class="insurance-number-disabled" name="standard_passenger" id="standard_passenger" value="{{ result.standardPassenger | number_format(2) }}" readonly="readonly"/></td>
                    <td><input class="after-discount-value" data-rel="#standard_passenger" name="after_discount_passenger" id="afterDiscountPassenger" value="{{ result.afterDiscountPassenger | number_format(2) }}"/></td>
                    <td colspan="2"><input   class="single-not-deductible-value" name="single_not_deductible_passenger" id="single_not_deductible_passenger" value="{{ result.singleNotDeductiblePassenger | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th>盗抢</th>
                    <td >
                        <select name="robbery_id">
                            {% for insurance_status in insurance_status_list %}
                                {% if insurance.robbery_id == insurance_status.id%}
                            <option value="{{ insurance_status.id }}" selected>{{ insurance_status.status }}</option>
                                {% else %}
                            <option value="{{ insurance_status.id }}">{{ insurance_status.status }}</option>
                                {% endif %}
                            {% endfor %}
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_robbery" id="standard_robbery" value="{{ result.standardRobbery | number_format(2) }}" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_robbery" name="after_discount_robbery" id="after_discount_robbery" value="{{ result.afterDiscountRobbery | number_format(2) }}"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_robbery" id="single_not_deductible_robbery" value="{{ result.singleNotDeductibleRobbery | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th>玻璃</th>
                    <td>
                        <select name="glass_id">
                            {% for glass_state in glass_state_list %}
                                {% if insurance.glass_id == glass_state.id %}
                            <option value="{{ glass_state.id }}" selected>{{ glass_state.status }}</option>
                                {% else %}
                            <option value="{{ glass_state.id }}">{{ glass_state.status }}</option>
                                {% endif %}
                            {% endfor %}
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_glass" id="standard_glass" value="{{ result.standardGlass | number_format(2) }}" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_glass" name="after_discount_glass" id="after_discount_glass" value="{{ result.afterDiscountGlass | number_format(2) }}"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_glass" id="single_not_deductible_glass" value="{{ result.singleNotDeductibleGlass | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th>划痕</th>
                    <td><input class="insurance-number" type="text" name="scratch" id="scratch" value="{{ insurance.scratch | number_format(2) }}"/></td>
                    <td><input class="insurance-number-disabled" name="standard_scratch" id="standard_scratch" value="{{ result.standardScratch | number_format(2) }}" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_scratch" name="after_discount_scratch" id="after_discount_scratch" value="{{ result.afterDiscountScratch | number_format(2) }}"/></td>
                    <td  colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_scratch" id="single_not_deductible_scratch" value="{{ result.singleNotDeductibleScratch | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th>自燃</th>
                    <td>
                        <select name="self_ignition_id">
                            {% for insurance_status in insurance_status_list %}
                                {% if insurance.selfIgnition_id == insurance_status.id%}
                            <option value="{{ insurance_status.id }}" selected>{{ insurance_status.status }}</option>
                                {% else %}
                            <option value="{{ insurance_status.id }}">{{ insurance_status.status }}</option>
                                {% endif %}
                            {% endfor %}
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_self_ignition" id="standard_self_ignition" value="{{ result.standardSelfIgnition | number_format(2) }}" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_self_ignition" name="after_discount_self_ignition" id="after_discount_self_ignition" value="{{ result.afterDiscountSelfIgnition | number_format(2) }}"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_self_ignition" id="single_not_deductible_self_ignition" value="{{ result.singleNotDeductibleSelfIgnition | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th>可选免赔额</th>
                    <td><input class="insurance-number" type="text" name="optional_deductible" id="optional_deductible" value="{{ insurance.optionalDeductible | number_format(2) }}"/></td>
                    <td><input class="insurance-number-disabled" name="standard_optional_deductible" id="standard_optional_deductible" value="{{ result.standardOptionalDeductible | number_format(2) }}" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_optional_deductible" name="after_discount_optional_deductible" id="after_discount_optional_deductible" value="{{ result.afterDiscountOptionalDeductible | number_format(2) }}"/></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <th>不计免赔</th>
                    <td >
                        <select name="not_deductible_id">
                            {% for insurance_status in insurance_status_list %}
                                {% if insurance.notDeductible_id == insurance_status.id%}
                            <option value="{{ insurance_status.id }}" selected>{{ insurance_status.status }}</option>
                                {% else %}
                            <option value="{{ insurance_status.id }}">{{ insurance_status.status }}</option>
                                {% endif %}
                            {% endfor %}
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_not_deductible" id="standard_not_deductible" value="{{ result.standardNotDeductible | number_format(2) }}" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_not_deductible" name="after_discount_not_deductible" id="after_discount_not_deductible" value="{{ result.afterDiscountNotDeductible | number_format(2) }}"/></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: center;">合计</th>
                    <td bgcolor="#FFEE99"><input class="insurance-number-disabled" name="total_standard" id="total_standard" value="{{ result.totalStandard | number_format(2) }}" readonly="readonly"/></td>
                    <td bgcolor="#FFEE99"><input name="total_after_discount" id="total_after_discount" value="{{ result.totalAfterDiscount | number_format(2) }}"/></td>
                    <td bgcolor="#FFEE99" colspan="2"><input name="total_single_not_deductible" id="total_single_not_deductible" value="{{ result.totalSingleNotDeductible | number_format(2) }}"/></td>
                </tr>
                <tr>
                    <th>商业</th>
                    <td bgcolor="#FFEE99"><input class="insurance-number" name="business" value="{{ result.business | number_format(2) }}"/></td>
                    <td bgcolor="#FFEE99">
                        车船税（
                        {% if insurance.tax === 0 or insurance.tax === '0' %}
                        不
                        {% endif %}
                        代缴）:
                    </td>
                    <td bgcolor="#FFEE99">
                        <input class="insurance-number" type="text" name="tax_money" value="{{ result.taxMoney | number_format(2) }}"/>
                    </td>
                    <td bgcolor="#FFEE99" colspan="2">
                        排量：{% if insurance.displacement %} {{ insurance.displacement }} {% endif %}
                    </td>
                </tr>
                <tr>
                    <th>出单礼包</th>
                    <td colspan="4">
                        <input class="insurance-number" type="text" name="gift_money" value="{{ result.giftMoney | number_format(2) }}"/>         
                    </td> 
                </tr>   
                <tr>
                    <th>已精算的保险公司</th>
                    <td colspan="4">
                        <div id="insurance_loading" style="display:none"></div>
                        <div id="insurance_has_actuary_container"></div>
                    </td>
                </tr>
                <tr height="30px">
                    <td  colspan="3" style="text-align: center;">
                        <input type="hidden" name="user_id" value="{{ info.userId }}"/>
                        <input type="hidden" name="info_id" value="{{ info.id }}"/>
                        <input type="hidden" name="result_id" value="{{ result.id }}"/>

                        {% if info.state_id == 2 or info.state_id == 7 %}
                        <input type="submit" id="insurance_company_result_cu_btn" class="btn btn-primary" value="提交当前保险公司精算结果(新)" style="width: 250px"/>
                        <input type="submit" id="insurance_result_update_btn" class="btn btn-warning" value="提交精算结果" style="width: 250px"/>
                        {% elseif info.state_id == 3 %}
                        <input type="submit" id="insurance_company_result_cu_btn" class="btn btn-primary" value="提交当前保险公司精算结果(新)" style="width: 250px"/>
                        <input type="submit" id="insurance_result_update_btn" class="btn btn-warning" value="提交精算结果" style="width: 250px"/>
                        <input type="submit" id="insurance_issuing_btn" class="btn btn-primary" value="出单" style="width: 250px"/>
                        {% endif %}
                    </td>
                    {% if info.state_id == 2 or info.state_id == 3 or info.state_id == 4 or info.state_id == 7 %}
                    <td  colspan="3">
                        <input type="button" id="insurance_unupdate_btn" data-id="{{ result.id }}" value="无法精算" class="reason-btn btn btn-danger" style="width: 250px"/>
                    </td>
                    {% else %}
                    <td  colspan="3">
                    </td>
                    {% endif %}
                </tr>
            </table>
        </form>
    </div>
</div>

{% if info.state_id == 2 or info.state_id == 3 or info.state_id == 4 or info.state_id == 7 %}
<div id="insurance_reason_dialog">
    <form action="" id="insurance_reason_form">
        <input type="hidden" name="id" value="{{ info.id }}"/>
        <div class="row-fluid">
            <span class="label">理由</span>
            <textarea name="reason" rows="5" cols="40"></textarea>
        </div>
    </form>
</div>
{% endif %}

{% if info.state_id == 3 %}
<div id="insurance_issuing_dialog">
    <form action="" id="insurance_issuing_form">
        <input type="hidden" id="" name="id" value="{{ info.id }}"/>
        <input type="hidden" id="" name="user_id" value="{{ info.userId }}"/>
        <div class="row-fluid">
            <span class="label">出单时间</span>
            <input name="issuing_time" validate="required" />
        </div>
        <div class="row-fluid">
            <span class="label">实收金额</span>
            <input type="text" name="actul_amount" />
        </div>
        <div class="row-fluid">
            <span class="label">优惠项目</span>
            <textarea name="preference_items" rows="5" cols="40"></textarea>
        </div>
    </form>
</div>
{% endif %}

<script type="text/javascript">
    (function($){

        //数字控件(金额输入框)
        $('.after-discount-value, .single-not-deductible-value, .insurance-number').numberbox({
            precision: 2,
            groupSeparator: ','
        });

        $('.insurance-number-int').numberbox({
            precision: 0,
            groupSeparator: ','
        });

        $('.insurance-number-disabled').numberbox({
            precision: 2,
            groupSeparator: ',',
            disabled: true
        });

        var total_after_discount = $('#total_after_discount').numberbox({
            precision: 2,
            groupSeparator: ',',
            disabled: true
        });

        var total_not_deductible_value = $('#total_single_not_deductible').numberbox({
            precision: 2,
            groupSeparator: ',',
            disabled: true
        });

        var need_destroy_dialogs = $('#insurance_window').data('need_destroy');

        if(!need_destroy_dialogs)
        {
            need_destroy_dialogs = [];
            $('#insurance_window').data('need_destroy', need_destroy_dialogs);
        }

        {# 条件输出js #}
        {% if info.state_id == 2 or info.state_id == 3 or info.state_id == 4 or info.state_id == 7 %}
        //创建窗口
        var insurance_reason_dialog = $('#insurance_reason_dialog').dialog({
            title: '无法精算理由',
            iconCls: 'icon-plus',
            width: 'auto',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '提交',
                    iconCls: 'icon-ok',
                    handler: function(){

                        var id = $('#insurance_reason_form [name=id]').val();
                        var reason = $('#insurance_reason_form [name=reason]').val();

                        $.ajax({
                            url: '/insuranceCantExactReason.json',
                            method: 'PUT',
                            data: {
                                data:{id: id, reason: reason}
                            },
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(data.success)
                            {
                                $('#insurance_search_btn').click();
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '无法精算理由提交成功'
                                })
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '无法精算理由提交失败'
                                })
                            }
                        });

                        $('#insurance_reason_dialog').dialog('close', true);
                        $('#insurance_window').window('close');

                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#insurance_reason_dialog').dialog('close', true);
                    }
                }
            ]
        });

        need_destroy_dialogs.push(insurance_reason_dialog);

        //附件图片tooltip
        $('.insurance-result-attach-img').tooltip({
            position: 'right',
            content: '<img />',
            onShow: function(){
                $(this).tooltip('tip').find('img').attr('src', $(this).attr('src'));
            }
        });

        //事件
        $('#insurance_unupdate_btn').click(function(event){
            event.preventDefault();
            $('#insurance_reason_dialog').dialog('open', true).dialog('center');
            return false;
        });

        {% endif %}

        {% if info.state_id == 3 %}

        //出单时间控件
        var issuing_time_box = $('#insurance_issuing_form [name=issuing_time]').datetimebox();
        var actul_amount_box = $('#insurance_issuing_form [name=actul_amount]').numberbox({
            precision: 2,
            groupSeparator: ','
        });

        //创建窗口
        var insurance_issuing_dialog = $('#insurance_issuing_dialog').dialog({
            title: '出单',
            iconCls: 'icon-plus',
            width: 'auto',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '提交',
                    iconCls: 'icon-ok',
                    handler: function(){

                        var id = $('#insurance_issuing_form [name=id]').val();
                        var user_id = $('#insurance_issuing_form [name=user_id]').val();

                        var issuing_time = issuing_time_box.datetimebox('getValue');
                        var actul_amount = actul_amount_box.numberbox('getValue');
                        var preference_items = $('#insurance_issuing_form [name=preference_items]').val();

                        $.ajax({
                            url: '/insuranceIssuing.json',
                            method: 'PUT',
                            data: {
                                data:{
                                    id: id,
                                    user_id: user_id,
                                    issuing_time: issuing_time,
                                    actul_amount: actul_amount,
                                    preference_items: preference_items
                                }
                            },
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(data.success)
                            {
                                $('#insurance_search_btn').click();

                                $.messager.show({
                                    title: '系统消息',
                                    msg: '出单成功'
                                })
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '出单失败'
                                })
                            }
                        });
                        $('#insurance_issuing_dialog').dialog('close', true);
                        $('#insurance_window').window('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#insurance_issuing_dialog').dialog('close', true);
                    }
                }
            ]
        });

        need_destroy_dialogs.push(insurance_issuing_dialog);

        //事件
        $('#insurance_issuing_btn').click(function(event){
            event.preventDefault();
            $('#insurance_issuing_dialog').dialog('open', true).dialog('center');
            return false;
        });

        {% endif %}

        {% if info.state_id == 2 or info.state_id == 3 or info.state_id == 7 %}                        
            //提交尽算结果按钮点击事件
            $('#insurance_result_update_btn').click(function(event){
                event.preventDefault();

                var data = {};

                $('#insurance_result_form [name]').each(function(i, n){
                    var name = $(n).attr('name');
                    var value = $(n).val();
                    data[name] = value;
                });

                $.ajax({
                    url: '/insuranceExactResult.json',
                    method: 'PUT',
                    data: {data: data},
                    dataType: 'json',
                    global: true
                }).done(function(data){

                    if(data.success)
                    {
                        $('#insurance_search_btn').click();

                        $.messager.show({
                            title: '系统消息',
                            msg: '精算提交成功'
                        })
                    }
                    else
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '精算提交失败'
                        })
                    }
                });
                $('#insurance_window').window('close');
                return false;
            });

            //提交此公司尽算结果点击事件
            $('#insurance_company_result_cu_btn').click(function(event){
                event.preventDefault();

                var data = {};

                $('#insurance_result_form [name]').each(function(i, n){
                    var name = $(n).attr('name');
                    var value = $(n).val();
                    data[name] = value;
                });

                var company_id = data.discount_company_id;

                $.ajax({
                    url: '/insurance/finalResult/' + info_id + '/' + company_id + '.json',
                    method: 'POST',
                    data: {data: data},
                    dataType: 'json',
                    global: true
                }).done(function(data){

                    if(data.success)
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '精算提交成功'
                        });
                        loadHasActuaryCompany();
                    }
                    else
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '精算提交失败'
                        });
                    }
                });
            });

        {% endif %}

        //折扣后输入框输入事件
        $('.after-discount-value+.numberbox>.textbox-text').keyup(function(event){
            var total = 0;

            $('.after-discount-value+.numberbox>.textbox-text').each(function(i, n){
                var value_str = $(n).val();
                value_str = value_str.replace(',', '');
                var value_num = Number(value_str);
                total += value_num;
            });

            total_after_discount.numberbox('setValue', total)
        });

        //单项不计免赔输入框输入事件
        $('.single-not-deductible-value+.numberbox>.textbox-text').keyup(function(event){
            var total = 0;

            $('.single-not-deductible-value+.numberbox>.textbox-text').each(function(i, n){
                var value_str = $(n).val();
                value_str = value_str.replace(',', '');
                var value_num = Number(value_str);
                total += value_num;
            });

            total_not_deductible_value.numberbox('setValue', total);
        });

        //折扣select change 事件
        $('#insurance_discount_select_btn').change(function(event){
            var discount = Number($(this).children(':selected').attr('data-discount'));
            var new_total_num = 0;
            $('.after-discount-value').each(function(i, n){
                var data_rel = $(n).attr('data-rel');
                //先去除数字千位分割符
                var old_value = $(data_rel).val().replace(',', '');
                var new_value_num = Number(old_value) * discount;
                $(n).numberbox('setValue', new_value_num);
                new_total_num += new_value_num;
            });

            total_after_discount.numberbox('setValue', new_total_num);
        });


        //保险公司box点击事件
        $('#insurance_has_actuary_container').on('click', '.insurance-company-box', function(event){

            $('.insurance-company-box.selected').removeClass('selected');
            $(this).addClass('selected');

            var company_id = $(this).attr('data-id');

            $('#insurance_discount_select_btn').val(company_id);

            $.ajax({
                url: '/insurance/finalResult/' + info_id + '/' + company_id + '.json',
                method: 'GET',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.row) return;
                renderFinalResult(data.row);
            });
        });

        /*数据相关*/
        var info_id = '{{info.id}}';

        loadHasActuaryCompany();

        function loadHasActuaryCompany()
        {    
            $.ajax({
                url: '/insurance/' + info_id + '/hasActuaryCompany.json',
                method: 'GET',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.rows) return;
                renderActuaryCompany(data.rows);
            });
        }

        function renderActuaryCompany(company_list){
            $('#insurance_has_actuary_container').empty();
            $.each(company_list, function(i, company){
                var box_html = '<span class="insurance-company-box" data-id="' + company.id  + '"> ' + company.short_name + ' <span>';
                $('#insurance_has_actuary_container').append(box_html);
            });
        }

        function renderFinalResult(result)
        {
            var $form = $('#insurance_result_form');
            $form.find('[numberboxname="standard_compulsory_insurance"]').val(result.standardCompulsoryInsurance);
            $form.find('[numberboxname="after_discount_compulsory_insurance"]').val(result.afterDiscountCompulsoryInsurance);
            $form.find('[numberboxname="single_not_deductible_compulsory_insurance"]').val(result.singleNotDeductibleCompulsoryInsurance);
            $form.find('[numberboxname="standard_damage_insurance"]').val(result.standardDamageInsurance);
            $form.find('[numberboxname="after_discount_damage_insurance"]').val(result.afterDiscountDamageInsurance);
            $form.find('[numberboxname="single_not_deductible_damage_insurance"]').val(result.singleNotDeductibleDamageInsurance);
            $form.find('[numberboxname="standard_third"]').val(result.standardThird);
            $form.find('[numberboxname="after_discount_third"]').val(result.afterDiscountThird);
            $form.find('[numberboxname="single_not_deductible_third"]').val(result.singleNotDeductibleThird);
            $form.find('[numberboxname="standard_driver"]').val(result.standardDriver);
            $form.find('[numberboxname="after_discount_driver"]').val(result.afterDiscountDriver);
            $form.find('[numberboxname="single_not_deductible_driver"]').val(result.singleNotDeductibleDriver);
            $form.find('[numberboxname="standard_passenger"]').val(result.standardPassenger);
            $form.find('[numberboxname="after_discount_passenger"]').val(result.afterDiscountPassenger);
            $form.find('[numberboxname="single_not_deductible_passenger"]').val(result.singleNotDeductiblePassenger);
            $form.find('[numberboxname="standard_robbery"]').val(result.standardRobbery);
            $form.find('[numberboxname="after_discount_robbery"]').val(result.afterDiscountRobbery);
            $form.find('[numberboxname="single_not_deductible_robbery"]').val(result.singleNotDeductibleRobbery);
            $form.find('[numberboxname="standard_glass"]').val(result.standardGlass);
            $form.find('[numberboxname="after_discount_glass"]').val(result.afterDiscountGlass);
            $form.find('[numberboxname="single_not_deductible_glass"]').val(result.singleNotDeductibleGlass);
            $form.find('[numberboxname="standard_scratch"]').val(result.standardScratch);
            $form.find('[numberboxname="after_discount_scratch"]').val(result.afterDiscountScratch);
            $form.find('[numberboxname="single_not_deductible_scratch"]').val(result.singleNotDeductibleScratch);
            $form.find('[numberboxname="standard_self_ignition"]').val(result.standardSelfIgnition);
            $form.find('[numberboxname="after_discount_self_ignition"]').val(result.afterDiscountSelfIgnition);
            $form.find('[numberboxname="single_not_deductible_self_ignition"]').val(result.singleNotDeductibleSelfIgnition);
            $form.find('[numberboxname="standard_optional_deductible"]').val(result.standardOptionalDeductible);
            $form.find('[numberboxname="after_discount_optional_deductible"]').val(result.afterDiscountOptionalDeductible);
            $form.find('[numberboxname="standard_not_deductible"]').val(result.standardNotDeductible);
            $form.find('[numberboxname="after_discount_not_deductible"]').val(result.afterDiscountNotDeductible);
            $form.find('[numberboxname="total_standard"]').val(result.totalStandard);
            $form.find('[numberboxname="total_after_discount"]').val(result.totalAfterDiscount);
            $form.find('[numberboxname="total_single_not_deductible"]').val(result.totalSingleNotDeductible);
            $form.find('[numberboxname="business"]').val(result.business);
            $form.find('[numberboxname="gift_money"]').val(result.giftMoney);
            $form.find('[numberboxname="tax_money"]').val(result.taxMoney);

            $('.after-discount-value, .single-not-deductible-value, .insurance-number').each(function(i, n){
                $(n).parent().fadeOut(300).fadeIn(300);
                $(n).numberbox('setValue', $(n).val());
            });

        }

    })(jQuery);
</script>