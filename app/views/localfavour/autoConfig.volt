<style type="text/css" rel="stylesheet">

    .auto-config-item {
        position:relative;
        width:240px;
        height:140px;
        margin-left: 5px;
        text-align: center;
        float:left;
    }
    .auto-config-item img {
        width: 100%;
    }
    .auto-config-item .tools {
        display:none;
        position:absolute;
        right: 0px;
        font-size: 30px;
        z-index: 999;
    }
    .auto-config-item .tools.tools-top {
        top: 0px;
    }
    .auto-config-item .tools.tools-bottom {
        bottom: 0px;
    }
    .auto-config-item:hover .tools {
        display: block;
    }

    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">汽车信息校对</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="local_favour_auto_grid"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){

        var custom_view = $.extend({},$.fn.datagrid.defaults.view);

        custom_view.renderRow = function(target, fields, frozen, rowIndex, rowData){
            var html = '<div class="drag-item pull-left">' +
                '<div class="well well-small auto-config-item" data-id="' + rowData.id +'">' +
                '<i class="iconfa-remove tools tools-top tools-unuse"></i>' +
                '<span class="tools tools-bottom">' +
                '<i class="iconfa-edit tools-edit"></i>' +
                '</span>';
            if(rowData.picData && rowData.picData.substring(0, 4) === 'http')
            {
                html += '<img class="pic" src="' + rowData.picData + '" />';
            }
            else if(rowData.urlCode == '200')
            {
                var pic_url = "{{ url('/autoPic') }}";
                html += '<img class="pic" src="' + pic_url + '/' + rowData.id + '" />';
                ;
            }

            html += '<div class="url" style="position: absolute; bottom: 5px; background-color:#eee; width:240px;"><span>' + rowData.urlCode + '</span></div>' +
            '</div>' +
            '</div>';
            return html;
        };

        $('#local_favour_auto_grid').datagrid({
            url: '/autoList.json',
            title: '汽车信息列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 450,
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            view: custom_view
        });
    };

    CarMate.page.on_leave = function(){

    };
</script>