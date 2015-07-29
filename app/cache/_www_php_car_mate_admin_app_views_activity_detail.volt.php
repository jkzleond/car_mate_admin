<table class="table" border="0" cellspacing="1" style="width:100%">
    <tr>
        <th width="17%">
            <div align="right">
                <strong>微博登记链接：</strong>
            </div>
        </th>
        <td width="33%">http://116.55.248.76:8080/car/userInfo.do?userid=WEIBO_ACCOUNT&aid=<?php echo $activity->id; ?></td>
        <th width="25%">
            <div align="right">
                <strong>后台添加链接：</strong>
            </div>
        </th>
        <td width="25%">
            <a href="http://116.55.248.76:8080/car/userInfo.do?userid=SYSTEM_ACCOUNT&aid=<?php echo $activity->id; ?>" target="_blank">单击此处</a>可以报名
        </td>
    </tr>
    <tr>
        <th width="17%">
            <div align="right">
                <strong>相关链接：</strong>
            </div>
        </th>
        <td width="33%"><?php echo $activity->url; ?></td>
        <th width="25%">
            <div align="right">
                <strong>是否自动生成链接：</strong>
            </div>
        </th>
        <td width="25%">
            <?php if ($activity->autoStart == '1') { ?>
            自动生成链接
            <?php } else { ?>
            不自动生成
            <?php } ?>
        </td>
    </tr>
    <tr>
        <th>
            <div align="right">
                <strong>类型：</strong>
            </div>
        </th>
        <td>
            <?php echo $activity->typeName; ?>
        </td>
        <th>
            <div align="right">
                <strong>创建时间：</strong>
            </div>
        </th>
        <td>
            <?php echo date('Y-m-d H:i:s', strtotime($activity->createDate)); ?>
        </td>
    </tr>
    <tr>
        <th>
            <div align="right">
                <strong> 开始时间： </strong>
            </div>
        </th>
        <td>
            <?php echo date('Y-m-d H:i:s', strtotime($activity->startDate)); ?>
        </td>
        <th>
            <div align="right">
                <strong> 结束时间： </strong>
            </div>
        </th>
        <td>
            <?php echo date('Y-m-d H:i:s', strtotime($activity->endDate)); ?>
        </td>
    </tr>
    <tr>
        <th>
            <div align="right">
                <strong>状态：</strong>
            </div>
        </th>
        <td>
            <?php if ($activity->state == 1) { ?>
            进行中
            <?php } elseif ($activity->state == 2) { ?>
            已过期
            <?php } elseif ($activity->state == 3) { ?>
            未启动
            <?php } ?>
        </td>
        <th>
            <div align="right">
                <strong>是否需要签到：</strong>
            </div>
        </th>
        <td>
            <?php if (empty($activity->needCheckIn)) { ?>
            不需要
            <?php } else { ?>
            需要
            <button class="check-user-view btn btn-info" title="查看签到详情" data-id="<?php echo $activity->id; ?>"><i class="iconfa-user"></i></button>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <th>
            <div align="right"><strong>登记信息</strong></div>
        </th>
        <td colspan="3">
            <?php foreach ($activity->infos as $info) { ?>
                <?php if ($info == 'uname') { ?>
            <span class="label">真实姓名</span>
                <?php } elseif ($info == 'phone') { ?>
            <span class="label">电话/手机</span>
                <?php } elseif ($info == 'idcarno') { ?>
            <span class="label">身份证号</span>
                <?php } elseif ($info == 'pca') { ?>
            <span class="label">省市区</span>
                <?php } elseif ($info == 'address') { ?>
            <span class="label">地址</span>
                <?php } elseif ($info == 'sinaWeibo') { ?>
            <span class="label">新浪微博账号</span>
                <?php } elseif ($info == 'weixin') { ?>
            <span class="label">微信账号</span>
                <?php } elseif ($info == 'hphm') { ?>
            <span class="label">号牌</span>
                <?php } elseif ($info == 'sex') { ?>
            <span class="label">性别</span>
                <?php } elseif ($info == 'people') { ?>
            <span class="label">人数</span>
                <?php } elseif ($info == 'qqNum') { ?>
            <span class="label">QQ号</span>
                <?php } elseif ($info == 'auto') { ?>
            <span class="label">其他信息(<?php echo $activity->option; ?>)</span>
                <?php } elseif ($info == 'select') { ?>
            <span class="label">下拉列表( <?php echo $activity->sname; ?> )</span>
            <select name="" id="">
                    <?php $v6506554608199766842iterator = $activity->optionList; $v6506554608199766842incr = 0; $v6506554608199766842loop = new stdClass(); $v6506554608199766842loop->length = count($v6506554608199766842iterator); $v6506554608199766842loop->index = 1; $v6506554608199766842loop->index0 = 1; $v6506554608199766842loop->revindex = $v6506554608199766842loop->length; $v6506554608199766842loop->revindex0 = $v6506554608199766842loop->length - 1; ?><?php foreach ($v6506554608199766842iterator as $option) { ?><?php $v6506554608199766842loop->first = ($v6506554608199766842incr == 0); $v6506554608199766842loop->index = $v6506554608199766842incr + 1; $v6506554608199766842loop->index0 = $v6506554608199766842incr; $v6506554608199766842loop->revindex = $v6506554608199766842loop->length - $v6506554608199766842incr; $v6506554608199766842loop->revindex0 = $v6506554608199766842loop->length - ($v6506554608199766842incr + 1); $v6506554608199766842loop->last = ($v6506554608199766842incr == ($v6506554608199766842loop->length - 1)); ?>
                <option value="<?php echo $activity->depositList[$v6506554608199766842loop->index0]; ?>"> <?php echo $option; ?></option>
                    <?php $v6506554608199766842incr++; } ?>
            </select>
                <?php } ?>
            <?php } ?>
        </td>
    </tr>
    <?php if ($activity->typeId == 2) { ?>
    <tr class="award">
        <th>
            <div align="right">
                <strong> 抽奖开始时间： </strong>
            </div>
        </th>
        <td>
            <?php echo date('Y-m-d H:i:s', strtotime($activity->awardStart)); ?>
        </td>
        <th>
            <div align="right">
                <strong> 抽奖结束时间： </strong>
            </div>
        </th>
        <td>
            <?php echo date('Y-m-d H:i:s', strtotime($activity->awardEnd)); ?>
        </td>
    </tr>
    <tr class="award">
        <th>
            <div align="right"><strong>抽奖状态：</strong></div>
        </th>
        <td colspan="3" id="awardState">
            <?php if ($activity->awardState == 1) { ?>
            进行中
            <?php } elseif ($activity->awardState == 2) { ?>
            已过期
            <?php } else { ?>
            未启动
            <?php } ?>
        </td>
    </tr>
    <?php } ?>

    <?php if ($activity->needPay == 1) { ?>
    <tr>
        <th><div align="right"><strong>付款金额：</strong></div></th>
        <td><?php echo $activity->deposit; ?></td>
        <th><div align="right"><strong>在线支付人数：</strong></div></th>
        <td>
            <button class="pay-user-view btn btn-info" title="查看在线支付详情" data-id="<?php echo $activity->id; ?>">
                <i class="iconfa-credit-card"></i>
                <?php echo $activity->orderNum; ?>
            </button>
        </td>
    </tr>
    <?php } ?>
</table>