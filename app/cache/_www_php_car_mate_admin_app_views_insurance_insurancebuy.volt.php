<div class="row-fluid">
    <form action="" method="post" id="insurance_no_form">
        <table class="table" style="font-size:14px;">
            <tr>
                <td bgcolor="#FFDDCC">保单号</td>
                <td bgcolor="#FFDDCC" colspan="5"><!-- #FFC8B4 #FFDDCC -->
                    <input type="text" name="insurance_no" value="<?php echo $info->insuranceNo; ?>" style="width: 250px;"/>
                </td>
            </tr>
            <tr height="25px">
                <td colspan="6"></td>
            </tr>
            <tr>
                <td bgcolor="#FFFFFF">车牌号</td>
                <td bgcolor="#CCEEFF"><?php echo $info->c_hphm; ?></td>
                <td bgcolor="#FFFFFF">车主姓名</td>
                <td bgcolor="#CCEEFF"><?php echo $info->userName; ?></td>
                <td bgcolor="#FFFFFF">联系方式</td>
                <td bgcolor="#CCEEFF"><?php echo $info->phoneNo; ?></td>
            </tr>
            <tr>
                <td bgcolor="#FFFFFF">车架号</td>
                <td bgcolor="#CCEEFF"><?php echo $info->c_frameNumber; ?></td>
                <td bgcolor="#FFFFFF">行驶省</td>
                <td bgcolor="#CCEEFF"><?php echo $info->c_provinceName; ?></td>
                <td bgcolor="#FFFFFF">行驶城市</td>
                <td bgcolor="#CCEEFF"><?php echo $info->c_cityName; ?></td>
            </tr>
            <tr>
                <td bgcolor="#FFFFFF">车价</td>
                <td bgcolor="#CCEEFF"><?php echo $insurance_param->carPrice; ?></td>
                <td bgcolor="#FFFFFF">座位</td>
                <td bgcolor="#CCEEFF"><?php echo $insurance_param->carSeat; ?></td>
                <td bgcolor="#FFFFFF">车主邮箱</td>
                <td bgcolor="#CCEEFF"><?php echo $info->emailAddr; ?></td>
            </tr>
            <tr>
                <td bgcolor="#FFFFFF">整年、月参数</td>
                <td bgcolor="#CCEEFF">
                    <?php echo $insurance_result->roundYear; ?>|<?php echo $insurance_result->roundMonth; ?>
                </td>
                <td bgcolor="#FFFFFF">初登年月</td>
                <td bgcolor="#CCEEFF">
                    <?php echo $insurance_param->firstYear; ?>.<?php echo $insurance_param->firstMonth; ?>
                    <input type="hidden" name="firstYear" value="<?php echo $insurance_param->firstYear; ?>"/>
                    <input type="hidden" name="firstMonth" value="<?php echo $insurance_param->firstMonth; ?>"/>
                </td>
                <td bgcolor="#FFFFFF">起保年月</td>
                <td bgcolor="#CCEEFF">
                    <?php echo $insurance_param->insuranceYear; ?>.<?php echo $insurance_param->insuranceMonth; ?>
                    <input type="hidden" name="insuranceYear" value="<?php echo $insurance_param->insuranceYear; ?>"/>
                    <input type="hidden" name="insuranceMonth" value="<?php echo $insurance_param->insuranceMonth; ?>"/>
                </td>
            </tr>
            <tr height="25px">
                <td bgcolor="#FFFFFF">联系地址</td>
                <td bgcolor="#CCEEFF" colspan="5"><?php echo $info->a_address; ?></td>
            </tr>
            <tr height="25px">
                <td bgcolor="#FFFFFF" colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td bgcolor="#FFDDCC">保险公司及折扣</td>
                <td bgcolor="#FFDDCC" colspan="5">
                    <?php echo $insurance_param->d_companyName; ?>:<?php echo $insurance_param->d_discount; ?>折
                </td>
            </tr>
            <tr height="25px">
                <td bgcolor="#FFFFFF" colspan="6">&nbsp;</td>
            </tr>
            <tr height="30px">
                <td bgcolor="#EFEFEF">险别</td>
                <td bgcolor="#EFEFEF">保险金额</td>
                <td bgcolor="#EFEFEF">标准保费</td>
                <td bgcolor="#EFEFEF">优惠后保费</td>
                <td bgcolor="#EFEFEF" colspan="2">单项不计免赔</td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">交强险</td>
                <td bgcolor="#FFFFFF">
                    <?php echo $insurance_param->compulsoryName; ?>
                </td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardCompulsoryInsurance; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountCompulsoryInsurance; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductibleCompulsoryInsurance; ?></td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">车损险</td>
                <td bgcolor="#FFFFFF">
                    <?php echo $insurance_param->damageName; ?>
                </td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardDamageInsurance; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountDamageInsurance; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductibleDamageInsurance; ?></td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">第三者</td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_param->third; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardThird; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountThird; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductibleThird; ?></td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF" rowspan="2">座位</td>
                <td bgcolor="#FFFFFF">驾驶员（1） : <?php echo $insurance_param->driver; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardDriver; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountDriver; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductibleDriver; ?></td>
            </tr>
            <tr>
                <td bgcolor="#FFFFFF">乘客（<?php echo $insurance_param->carSeat - 1; ?>） : <?php echo $insurance_param->passenger; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardPassenger; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountPassenger; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductiblePassenger; ?></td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">盗抢</td>
                <td bgcolor="#FFFFFF">
                    <?php echo $insurance_param->robberyName; ?>
                </td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardRobbery; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountRobbery; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductibleRobbery; ?></td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">玻璃</td>
                <td bgcolor="#FFFFFF">
                    <?php echo $insurance_param->glassName; ?>
                </td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardGlass; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountGlass; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductibleGlass; ?></td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">划痕</td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_param->scratch; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardScratch; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountScratch; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductibleScratch; ?></td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">自燃</td>
                <td bgcolor="#FFFFFF">
                    <?php echo $insurance_param->selfIgnitionName; ?>
                </td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardSelfIgnition; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountSelfIgnition; ?></td>
                <td bgcolor="#FFFFFF" colspan="2"><?php echo $insurance_result->singleNotDeductibleSelfIgnition; ?></td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">可选免赔额</td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_param->optionalDeductible; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardOptionalDeductible; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountOptionalDeductible; ?></td>
                <td bgcolor="#FFFFFF" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF">不计免赔</td>
                <td bgcolor="#FFFFFF">
                    <?php echo $insurance_param->notDeductibleName; ?>
                </td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->standardNotDeductible; ?></td>
                <td bgcolor="#FFFFFF"><?php echo $insurance_result->afterDiscountNotDeductible; ?></td>
                <td bgcolor="#FFFFFF" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td bgcolor="#EFEFEF" colspan="2" style="text-align: center;">合计</td>
                <td bgcolor="#FFEE99"><?php echo $insurance_result->totalStandard; ?></td>
                <td bgcolor="#FFEE99"><?php echo $insurance_result->totalAfterDiscount; ?></td>
                <td bgcolor="#FFEE99" colspan="2"><?php echo $insurance_result->totalSingleNotDeductible; ?></td>
            </tr>
            <tr>
                <td bgcolor="#FFEE99">商业</td>
                <td bgcolor="#FFEE99"><?php echo $insurance_result->business; ?></td>
                <td bgcolor="#FFEE99">
                    车船税（
                    <?php if ($insurance_param->tax === 0 || $insurance_param->tax === '0') { ?>
                    不
                    <?php } ?>
                    代缴）:
                </td>
                <td bgcolor="#FFEE99">
                    <?php echo $insurance_result->taxMoney; ?>
                </td>
                <td bgcolor="#FFEE99" colspan="2">&nbsp;</td>
            </tr>
            <tr height="30px">
                <td bgcolor="#FFFFFF" colspan="6" style="text-align: center;">
                    <input type="hidden" name="user_id" value="<?php echo $info->userId; ?>"/>
                    <input type="hidden" name="info_id" value="<?php echo $info->id; ?>"/>
                    <?php if ($info->state_id == 4) { ?>
                    <button id="insurance_gen_no_btn" class="btn btn-info"><i class="iconfa-file-alt"></i>生成保单</button>
                    <button id="insurance_issuing_btn" class="btn btn-primary"><i class="iconfa-legal"></i>出单</button>
                    <?php } elseif ($info->state_id == 5) { ?>
                    <button id="insurance_complete" class="btn btn-primary" data-id="<?php echo $info->id; ?>">完成保单</button>
                    <?php } ?>
                </td>
            </tr>
        </table>
    </form>
