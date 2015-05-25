<table class="table" border="0" cellspacing="1" style="width:100%">
    <tr>
        <th width="17%">
            <div align="right">
                <strong>微博登记链接：</strong>
            </div>
        </th>
        <td width="33%">http://116.55.248.76:8080/car/userInfo.do?userid=WEIBO_ACCOUNT&aid={{ activity.id }}</td>
        <th width="25%">
            <div align="right">
                <strong>后台添加链接：</strong>
            </div>
        </th>
        <td width="25%">
            <a href="http://116.55.248.76:8080/car/userInfo.do?userid=SYSTEM_ACCOUNT&aid={{ activity.id }}" target="_blank">单击此处</a>可以报名
        </td>
    </tr>
    <tr>
        <th width="17%">
            <div align="right">
                <strong>相关链接：</strong>
            </div>
        </th>
        <td width="33%">{{ activity.url }}</td>
        <th width="25%">
            <div align="right">
                <strong>是否自动生成链接：</strong>
            </div>
        </th>
        <td width="25%">
            {% if activity.autoStart == '1' %}
            自动生成链接
            {% else %}
            不自动生成
            {% endif %}
        </td>
    </tr>
    <tr>
        <th>
            <div align="right">
                <strong>类型：</strong>
            </div>
        </th>
        <td>
            {{ activity.typeName }}
        </td>
        <th>
            <div align="right">
                <strong>创建时间：</strong>
            </div>
        </th>
        <td>
            {{ activity.createDate | uniform_time }}
        </td>
    </tr>
    <tr>
        <th>
            <div align="right">
                <strong> 开始时间： </strong>
            </div>
        </th>
        <td>
            {{ activity.startDate | uniform_time }}
        </td>
        <th>
            <div align="right">
                <strong> 结束时间： </strong>
            </div>
        </th>
        <td>
            {{ activity.endDate | uniform_time }}
        </td>
    </tr>
    <tr>
        <th>
            <div align="right">
                <strong>状态：</strong>
            </div>
        </th>
        <td>
            {% if activity.state == 1%}
            进行中
            {% elseif activity.state == 2 %}
            已过期
            {% elseif activity.state == 3 %}
            未启动
            {% endif %}
        </td>
        <th>
            <div align="right">
                <strong>是否需要签到：</strong>
            </div>
        </th>
        <td>
            {% if activity.needCheckIn is empty%}
            不需要
            {% else %}
            需要
            <button class="check-user-view btn btn-info" title="查看签到详情" data-id="{{ activity.id }}"><i class="iconfa-user"></i></button>
            {% endif %}
        </td>
    </tr>
    <tr>
        <th>
            <div align="right"><strong>登记信息</strong></div>
        </th>
        <td colspan="3">
            {% for info in activity.infos %}
                {% if info == 'uname' %}
            <span class="label">真实姓名</span>
                {% elseif info == 'phone'%}
            <span class="label">电话/手机</span>
                {% elseif info == 'idcarno' %}
            <span class="label">身份证号</span>
                {% elseif info == 'pca' %}
            <span class="label">省市区</span>
                {% elseif info == 'address' %}
            <span class="label">地址</span>
                {% elseif info == 'sinaWeibo' %}
            <span class="label">新浪微博账号</span>
                {% elseif info == 'weixin' %}
            <span class="label">微信账号</span>
                {% elseif info == 'hphm' %}
            <span class="label">号牌</span>
                {% elseif info == 'sex' %}
            <span class="label">性别</span>
                {% elseif info == 'people' %}
            <span class="label">人数</span>
                {% elseif info == 'qqNum' %}
            <span class="label">QQ号</span>
                {% elseif info == 'auto' %}
            <span class="label">其他信息({{ activity.option }})</span>
                {% elseif info == 'select' %}
            <span class="label">下拉列表( {{ activity.sname }} )</span>
            <select name="" id="">
                    {% for option in activity.optionList %}
                <option value="{{ activity.depositList[loop.index0] }}"> {{ option }}</option>
                    {% endfor %}
            </select>
                {% endif %}
            {% endfor %}
        </td>
    </tr>
    {% if activity.typeId == 2 %}
    <tr class="award">
        <th>
            <div align="right">
                <strong> 抽奖开始时间： </strong>
            </div>
        </th>
        <td>
            {{ activity.awardStart | uniform_time }}
        </td>
        <th>
            <div align="right">
                <strong> 抽奖结束时间： </strong>
            </div>
        </th>
        <td>
            {{ activity.awardEnd | uniform_time }}
        </td>
    </tr>
    <tr class="award">
        <th>
            <div align="right"><strong>抽奖状态：</strong></div>
        </th>
        <td colspan="3" id="awardState">
            {% if activity.awardState == 1 %}
            进行中
            {% elseif activity.awardState == 2 %}
            已过期
            {% else %}
            未启动
            {% endif %}
        </td>
    </tr>
    {% endif %}

    {% if activity.needPay == 1 %}
    <tr>
        <th><div align="right"><strong>付款金额：</strong></div></th>
        <td>{{ activity.deposit }}</td>
        <th><div align="right"><strong>在线支付人数：</strong></div></th>
        <td>
            <button class="pay-user-view btn btn-info" title="查看在线支付详情" data-id="{{ activity.id }}">
                <i class="iconfa-credit-card"></i>
                {{ activity.orderNum }}
            </button>
        </td>
    </tr>
    {% endif %}
</table>