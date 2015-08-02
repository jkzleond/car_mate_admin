<div id="app_ranks_dg">

</div>

<script type="text/javascript">
    (function($){

        //创建datagrid控件
        $('#app_ranks_dg').datagrid({
            url: '/ranks.json',
            title: 'app排行(<?php echo $latest_up_time; ?>)',
            iconCls: 'icon-save',
            width: 'auto',
            height: 'auto',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            rownumbers:true,///行数
            pageSize:50,///（每页记录数）
            pageNumber:1,///（当前页码）
            pageList:[50,100,150,200],
            columns:[[
                {field:'appId',title:'appId',width:20,align:'right'},
                {field:'Top_Free_Applications_All',title:'总榜',width:15,align:'right'},
                {field:'Top_Free_iPad_Applications_All',title:'iPad-总榜',width:15,align:'right'},
                {field:'Top_Free_Applications_Lifestyle',title:'生活',width:15,align:'right'},
                {field:'Top_Free_iPad_Applications_Lifestyle',title:'iPad-生活',width:15,align:'right'},
                {field:'Top_Free_Applications_Navigation',title:'导航',width:15,align:'right'},
                {field:'Top_Free_iPad_Applications_Navigation',title:'iPad-导航',width:15,align:'right'},
                {field:'Top_Free_Applications_Utilities',title:'工具',width:15,align:'right'},
                {field:'Top_Free_iPad_Applications_Utilities',title:'iPad-工具',width:15,align:'right'},
                {field:'Top_Free_Applications_Travel',title:'旅游',width:15,align:'right'},
                {field:'Top_Free_iPad_Applications_Travel',title:'iPad-旅游',width:15,align:'right'},
                {field:'appName',title:'app名称',width:150,align:'left'}
            ]]
        });

    })(jQuery);
</script>