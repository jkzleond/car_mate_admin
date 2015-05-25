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
                <tr height="25px">
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <th>车牌号</th>
                    <td bgcolor="#EEE"><?php echo $info->c_hphm; ?></td>
                    <th>车主姓名</th>
                    <td bgcolor="#EEE"><?php echo $info->userName; ?></td>
                    <th>联系方式</th>
                    <td bgcolor="#EEE"><?php echo $info->phoneNo; ?></td>
                </tr>
                <tr>
                    <th>车架号</th>
                    <td bgcolor="#EEE"><?php echo $info->c_frameNumber; ?></td>
                    <th>行驶省</th>
                    <td bgcolor="#EEE"><?php echo $info->c_provinceName; ?></td>
                    <th>行驶城市</th>
                    <td bgcolor="#EEE"><?php echo $info->c_cityName; ?></td>
                </tr>
                <tr>
                    <th>车价</th>
                    <td bgcolor="#EEE"><?php echo $insurance->carPrice; ?><input type="hidden" name="car_price" value="<?php echo $insurance->carPrice; ?>"/></td>
                    <th>座位</th>
                    <td bgcolor="#EEE"><?php echo $insurance->carSeat; ?><input type="hidden" name="car_seat" value="<?php echo $insurance->carSeat; ?>"/></td>
                    <th>车主邮箱</th>
                    <td bgcolor="#EEE"><?php echo $info->emailAddr; ?></td>
                </tr>
                <tr>
                    <th>整年、月参数</th>
                    <td bgcolor="#EEE">
                        <?php echo $result->roundYear; ?>|<?php echo $result->roundMonth; ?>
                        <input type="hidden" name="round_year" value="<?php echo $result->roundYear; ?>"/>
                        <input type="hidden" name="round_month" value="<?php echo $result->roundMonth; ?>"/>
                    </td>
                    <th>初登年月</th>
                    <td bgcolor="#EEE">
                        <?php echo $insurance->firstYear; ?>.<?php echo $insurance->firstMonth; ?>
                        <input type="hidden" name="first_year" value="<?php echo $insurance->firstYear; ?>"/>
                        <input type="hidden" name="first_month" value="<?php echo $insurance->firstMonth; ?>"/>
                    </td>
                    <th>起保年月</th>
                    <td bgcolor="#EEE">
                        <?php echo $insurance->insuranceYear; ?>.<?php echo $insurance->insuranceMonth; ?>
                        <input type="hidden" name="insurance_year" value="<?php echo $insurance->insuranceYear; ?>"/>
                        <input type="hidden" name="insurance_month" value="<?php echo $insurance->insuranceMonth; ?>"/>
                    </td>
                </tr>
                <tr height="25px">
                    <th>联系地址</th>
                    <td bgcolor="#EEE"><?php echo $info->a_address; ?></td>
                    <th>发动机号</th>
                    <td bgcolor="#EEE"><?php echo $info->c_fdjh; ?></td>
                    <th>品牌型号</th>
                    <td bgcolor="#EEE"><?php echo $info->c_autoname; ?></td>
                </tr>
                <tr>
                    <th>身份证号</th>
                    <td bgcolor="#EEE"><?php echo $info->sfzh; ?></td>
                    <?php if ($info->state_id == 7) { ?>
                    <td >无法精算理由</td>
                    <td bgcolor="#EEE"><?php echo $info->failureReason; ?></td>
                    <?php } ?>
                </tr>
                <tr height="25px">
                    <td  colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <th>保险公司及折扣</th>
                    <td bgcolor="#CCDDEE" colspan="5">
                        <select name="discount_company_id" id="insurance_discount_select_btn" class="input-xlarge">
                            <?php foreach ($discount_list as $discount) { ?>
                                <?php if ($insurance->d_companyId == $discount->companyId) { ?>
                            <option value="<?php echo $discount->companyId; ?>" data-discount="<?php echo $discount->discount; ?>" selected><?php echo $discount->companyName; ?>(折扣:<?php echo $discount->discount; ?>)</option>
                                <?php } else { ?>
                            <option value="<?php echo $discount->companyId; ?>" data-discount="<?php echo $discount->discount; ?>"><?php echo $discount->companyName; ?>(折扣:<?php echo $discount->discount; ?>)</option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr height="25px">
                    <td  colspan="6">&nbsp;</td>
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
                            <?php foreach ($compulsory_state_list as $compulsory_state) { ?>
                                <?php if ($insurance->compulsory_id == $compulsory_state->id) { ?>
                            <option value="<?php echo $compulsory_state->id; ?>" selected><?php echo $compulsory_state->status; ?></option>
                                <?php } else { ?>
                            <option value="<?php echo $compulsory_state->id; ?>"><?php echo $compulsory_state->status; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_compulsory_insurance" id="standard_compulsory_insurance" value="<?php echo number_format($result->standardCompulsoryInsurance, 2); ?>" readonly="readonly"/></td>
                    <td><input class="after-discount-value" data-rel="#standard_compulsory_insurance" name="after_discount_compulsory_insurance" id="after_discount_compulsory_insurance" value="<?php echo number_format($result->afterDiscountCompulsoryInsurance, 2); ?>"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_compulsory_insurance" id="single_not_deductible_compulsory_insurance" value="<?php echo number_format($result->singleNotDeductibleCompulsoryInsurance, 2); ?>"/></td>
                </tr>
                <tr>
                    <th>车损险</th>
                    <td>
                        <select name="damage_id">
                            <?php foreach ($insurance_status_list as $insurance_status) { ?>
                                <?php if ($insurance->damage_id == $insurance_status->id) { ?>
                            <option value="<?php echo $insurance_status->id; ?>" selected><?php echo $insurance_status->status; ?></option>
                                <?php } ?>
                            <option value="<?php echo $insurance_status->id; ?>"><?php echo $insurance_status->status; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td ><input class="insurance-number-disabled" name="standard_damage_insurance" id="standard_damage_insurance" value="<?php echo number_format($result->standardDamageInsurance, 2); ?>" readonly="readonly"/></td>
                    <td ><input class="after-discount-value" data-rel="#standard_damage_insurance" name="after_discount_damage_insurance" id="after_discount_damage_insurance" value="<?php echo number_format($result->afterDiscountDamageInsurance, 2); ?>"/></td>
                    <td  colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_damage_insurance" id="single_not_deductible_damage_insurance" value="<?php echo number_format($result->singleNotDeductibleDamageInsurance, 2); ?>"/></td>
                </tr>
                <tr>
                    <th>第三者</th>
                    <td ><input class="insurance-number-int" type="text" name="third" value="<?php echo number_format($insurance->third, 2); ?>"/></td>
                    <td ><input class="insurance-number-disabled" name="standard_third" id="standard_third" value="<?php echo number_format($result->standardThird, 2); ?>" readonly="readonly"/></td>
                    <td ><input class="after-discount-value" data-rel="#standard_third" name="after_discount_third" id="after_discount_third" value="<?php echo number_format($result->afterDiscountThird, 2); ?>"/></td>
                    <td  colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_third" id="single_not_deductible_third" value="<?php echo number_format($result->singleNotDeductibleThird, 2); ?>"/></td>
                </tr>
                <tr>
                    <th rowspan="2">座位</th>
                    <td>驾驶员（1）: <input class="insurance-number-int" name="driver" value="<?php echo number_format($insurance->driver, 2); ?>"/></td>
                    <td><input class="insurance-number-disabled" name="standard_driver" id="standard_driver" value="<?php echo number_format($result->standardDriver, 2); ?>" readonly="readonly"/></td>
                    <td><input class="after-discount-value" data-rel="#standard_driver" name="after_discount_driver" id="after_discount_driver" value="<?php echo number_format($result->afterDiscountDriver, 2); ?>"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_driver" id="single_not_deductible_driver" value="<?php echo number_format($result->singleNotDeductibleDriver, 2); ?>"/></td>
                </tr>
                <tr>
                    <td>乘客（<?php echo $insurance->carSeat - 1; ?>） : <input class="insurance-number-int" name="passenger" value="<?php echo number_format($insurance->passenger, 2); ?>"/></td>
                    <td><input class="insurance-number-disabled" name="standard_passenger" id="standard_passenger" value="<?php echo number_format($result->standardPassenger, 2); ?>" readonly="readonly"/></td>
                    <td><input class="after-discount-value" data-rel="#standard_passenger" name="after_discount_passenger" id="afterDiscountPassenger" value="<?php echo number_format($result->afterDiscountPassenger, 2); ?>"/></td>
                    <td colspan="2"><input   class="single-not-deductible-value" name="single_not_deductible_passenger" id="single_not_deductible_passenger" value="<?php echo number_format($result->singleNotDeductiblePassenger, 2); ?>"/></td>
                </tr>
                <tr>
                    <th>盗抢</th>
                    <td >
                        <select name="robbery_id">
                            <?php foreach ($insurance_status_list as $insurance_status) { ?>
                                <?php if ($insurance->robbery_id == $insurance_status->id) { ?>
                            <option value="<?php echo $insurance_status->id; ?>" selected><?php echo $insurance_status->status; ?></option>
                                <?php } else { ?>
                            <option value="<?php echo $insurance_status->id; ?>"><?php echo $insurance_status->status; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_robbery" id="standard_robbery" value="<?php echo number_format($result->standardRobbery, 2); ?>" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_robbery" name="after_discount_robbery" id="after_discount_robbery" value="<?php echo number_format($result->afterDiscountRobbery, 2); ?>"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_robbery" id="single_not_deductible_robbery" value="<?php echo number_format($result->singleNotDeductibleRobbery, 2); ?>"/></td>
                </tr>
                <tr>
                    <th>玻璃</th>
                    <td>
                        <select name="glass_id">
                            <?php foreach ($glass_state_list as $glass_state) { ?>
                                <?php if ($insurance->glass_id == $glass_state->id) { ?>
                            <option value="<?php echo $glass_state->id; ?>" selected><?php echo $glass_state->status; ?></option>
                                <?php } else { ?>
                            <option value="<?php echo $glass_state->id; ?>"><?php echo $glass_state->status; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_glass" id="standard_glass" value="<?php echo number_format($result->standardGlass, 2); ?>" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_glass" name="after_discount_glass" id="after_discount_glass" value="<?php echo number_format($result->afterDiscountGlass, 2); ?>"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_glass" id="single_not_deductible_glass" value="<?php echo number_format($result->singleNotDeductibleGlass, 2); ?>"/></td>
                </tr>
                <tr>
                    <th>划痕</th>
                    <td><input class="insurance-number" type="text" name="scratch" id="scratch" value="<?php echo number_format($insurance->scratch, 2); ?>"/></td>
                    <td><input class="insurance-number-disabled" name="standard_scratch" id="standard_scratch" value="<?php echo number_format($result->standardScratch, 2); ?>" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_scratch" name="after_discount_scratch" id="after_discount_scratch" value="<?php echo number_format($result->afterDiscountScratch, 2); ?>"/></td>
                    <td  colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_scratch" id="single_not_deductible_scratch" value="<?php echo number_format($result->singleNotDeductibleScratch, 2); ?>"/></td>
                </tr>
                <tr>
                    <th>自燃</th>
                    <td>
                        <select name="self_ignition_id">
                            <?php foreach ($insurance_status_list as $insurance_status) { ?>
                                <?php if ($insurance->selfIgnition_id == $insurance_status->id) { ?>
                            <option value="<?php echo $insurance_status->id; ?>" selected><?php echo $insurance_status->status; ?></option>
                                <?php } else { ?>
                            <option value="<?php echo $insurance_status->id; ?>"><?php echo $insurance_status->status; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_self_ignition" id="standard_self_ignition" value="<?php echo number_format($result->standardSelfIgnition, 2); ?>" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_self_ignition" name="after_discount_self_ignition" id="after_discount_self_ignition" value="<?php echo number_format($result->afterDiscountSelfIgnition, 2); ?>"/></td>
                    <td colspan="2"><input class="single-not-deductible-value" name="single_not_deductible_self_ignition" id="single_not_deductible_self_ignition" value="<?php echo number_format($result->singleNotDeductibleSelfIgnition, 2); ?>"/></td>
                </tr>
                <tr>
                    <th>可选免赔额</th>
                    <td><input class="insurance-number" type="text" name="optional_deductible" id="optional_deductible" value="<?php echo number_format($insurance->optionalDeductible, 2); ?>"/></td>
                    <td><input class="insurance-number-disabled" name="standard_optional_deductible" id="standard_optional_deductible" value="<?php echo number_format($result->standardOptionalDeductible, 2); ?>" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_optional_deductible" name="after_discount_optional_deductible" id="after_discount_optional_deductible" value="<?php echo number_format($result->afterDiscountOptionalDeductible, 2); ?>"/></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <th>不计免赔</th>
                    <td >
                        <select name="not_deductible_id">
                            <?php foreach ($insurance_status_list as $insurance_status) { ?>
                                <?php if ($insurance->notDeductible_id == $insurance_status->id) { ?>
                            <option value="<?php echo $insurance_status->id; ?>" selected><?php echo $insurance_status->status; ?></option>
                                <?php } else { ?>
                            <option value="<?php echo $insurance_status->id; ?>"><?php echo $insurance_status->status; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input class="insurance-number-disabled" name="standard_not_deductible" id="standard_not_deductible" value="<?php echo number_format($result->standardNotDeductible, 2); ?>" readonly="readonly"/></td>
                    <td><input  class="after-discount-value" data-rel="#standard_not_deductible" name="after_discount_not_deductible" id="after_discount_not_deductible" value="<?php echo number_format($result->afterDiscountNotDeductible, 2); ?>"/></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: center;">合计</th>
                    <td bgcolor="#FFEE99"><input class="insurance-number-disabled" name="total_standard" id="total_standard" value="<?php echo number_format($result->totalStandard, 2); ?>" readonly="readonly"/></td>
                    <td bgcolor="#FFEE99"><input name="total_after_discount" id="total_after_discount" value="<?php echo number_format($result->totalAfterDiscount, 2); ?>"/></td>
                    <td bgcolor="#FFEE99" colspan="2"><input name="total_single_not_deductible" id="total_single_not_deductible" value="<?php echo number_format($result->totalSingleNotDeductible, 2); ?>"/></td>
                </tr>
                <tr>
                    <th>商业</th>
                    <td bgcolor="#FFEE99"><input class="insurance-number" name="business" value="<?php echo number_format($result->business, 2); ?>"/></td>
                    <td bgcolor="#FFEE99">
                        车船税（
                        <?php if ($insurance->tax === 0 || $insurance->tax === '0') { ?>
                        不
                        <?php } ?>
                        代缴）:
                    </td>
                    <td bgcolor="#FFEE99">
                        <input class="insurance-number" type="text" name="tax_money" value="<?php echo number_format($result->taxMoney, 2); ?>"/>
                    </td>
                    <td bgcolor="#FFEE99" colspan="2">
                        排量：<?php if ($insurance->displacement) { ?> <?php echo $insurance->displacement; ?> <?php } ?>
                    </td>
                </tr>
                <tr height="30px">
                    <td  colspan="3" style="text-align: center;">
                        <input type="hidden" name="user_id" value="<?php echo $info->userId; ?>"/>
                        <input type="hidden" name="info_id" value="<?php echo $info->id; ?>"/>
                        <input type="hidden" name="result_id" value="<?php echo $result->id; ?>"/>

                        <?php if ($info->state_id == 2 || $info->state_id == 7) { ?>
                        <input type="submit" id="insurance_result_update_btn" class="btn btn-warning" value="提交精算结果" style="width: 250px"/>
                        <?php } elseif ($info->state_id == 3) { ?>
                        <input type="submit" id="insurance_result_update_btn" class="btn btn-warning" value="提交精算结果" style="width: 250px"/>
                        <input type="submit" id="insurance_issuing_btn" class="btn btn-primary" value="出单" style="width: 250px"/>
                        <?php } ?>
                    </td>
                    <?php if ($info->state_id == 2 || $info->state_id == 3 || $info->state_id == 4 || $info->state_id == 7) { ?>
                    <td  colspan="3">
                        <input type="button" id="insurance_unupdate_btn" data-id="<?php echo $result->id; ?>" value="无法精算" class="reason-btn btn btn-danger" style="width: 250px"/>
                    </td>
                    <?php } else { ?>
                    <td  colspan="3">
                    </td>
                    <?php } ?>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php if ($info->state_id == 2 || $info->state_id == 3 || $info->state_id == 4 || $info->state_id == 7) { ?>
<div id="insurance_reason_dialog">
    <form action="" id="insurance_reason_form">
        <input type="hidden" name="id" value="<?php echo $info->id; ?>"/>
        <div class="row-fluid">
            <span class="label">理由</span>
            <textarea name="reason" rows="5" cols="40"></textarea>
        </div>
    </form>
</div>
<?php } ?>

<?php if ($info->state_id == 3) { ?>
<div id="insurance_issuing_dialog">
    <form action="" id="insurance_issuing_form">
        <input type="hidden" id="" name="id" value="<?php echo $info->id; ?>"/>
        <input type="hidden" id="" name="user_id" value="<?php echo $info->userId; ?>"/>
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
<?php } ?>

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

        
        <?php if ($info->state_id == 2 || $info->state_id == 3 || $info->state_id == 4 || $info->state_id == 7) { ?>
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

        //事件
        $('#insurance_unupdate_btn').click(function(event){
            event.preventDefault();
            $('#insurance_reason_dialog').dialog('open', true).dialog('center');
            return false;
        });

        <?php } ?>

        <?php if ($info->state_id == 3) { ?>

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

        <?php } ?>

        <?php if ($info->state_id == 2 || $info->state_id == 3 || $info->state_id == 7) { ?>
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
        <?php } ?>

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

    })(jQuery);
</script>