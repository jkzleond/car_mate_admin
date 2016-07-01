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
            <th>
                通话清单
            </th>
            <td colspan="7">
                <table class="table" style="margin-bottom:0px;">
                    <tr>
                        <th>时间</th>
                        <th>主叫</th>
                        <th>被叫</th>
                        <th>时长(秒)</th>
                        <th>话费</th>
                        <th>状态</th>
                    </tr>
                    {% set call_count = 0 %}
                    {% set sum_duration = 0 %}
                    {% set sum_bill = 0 %}
                    {% for call_record in call_records %}
                    {% set call_count += 1 %}
                    {% set sum_duration += call_record['duration'] %}
                    {% set sum_bill += call_record['bill'] %}
                    <tr>
                        <td>{{ call_record['start_time'] }}</td>
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
                        <th>通话总次数</th>
                        <td>{{ call_count }}</td>
                        <th>总计时长(秒)</th>
                        <td>{{ sum_duration }}</td>
                        <th>总计话费</th>
                        <td>{{ sum_bill }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>