</div>

<?php if ($info->state_id == 4) { ?>
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
<script text="javascript">
    (function($){
        /**
         * 控件相关
         **/
        //窗口

        var need_destroy_dialogs = $('#insurance_window').data('need_destroy');

        if(!need_destroy_dialogs)
        {
            need_destroy_dialogs = [];
            $('#insurance_window').data('need_destroy', need_destroy_dialogs);
        }

        <?php if ($info->state_id == 4) { ?>

        //出单时间控件
        var issuing_time_box = $('#insurance_issuing_form [name=issuing_time]').datetimebox();
        var actul_amount_box = $('#insurance_issuing_form [name=actul_amount]').numberbox({
            precision: 2,
            groupSeparator: ','
        });

        //出单窗口
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

        /*  事件  */

        //出单按钮事件
        $('#insurance_no_form #insurance_issuing_btn').click(function(event){
            event.preventDefault();
            $('#insurance_issuing_dialog').dialog('open', true).dialog('center');
            return false;
        });

        $('#insurance_no_form #insurance_gen_no_btn').click(function(event){
            event.preventDefault();

            var info_id = $('#insurance_no_form [name=info_id]').val();
            var user_id = $('#insurance_no_form [name=user_id]').val()
            var insurance_no = $('#insurance_no_form [name=insurance_no]').val();

            $.ajax({
                url: '/insuranceGenNo.json',
                method: 'PUT',
                data: {
                    data:{
                        info_id: info_id,
                        user_id: user_id,
                        insurance_no: insurance_no
                    }
                },
                dataType: 'json',
                global: true
            }).done(function(data){
                if(data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '保单生成成功'
                    });
                    $('#insurance_search_btn').click();
                }
            });

            $('#insurance_window').window('close');

            return false;
        });

        <?php } ?>


    })(jQuery);
</script>