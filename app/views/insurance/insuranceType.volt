<style type="text/css" rel="stylesheet">
    .cursor-cross{
        cursor:crosshair;
    }
    .link {
        text-shadow: 2px 3px 2px;
    }
    .link-parent {
        color:orangered;
    }
    .link-parent:hover {
        color:orangered;
    }
    .link-parent:focus {
        color:orangered;
    }
    .link-child {
        color:skyblue;
    }
    .link-child:hover {
        color:skyblue;
    }
    .link-child:focus {
        color:skyblue;
    }
    .link-available {
        color: #BBD150;
    }
    .link-available:hover {
        color: #BBD150;
    }
    .link-available:focus {
        color: #BBD150;
    }
    .link-inavailable {
        color: red;
    }
    .link-inavailable:hover {
        color: red;
    }
    .link-inavailable:focus {
        color: red;
    }
    .link-already {
        color: orange;
    }
    .link-already:hover {
        color: orange;
    }
    .link-already:focus {
        color: orange;
    }
    .top {
        z-index: 999;
    }
    #insurance_type_company_section .item{
        position:relative;
        width:220px;
        height:220px;
        margin-left: 5px;
        text-align: center;
        float:left;
        cursor:pointer;
    }
    #insurance_type_company_section .company-box .tools {
        display:none;
        position:absolute;
        right: 0px;
        font-size: 20px;
        z-index: 999;
    }
    #insurance_type_company_section .company-box .tools.tools-top {
        top: 0px;
    }
    #insurance_type_company_section .company-box .tools.tools-bottom {
        bottom: 0px;
    }
    #insurance_type_company_section .company-box:hover .tools {
        display: block;
    }
    .add-company:hover, .tools:hover {
        color:#0866c6;
    }
    div.datagrid * {
        vertical-align: middle;
    }

    .insurance-type-field-add-parts-btn {
        display:none;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">险种管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="insurance_type_grid_tb">
                    <div class="row-fluid" id="insurance_type_search_bar">
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">类目</span>
                                <select name="cate_id" class="input">
                                </select>
                            </div>
                            <div class="span4">
                                <span class="label">保险公司</span>
                                <select name="company_id" class="input">
                                </select>
                            </div>
                            <div class="span4">
                                <span class="label">名称</span>
                                <input type="text" name="name" class="input">
                                </select>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">价格计算方式</span>
                                <select name="price_type" class="input-mini">
                                    <option value="">全部</option>
                                    <option value="1">人工核算</option>
                                    <option value="2">固定价格</option>
                                    <option value="3">脚本计算</option>
                                </select>
                            </div>
                            <div class="span4">
                                <span class="label">上架状态</span>
                                <select name="is_push" class="input-mini">
                                    <option value="">全部</option>
                                    <option value="0">未上架</option>
                                    <option value="1">已上架</option>
                                </select>
                            </div>
                            <div class="span4">
                                <button class="btn btn-primary" id="insurance_type_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="btn-group">
                                <button class="btn" id="insurance_type_add_btn"><i class="iconfa-plus"></i>添加</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="insurance_type_grid"></div>
            </div>
        </div>
        <div id="insurance_type_cu_dialog">
            <form id="insurance_type_cu_form" action="">
                <div id="insurance_type_cu_form_tabs">
                    <div title="基本信息">
                        <input name="id" type="hidden"/>
                        <div class="control-group">
                            <span class="label">类目</span>
                            <div class="controls">
                                <select name="cate" multiple></select>
                            </div>
                        </div>
                        <div class="control-group">
                            <span class="label">名称</span>
                            <div class="controls">
                                <input name="name" type="text"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <span class="label">价格计算方式</span>
                            <div class="controls">
                                <select name="price_type">
                                    <option value="1">人工核算</option>
                                    <option value="2">固定价格</option>
                                    <option value="3">脚本计算</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group insurance-type-price" style="display:none">
                            <span class="label">价格</span>
                            <div class="controls">
                                <input name="price" type="number" class="input-number"/>
                            </div>
                        </div>
                        <div class="row-fluid insurance-type-script" style="display:none">
                            <span class="label">保费算式脚本</span>
                            <div class="controls">
                                <textarea name="script" cols="50" rows="10" class="input-xxlarge"></textarea>
                            </div>
                        </div>
                        <div class="control-group">
                            <span class="label">描述</span>
                            <div class="controls">
                                <textarea id="insurance_type_des" name="des"></textarea>
                            </div>
                        </div>    
                    </div>
                    <div title="表单字段">
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="btn-toolbar">
                                    <div class="btn-group">
                                        <button class="insurance-type-add-field-btn btn"><i class="iconfa-plus"></i>添加字段</button>
                                        <button id="insurance_type_field_links_btn" class="btn"><i class="iconfa-link"></i>字段关联</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div id="insurance_type_field_container" class="well well-small form-horizontal">
                            </div>
                        </div>
                    </div>
                    <div title="保险公司及优惠">
                        <div id="insurance_type_company_section">
                            <div id="insurance_type_company_container" style="float:left">
                                
                            </div>
                            <div class="insurance-type-add-company item well well-small" style="text-align: center; vertical-align: middle; line-height:220px">
                                <i class="iconfa-plus" style="font-size:40px;margin-top: 40px"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="insurance_type_field_window">
        <div id="insurance_type_field_grid"></div>  
    </div>
    <div id="insurance_type_field_cu_window">
        <form id="insurance_type_field_cu_form" class="form-horizontal">
            <input type="hidden" name="id">
            <div class="control-group">
                <div class="control-label">名称</div>
                <div class="controls">
                    <input type="text" name="name">
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">英文名称</div>
                <div class="controls">
                    <input type="text" name="ename">
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">字段类型</div>
                <div class="controls">
                    <select name="type">
                        <option value="1" selected>文本输入框</option>
                        <option value="2">数字输入框</option>
                        <option value="3">日期时间输入框</option>
                        <option value="4">日期输入框</option>
                        <option value="5">选择框</option>
                        <option value="6">单选项</option>
                        <option value="7">多选框</option>
                        <option value="8">文本域</option>
                        <option value="9">只读文本</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">可选值</div>
                <div class="controls">
                    <textarea name="values" rows="8" disabled></textarea>
                    <div><i style="color:orange">格式:</i>文本:实际值,每行为一个选项</div>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">默认值</div>
                <div class="controls">
                    <input type="text" name="default">
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">描述</div>
                <div class="controls">
                    <textarea name="des"></textarea>
                </div>
            </div>
        </form>
    </div>
    <div id="insurance_type_field_links_window">
        <div class="row-fluid" id="insurance_type_field_links_container"></div>
        <div class="row-fluid" style="text-align:center">
            <button class="btn row-fluid" id="insurance_type_field_add_link_btn"><i class="iconfa-plus"></i></button>
        </div>
    </div>
    <div id="insurance_type_company_window">
        <div id="insurance_type_company_grid"></div>  
    </div>
    <div id="insurance_type_cate_tree_context_menu">
        <div class="insurance-type-cate-memu-add" data-options="name:'add',iconCls:'icon-plus'">添加</div>
        <div class="insurance-type-cate-memu-edit" data-options="name:'edit',iconCls:'icon-edit'">编辑</div>
        <div class="insurance-type-cate-memu-remove" data-options="name:'remove',iconCls:'icon-trash'">删除</div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */
        
        //CKeditor
        //创建ckeditor
        var des_editor = CKEDITOR.replace( 'insurance_type_des', {
            enterMode: CKEDITOR.ENTER_P,
            height: 270,
            removePlugins : 'save'
            //filebrowserImageUploadUrl : 'ckUploadImage?command=QuickUpload&type=Images'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";
        //集成ckfinder
        CKFinder.setupCKEditor(des_editor, finder_path);

        //数字控件
        // $('.input-number').numberbox({
        //     min: 0,
        //     precision: 2
        // });
        // 
        
        //combogrid
        var company_combogrid = $('#insurance_type_search_bar [name=company_id]').combogrid({
            url: 'insuranceCompanyList.json',
            title: '保险公司列表',
            width: 240,
            panelWidth: 300,
            panelHeight: 350,
            idField: 'companyId',
            textField: 'companyName',
            fitColumns: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            columns: [[
                {field:'shortName',title:'简称',width:'20%',align:'center'},
                {field:'companyName', title:'全称', width:'75%', align:'center'},
            ]]
        });

        //tabs
        var insurance_type_cu_form_tabs = $('#insurance_type_cu_form_tabs').tabs({
            border: false
        });
        
         //contextmenu
         var cate_tree_menu = $('#insurance_type_cate_tree_context_menu').menu({
            onClick: function(item){
                var tree = cate_combotree.combotree('tree');
                var tree_selected = tree.tree('getSelected');

                if(item.name == 'add')
                {
                    tree.tree('append',{
                        parent:tree_selected.target,
                        data:[{
                            text: '新节点',
                            attributes: {is_new: true}
                        }]
                    });
                    var children = tree.tree('getChildren', tree_selected.target);
                    var new_node = children[children.length - 1];
                    tree.tree('beginEdit', new_node.target);
                }
                else if(item.name == 'edit')
                {
                    tree_selected.attributes = tree_selected.attributes || {};
                    tree_selected.attributes.old_name = tree_selected.text;
                    tree.tree('beginEdit', tree_selected.target);
                }
                else if(item.name == 'remove')
                {
                    var cate_data = {
                        id: tree_selected.id
                    };

                    delInsuranceCategory(cate_data, function(data){
                        if(!data.success)
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '险种类目删除失败'
                            });
                        }
                        else
                        {
                            tree.tree('remove', tree_selected.target);
                            $.messager.show({
                                title: '系统消息',
                                msg: '险种类目删除成功'
                            });
                        }
                    });
                }
            }
         });

        //combotree
        var search_cate_combotree = $('#insurance_type_grid_tb [name="cate_id"]').combotree({
            url: '/insurance/insuranceCategoryList.json',
            cascadeCheck: false,
            //onlyLeafCheck: true,
            loadFilter: function(data, parent){
                if( (parent == null || typeof parent == 'undefinded') && data.rows) //初始数据
                {
                    var root = {
                        text: '全部', 
                        children: []
                    };
                    var node_map = {};
                    $.each(data.rows, function(i, n){
                        var node = node_map[n.id];

                        var p_node = n.pid ? node_map[n.pid] : root;
                        
                        if(!node)
                        {
                            node = node_map[n.id] = {
                                id: n.id,
                                text: n.name
                            };
                        }
                        else
                        {
                            //node的子节点先被遍历到的情况, 这时候node已存在于node_map,并且具有children数组,但无id,text
                            node.id = n.id;
                            node.text = n.name;
                        }

                        if(p_node)
                        {
                            p_node.children || (p_node.children = []);
                            p_node.children.push(node);
                        }
                        else
                        {
                            //node的父节点还没被遍历到
                            node_map[n.pid] = {children:[node]};
                        }
                    });

                    return [root];
                }
                else
                {
                    return data;
                }
            }
        });

        var cate_combotree = $('#insurance_type_cu_form [name="cate"]').combotree({
            url: '/insurance/insuranceCategoryList.json',
            requried: true,
            dnd: true,
            checkbox: true,
            cascadeCheck: false,
            //onlyLeafCheck: true,
            loadFilter: function(data, parent){
                if( (parent == null || typeof parent == 'undefinded') && data.rows) //初始数据
                {
                    var root = {
                        text: '类目', 
                        children: []
                    };
                    var node_map = {};
                    $.each(data.rows, function(i, n){
                        var node = node_map[n.id];

                        var p_node = n.pid ? node_map[n.pid] : root;
                        
                        if(!node)
                        {
                            node = node_map[n.id] = {
                                id: n.id,
                                text: n.name
                            };
                        }
                        else
                        {
                            //node的子节点先被遍历到的情况, 这时候node已存在于node_map,并且具有children数组,但无id,text
                            node.id = n.id;
                            node.text = n.name;
                        }

                        if(p_node)
                        {
                            p_node.children || (p_node.children = []);
                            p_node.children.push(node);
                        }
                        else
                        {
                            //node的父节点还没被遍历到
                            node_map[n.pid] = {children:[node]};
                        }
                    });

                    return [root];
                }
                else
                {
                    return data;
                }
            },
            onBeforeCheck: function(node, check){
                if(!node.id) return false;
            },
            onContextMenu: function(event, node){
                event.preventDefault();
                
                //node为虚拟的root节点,这里为类目
                if(!node.id) 
                {
                    cate_tree_menu.menu('disableItem', $('.insurance-type-cate-memu-edit'));
                    cate_tree_menu.menu('disableItem', $('.insurance-type-cate-memu-remove'));
                }
                else
                {
                    cate_tree_menu.menu('enableItem', $('.insurance-type-cate-memu-edit'));
                    cate_tree_menu.menu('enableItem', $('.insurance-type-cate-memu-remove'));   
                }

                cate_combotree.combotree('tree').tree('select', node.target);
                cate_tree_menu.menu('show', {
                    left: event.pageX,
                    top: event.pageY
                });
                return false;
            },
            onAfterEdit: function(node){
                var tree = cate_combotree.combotree('tree');
                var cate_data = null;
                if(node.attributes && node.attributes.is_new)
                {
                    var parent_node = tree.tree('getParent', node.target);
                    var pid = parent_node ? tree.tree('getParent', node.target).id : null;
                    cate_data = {
                        name: node.text,
                        pid: pid
                    };
                    addInsuranceCategory(cate_data, function(data){
                        if(!data.success)
                        {
                            tree.tree('remove', node.target);
                            $.messager.show({
                                title: '系统消息',
                                msg: '险种类目添加失败'
                            });
                        }
                        else
                        {
                            node.id = data.new_id;
                            $.messager.show({
                                title: '系统消息',
                                msg: '险种类目添加成功'
                            });
                        }
                    });
                }
                else
                {
                    cate_data = {
                        id: node.id,
                        name: node.text
                    };

                    updateInsuranceCategory(cate_data, function(data){
                        if(!data.success)
                        {
                            tree.tree('update', {
                                target: node.target,
                                text: node.attributes.old_name
                            });

                            $.messager.show({
                                title: '系统消息',
                                msg: '险种类目编辑失败'
                            });
                        }
                        else
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '险种类目编辑成功'
                            });
                        }
                    });
                }
            },
            onBeforeDrag: function(node){
                var tree = cate_combotree.combotree('tree');
                var parent = tree.tree('getParent', node.target);
                var before_node_id = null;
                var after_node_id = null;
                if(parent)
                {
                    var p_children = parent.children;

                    var index = p_children.indexOf(node);

                    if(index != 0)
                    {
                        before_node_id = p_children[index - 1].id;
                    }
                    else if(index == 0 && index != p_children.length - 1)
                    {
                        after_node_id = p_children[index + 1].id;
                    }   
                }

                node.attributes || (node.attributes = {});
                node.attributes.old_parent = parent;
                node.attributes.old_before_node_id = before_node_id;
                node.attributes.old_after_node_id = after_node_id;
            },
            onDrop: function(target, source, point){
                var tree = cate_combotree.combotree('tree');
                var new_parent = null;
                var new_p_children = null;
                var new_order = null;

                var cate_data = {
                    id: source.id
                };

                var index = null;

                if(point == 'append')
                {
                    new_parent = tree.tree('getNode', target);
                }
                else
                {
                    new_parent = tree.tree('getParent', target);
                }
                new_p_children = new_parent.children;
                cate_data.display_order = new_p_children.indexOf(source) + 1;
                cate_data.pid = new_parent.id;

                var old_parent = source.attributes.old_parent;
                var before_node_id = source.attributes.old_before_node_id;
                var after_node_id = source.attributes.old_after_node_id;
                var node_data = tree.tree('getData', source.target);

                updateInsuranceCategory(cate_data, function(data){
                    if(!data.success)
                    {
                        if(!before_node_id && !after_node_id)
                        {
                            tree.tree('append',{
                                parent: old_parent ? old_parent.target : null,
                                data: node_data
                            });
                        }
                        else if(before_node_id)
                        {
                            var before_node = tree.tree('find', before_node_id);
                            tree.tree('pop', source.target);
                            tree.tree('insert', {
                                after: before_node ? before_node.target : null,
                                data: node_data
                            });
                        }
                        else if(after_node_id)
                        {
                            console.log('has_after');
                            var after_node = tree.tree('find', after_node_id);
                            tree.tree('pop', source.target);
                            tree.tree('insert', {
                                before: after_node ? after_node.target : null,
                                data: node_data
                            });
                        }

                        $.messager.show({
                            title: '系统消息',
                            msg: '保险险种类目更新失败'
                        });
                    }
                    else
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '保险险种类目更新成功'
                        });
                    }
                });
            }
        });
    
        //窗口
        var insurance_type_cu_dialog = $('#insurance_type_cu_dialog').dialog({
            title: '险种添加',
            iconCls: 'icon-plus',
            width: '80%',
            height: '80%',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            onBeforeClose: function()
            {
                insurance_type_cu_form_tabs.tabs('select', 0);
                cate_combotree.combotree('clear');
                des_editor.setData('');
                $('#insurance_type_cu_form [name="id"]').val('');
                $('#insurance_type_cu_form [name="name"]').val('');
                $('#insurance_type_cu_form [name="price_type"]').val(1).change();
                $('#insurance_type_cu_form [name="price"]').val('');
                $('#insurance_type_cu_form [name="script"]').val('');
                $('#insurance_type_field_container').empty();
                $('#insurance_type_field_links_container').empty();
                $('#insurance_type_company_container').empty();

                insurance_type_cu_dialog.data('locks', 0);

                //清除关联操作状态
                is_linking = false;
                link_source_field_id = null;
            },
            buttons: [
                {
                    text: '添加',
                    iconCls: 'icon-ok',
                    handler: function(){

                        var win_locks = insurance_type_cu_dialog.data('locks');

                        if(win_locks > 0)
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '窗口处于锁定状态,数据加载完毕后自动解锁'
                            });

                            return;
                        }
                        
                        var btn_state = $('#insurance_type_cu_dialog').data('state');

                        var type_data = {};

                        type_data.cates = cate_combotree.combotree('getValues');
                        type_data.name = $('#insurance_type_cu_form [name="name"]').val();
                        type_data.price_type = $('#insurance_type_cu_form [name="price_type"]').val();
                        type_data.price = $('#insurance_type_cu_form [name="price"]').val();
                        type_data.script = $('#insurance_type_cu_form [name="script"]').val();
                        type_data.des = des_editor.getData();
                        type_data.fields = [];

                        $('#insurance_type_field_container [data-field-id]').each(function(i, n){
                            var field_id = $(n).attr('data-field-id');
                            var default_visible = $(n).attr('data-field-default-visible');
                            var default_disabled = $(n).attr('data-field-default-disabled');
                            var links = [];

                            $('#insurance_type_field_links_container .insurance-type-field-link-item').each(function(i, n){
                                if( $(n).find('.field').val() != field_id ) return;

                                var link_type = $(n).find('.field-link-type').val();
                                var link_value = null;
                                if(link_type == 1)
                                {
                                    link_value = $(n).find('.field-link-value.link-with-value').val();
                                }
                                else if(link_type == 2)
                                {
                                    link_value = $(n).find('.field-link-value.link-with-enable').val();
                                }
                                else if(link_type == 3)
                                {
                                    link_value = $(n).find('.field-link-value.link-with-visible').val();
                                }

                                var link_to_type = $(n).find('.field-link-to-type').val();
                                var link_to_value = null;

                                if(link_to_type == 1)
                                {
                                    link_to_value = $(n).find('.field-link-to-value.link-with-value').val();
                                }
                                else if(link_to_type == 2)
                                {
                                    link_to_value = $(n).find('.field-link-to-value.link-with-enable').val();
                                }
                                else if(link_to_type == 3)
                                {
                                    link_to_value = $(n).find('.field-link-to-value.link-with-visible').val();
                                }

                                links.push({
                                    link_to_id: $(n).find('.link-to-field').val(),
                                    link_type: link_type,
                                    link_value: link_value,
                                    link_to_type: link_to_type,
                                    link_to_value: link_to_value
                                });
                            });

                            type_data.fields.push({
                                id: field_id,
                                links: links,
                                default_visible: default_visible,
                                default_disabled: default_disabled
                            });
                        });

                        type_data.companise = [];

                        $('#insurance_type_company_container [data-company-id]').each(function(i, n){
                            var company_id = $(n).attr('data-company-id');
                            var discount_type = $('.insurance-type-company-discount[data-id="' + company_id + '"]').val();
                            var discount_percent = $('.insurance-type-company-discount-percent[data-id="' + company_id + '"]').val();
                            var discount_setting = $('.insurance-type-company-discount-setting[data-id="' + company_id + '"]').val();

                            type_data.companise.push({
                                id: company_id,
                                discount_type: discount_type,
                                discount_percent: discount_percent,
                                discount_setting: discount_setting
                            });
                        });
                        
                        if ( btn_state == 'update' )
                        {
                            type_data.id = $('#insurance_type_cu_form [name="id"]').val();
                            updateInsuranceType(type_data, function(data){
                                if(!data.success)
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '险种更新失败'
                                    });
                                }
                                else
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '险种更新成功'
                                    });
                                    insurance_type_grid.datagrid('reload');
                                }

                            });
                        }
                        else if ( btn_state == 'create' )
                        {
                            addInsuranceType(type_data, function(data){
                                if(!data.success)
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '险种添加失败'
                                    });
                                }
                                else
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '险种添加成功'
                                    });
                                    insurance_type_grid.datagrid('reload');
                                }
                            });
                        }

                        $('#insurance_type_cu_dialog').dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#insurance_type_cu_dialog').dialog('close');
                    }
                }
            ]
        });

        //字段列表窗口
        var field_window = $('#insurance_type_field_window').window({
            title: '字段(双击数据行可将字段添加到险种)',
            iconCls: 'icon-list',
            width: '80%',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //字段表单窗口
        var field_cu_window = $('#insurance_type_field_cu_window').dialog({
            title: '字段添加',
            iconCls: 'icon-plus',
            width: '600',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            onClose: function(){
                //关闭窗口后,需要清空表单
                $('#insurance_type_field_cu_form [name="id"]').val('');
                $('#insurance_type_field_cu_form [name="name"]').val('');
                $('#insurance_type_field_cu_form [name="ename"]').val('');
                $('#insurance_type_field_cu_form [name="type"]').val('1').change();
                $('#insurance_type_field_cu_form [name="values"]').val('');
                $('#insurance_type_field_cu_form [name="default"]').val('');
                $('#insurance_type_field_cu_form [name="des"]').val('');
            },
            buttons: [
                {
                    text: '添加',
                    iconCls: 'icon-ok',
                    handler: function(){
                        var btn_state = field_cu_window.data('state');

                        var field_data = {};

                        field_data.name = $('#insurance_type_field_cu_form [name="name"]').val();
                        field_data.ename = $('#insurance_type_field_cu_form [name="ename"]').val();
                        field_data.type = $('#insurance_type_field_cu_form [name="type"]').val();
                        field_data.values = $('#insurance_type_field_cu_form [name="values"]').val();
                        field_data.default = $('#insurance_type_field_cu_form [name="default"]').val();
                        field_data.des = $('#insurance_type_field_cu_form [name="des"]').val();

                        if ( btn_state == 'update' )
                        {
                            field_data.id = $('#insurance_type_field_cu_form [name="id"]').val();
                            updateField(field_data, function(data){
                                if(!data.success)
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '字段更新失败'
                                    });
                                }
                                else
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '字段更新成功'
                                    });
                                    field_grid.datagrid('reload');
                                }
                            });
                        }
                        else if ( btn_state == 'create' )
                        {
                            addField(field_data, function(data){
                                if(!data.success)
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '字段添加失败'
                                    });
                                }
                                else
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '字段添加成功'
                                    });
                                    field_grid.datagrid('reload');
                                }
                            });
                        }

                        field_cu_window.dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        field_cu_window.dialog('close');
                    }
                }
            ]
        });

        //字段关联设置窗口
        var field_links_window = $('#insurance_type_field_links_window').window({
            title: '险种表单字段关联设置',
            iconCls: 'icon-wrench',
            width: 900,
            height: 500,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });
        
        //保险公司窗口
        var insurance_type_company_window = $('#insurance_type_company_window').window({
            title: '保险公司(双击数据行可将字段添加到险种)',
            iconCls: 'icon-list',
            width: 600,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //险种列表
        var insurance_type_grid = $('#insurance_type_grid').datagrid({
            url: '/insurance/insuranceTypeList.json',
            title: '险种列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            idField: 'id',
            toolbar: '#insurance_type_grid_tb',
            columns:[[
                {field:'cates', title:'类目', width:'30%', align:'center'},
                {field:'name', title:'名称', width:'20%', align:'center'},
                {field:'field_num', title:'字段数', width:'15%', align:'center'},
                {field:'companise', title:'保险公司', width:'15%', align:'center'},
                {field:'price',title:'价格',width:'10%',align:'center', formatter: function(value, row, index){
                    if(row.price_type == 1)
                    {
                        return '人工核算';
                    }
                    else if(row.price_type == 3)
                    {
                        return '脚本计算';
                    }

                    return value;
                }},
                {field:'id',title:'操作',width:'12%',align:'center', formatter: function(value, row, index){
                    var extra_btn = '';
                    if(row.is_push == 1)
                    {
                        extra_btn = '<button class="btn btn-primary insurance-type-unpush-btn" data-id="' + value + '" title="下架"><i class="iconfa-circle"></i></button>';
                    }
                    else
                    {
                        extra_btn = '<button class="btn btn-primary insurance-type-push-btn" data-id="' + value + '" title="上架"><i class="iconfa-circle-blank"></i></button>';   
                    }

                    return '<div class="btn-group">' + extra_btn + '<button class="btn btn-warning insurance-type-edit-btn" data-id="' + value + '"><i class="icon-edit"></i></button><button class="btn btn-danger insurance-type-del-btn" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        //字段列表
        var field_grid = $('#insurance_type_field_grid').datagrid({
            url: '/field/fieldList.json',
            title: '字段列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            idField: 'id',
            onDblClickRow: function(index, row){
                row.default_visible = 1;
                row.default_disabled = 0;
                renderInsuranceTypeField(row);
            },
            toolbar: [{
                text: '添加',
                iconCls: 'icon-plus',
                handler: function(){
                    //设置窗口状态,并打开
                    field_cu_window.data('state', 'create');

                    var opt = field_cu_window.dialog('options');
                    opt.title = '字段添加';
                    opt.iconCls = 'icon-plus';
                    opt.buttons[0].text = '添加';
                    field_cu_window.dialog(opt).dialog('open').dialog('center');
                }
            }],
            columns:[[
                {field:'name', title:'名称', width:'15%', align:'center'},
                {field:'ename', title:'英文名称', width:'10%', align:'center'},
                {field:'type',title:'类型',width:'15%',align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '文本输入框';
                    }
                    else if(value == 2)
                    {
                        return '数字输入框';
                    }
                    else if(value == 3)
                    {
                        return '日期时间输入框';
                    }
                    else if(value == 4)
                    {
                        return '日期输入框';
                    }
                    else if(value == 5)
                    {
                        return '选择框';
                    }
                    else if(value == 6)
                    {
                        return '单选项';                     
                    }
                    else if(value == 7)
                    {
                        return '多选框';
                    }
                    else if(value == 8)
                    {
                        return '文本域';
                    }
                    else if(value == 9)
                    {
                        return '只读文本';
                    }
                }},
                {field:'values',title:'可选值',width:'15%',align:'center', formatter: function(value, row, index){
                    return '<pre>' + value + '</pre>';
                }},
                {field:'default',title:'默认值',width:'10%',align:'center'},
                {field:'des',title:'说明',width:'15%',align:'center'},
                {field:'id',title:'操作',width:'12%',align:'center', formatter: function(value, row, index){
                    return '<div class="btn-group"><button class="btn btn-warning insurance-type-field-edit-btn" data-id="' + value + '"><i class="icon-edit"></i></button><button class="btn btn-danger insurance-type-field-del-btn" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        //保险公司列表
        var company_grid = $('#insurance_type_company_grid').datagrid({
            url: '/insuranceCompanyList.json',
            title: '字段列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            idField: 'id',
            onDblClickRow: function(index, row){
                renderInsuranceTypeCompany(row);
            },
            columns:[[
                {field:'companyName', title:'名称', width:'50%', align:'center'},
                {field:'ename', title:'英文名称', width:'45%', align:'center'},
            ]]
        });

        /**
         * 事件相关
         */
        //险种查找按钮点击事件
        $('#insurance_type_search_btn').click(function(event){
            event.preventDefault();
            var criteria = {};
            criteria.cate_id = search_cate_combotree.combotree('getValue');
            criteria.company_id = company_combogrid.combogrid('getValue');
            criteria.name = $('#insurance_type_search_bar [name="name"]').val();
            criteria.price_type = $('#insurance_type_search_bar [name="price_type"]').val();
            criteria.is_push = $('#insurance_type_search_bar [name="is_push"]').val();
            insurance_type_grid.datagrid('reload', {criteria: criteria});
            return false;
        });
        //险种添加按钮点击事件
        $('#insurance_type_add_btn').click(function(event){
            event.preventDefault();
            //设置窗口状态,并打开
            $('#insurance_type_cu_dialog').data('state', 'create');

            var opt = $('#insurance_type_cu_dialog').dialog('options');
            opt.title = '险种添加';
            opt.iconCls = 'icon-plus';
            opt.buttons[0].text = '添加';
            $('#insurance_type_cu_dialog').dialog(opt).dialog('open').dialog('center');
            return false;
        });

        //险种编辑按钮点击事件
        $(document).on('click', '.insurance-type-edit-btn', function(event){
            event.preventDefault();
            var win_locks = 0;
            insurance_type_cu_dialog.data('locks', win_locks);
            var ts = Date.now();
            insurance_type_cu_dialog.data('timestamp', ts);
            var id = $(this).attr('data-id');
            
            var index = insurance_type_grid.datagrid('getRowIndex', id);
            var row = insurance_type_grid.datagrid('getRows')[index];

            $('#insurance_type_cu_form [name="id"]').val(row.id);
            $('#insurance_type_cu_form [name="name"]').val(row.name);
            $('#insurance_type_cu_form [name="price_type"]').val(row.price_type).change();
            $('#insurance_type_cu_form [name="price"]').val(row.price);
            $('#insurance_type_cu_form [name="script"]').val(row.script);

            des_editor.setData(row.des);

            insurance_type_cu_dialog.data('locks', ++win_locks);
            cate_combotree.combotree('disable', true);
            getInsuranceTypeCategory({id: id}, function(resp){
                //窗口时间戳验证
                var cur_ts = insurance_type_cu_dialog.data('timestamp');
                if(cur_ts != ts) return;
                
                renderInsuranceTypeCategory(resp.rows);
                cate_combotree.combotree('enable', true);
                insurance_type_cu_dialog.data('locks', --win_locks);
            });

            insurance_type_cu_dialog.data('locks', ++win_locks);
            $('.insurance-type-add-field-btn').prop('disabled', true);
            getInsuranceTypeField({id: id}, function(resp){
                var cur_ts = insurance_type_cu_dialog.data('timestamp');
                if(cur_ts != ts) return;

                $.each(resp.rows, function(i,n){
                    n.is_old = true;
                    renderInsuranceTypeField(n);
                });

                var fields = [];
                $('#insurance_type_field_container .field-item').each(function(i, n){
                    fields.push({
                        id: $(n).attr('data-field-id'),
                        name: $(n).attr('data-field-name')
                    });
                });

                $.each(resp.rows, function(i,n){
                    if(!n.links) return;
                    var links = JSON.parse(n.links);
                    var len = links.length;
                    var data = null;
                    for(var i = 0; i < len; i++)
                    {
                        data = links[i];
                        data.id = n.id;
                        data.fields = fields;
                        renderInsuranceTypeFieldLink(data);
                    }
                });

                $('.insurance-type-add-field-btn').prop('disabled', false);
                insurance_type_cu_dialog.data('locks', --win_locks);
            });

            insurance_type_cu_dialog.data('locks', ++win_locks);
            $('.insurance-type-add-company').hide();
            getInsuranceTypeCompany({id: id}, function(resp){
                var cur_ts = insurance_type_cu_dialog.data('timestamp');
                if(cur_ts != ts) return;

                $.each(resp.rows, function(i,n){
                    n.is_old = true;
                    renderInsuranceTypeCompany(n);
                });
                $('.insurance-type-company-discount').change();
                $('.insurance-type-add-company').show();
                insurance_type_cu_dialog.data('locks', --win_locks);
            });

            //改变窗口状态
            $('#insurance_type_cu_dialog').data('state', 'update');

            var opt = insurance_type_cu_dialog.dialog('options');
            opt.title = '险种编辑';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '更新';
            insurance_type_cu_dialog.dialog(opt).dialog('open').dialog('center');

            return false;
        });

        //险种删除按钮点击事件
        $(document).on('click', '.insurance-type-del-btn', function(event){
            event.preventDefault();
            var id = $(this).attr('data-id');
            
            $.messager.confirm('删除险种', '确认删除险种?', function(is_ok){
                if(!is_ok) return;
                delInsuranceType({id:id}, function(data){
                    insurance_type_grid.datagrid('reload');
                });
            });

            return false;
        });

        //险种上架按钮点击事件
        $(document).on('click', '.insurance-type-push-btn', function(event){
            event.preventDefault();
            var id = $(this).attr('data-id');
            updateInsuranceType({id:id, is_push:1}, function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '险种上架失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '险种上架成功'
                    });
                    insurance_type_grid.datagrid('reload');
                }
            });
            return false;
        });

        //险种上架按钮点击事件
        $(document).on('click', '.insurance-type-unpush-btn', function(event){
            event.preventDefault();
            var id = $(this).attr('data-id');
            updateInsuranceType({id:id, is_push:0}, function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '险种下架失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '险种下架成功'
                    });
                    insurance_type_grid.datagrid('reload');
                }
            });
            return false;
        });

        //价格计算方式选择框改变事件
        $('#insurance_type_cu_form [name="price_type"]').change(function(event){
            var value = $(this).val();

            $('.insurance-type-price').hide();
            $('.insurance-type-script').hide();

            if(value == '2')
            {
                $('.insurance-type-price').show();
            }
            else if(value == '3')
            {
                $('.insurance-type-script').show();
            }
        });

        //添加字段按钮点击事件
        $('.insurance-type-add-field-btn').click(function(event){
            event.preventDefault();
            field_window.window('move',{left:event.pageX + 50,top:event.pageY + 50})
                        .window('open'); 
            return false;
        });

        //编辑字段按钮点击事件
        $(document).on('click', '.insurance-type-field-edit-btn', function(event){

            var field_id = $(this).attr('data-id');
            var index = field_grid.datagrid('getRowIndex', field_id);
            var rows = field_grid.datagrid('getRows');
            var field = rows[index];

            $('#insurance_type_field_cu_form [name="id"]').val(field.id);
            $('#insurance_type_field_cu_form [name="name"]').val(field.name);
            $('#insurance_type_field_cu_form [name="ename"]').val(field.ename);
            $('#insurance_type_field_cu_form [name="type"]').val(field.type).change();
            $('#insurance_type_field_cu_form [name="values"]').val(field.values);
            $('#insurance_type_field_cu_form [name="default"]').val(field.default);
            $('#insurance_type_field_cu_form [name="des"]').val(field.des);

            field_cu_window.data('state', 'update');
            var opt = field_cu_window.dialog('options');
            opt.title = '字段更新';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '更新';
            field_cu_window.dialog(opt).dialog('open').dialog('center');
        });

        //删除字段按钮点击事件
        $(document).on('click', '.insurance-type-field-del-btn', function(event){
            var field_id = $(this).attr('data-id');

            $.messager.confirm('提示', '删除字段将使与之相关联的项失效,如险种,确定删除吗？', function(is_ok){
                if(!is_ok) return;
                delField({id: field_id}, function(data){
                    if(!data.success)
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '字段删除失败'
                        });
                    }
                    else
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '字段删除成功'
                        });

                        field_grid.datagrid('reload');
                    }
                });
            });
        });

        //字段类型选择框改变事件
        $('#insurance_type_field_cu_form [name="type"]').change(function(event){
            var value = $(this).val();
            if(value == '1' || value == '2' || value == '3' || value == '4' || value == '8' || value == '9')
            {
                $('#insurance_type_field_cu_form [name="values"]').prop('disabled', true);
            }
            else
            {
                $('#insurance_type_field_cu_form [name="values"]').prop('disabled', false);   
            }
        });

        //字段关联按钮点击事件
        $('#insurance_type_field_links_btn').click(function(event){
            event.preventDefault();
            field_links_window.window('open').window('center');
            return false;
        });

        //添加字段关联项按钮点击事件
        var link_item_tpl = _.template($('#insurance_type_field_link_tpl').html(), {variable: 'data'});
        $('#insurance_type_field_add_link_btn').click(function(event){
            event.preventDefault();
            var fields = [];
            $('#insurance_type_field_container .field-item').each(function(i, n){
                fields.push({
                    id: $(n).attr('data-field-id'),
                    name: $(n).attr('data-field-name')
                });
            });
            $('#insurance_type_field_links_container').append(link_item_tpl({
                fields: fields
            }));
            return false;
        });

        //field-link-to-type选择框改变事件
        $(document).on('change', '#insurance_type_field_links_container .field-link-to-type', function(event){

            var value = $(this).val();
            var $item = $(this).parents('.insurance-type-field-link-item');

            $item.find('.field-link-to-value').hide();
            if( value == 1)
            {
                $item.find('.field-link-to-value.link-with-value').show();
            }
            else if( value == 2)
            {
                $item.find('.field-link-to-value.link-with-enable').show();
            }
            else if( value == 3)
            {
                $item.find('.field-link-to-value.link-with-visible').show();
            }
        });

        //移除字段关联项按钮点击事件
        $(document).on('click', '.insurance-type-field-link-remove-btn', function(event){
            event.preventDefault();
            $(this).parents('.insurance-type-field-link-item').remove();
            return false;
        });

        //添加组件按钮点击事件(表单字段)
        $(document).on('click', '.insurance-type-field-add-parts-btn', function(event){
            event.preventDefault();
            //TODO 添加组件代码需要完善, 记得去了css里面.insurance-type-field-add-parts-btn的display:none
            return false;
        });

        //字段显隐按钮点击事件
        $(document).on('click', '.insurance-type-field-default-visible-btn', function(event){
            event.preventDefault();
            var field_id = $(this).attr('data-id');
            var field_default_visible = $(this).attr('data-default-visible');
            var $field_box = $('#insurance_type_field_container [data-field-id="' + field_id +'"]');

            if(field_default_visible == '0')
            {
                $field_box.attr('data-field-default-visible', '1');
                $(this).attr('data-default-visible', '1');
                $(this).attr('title', '默认显示');
                $(this).find('i.iconfa-eye-close').attr('class', 'iconfa-eye-open');             
            }
            else
            {
                $field_box.attr('data-field-default-visible', '0');
                $(this).attr('data-default-visible', '0');
                $(this).attr('title', '默认隐藏');
                $(this).find('i.iconfa-eye-open').attr('class', 'iconfa-eye-close');              
            }

            return false;
        });

        //字段禁启按钮点击事件
        $(document).on('click', '.insurance-type-field-default-disabled-btn', function(event){
            event.preventDefault();
            var field_id = $(this).attr('data-id');
            var field_default_disabled = $(this).attr('data-default-disabled');
            var $field_box = $('#insurance_type_field_container [data-field-id="' + field_id +'"]');

            if(field_default_disabled == '0')
            {
                $field_box.attr('data-field-default-disabled', '1');
                $(this).attr('data-default-disabled', '1');
                $(this).attr('title', '默认启用');
                $(this).find('i.iconfa-unlock').attr('class', 'iconfa-lock');             
            }
            else
            {
                $field_box.attr('data-field-default-disabled', '0');
                $(this).attr('data-default-disabled', '0');
                $(this).attr('title', '默认禁用');
                $(this).find('i.iconfa-lock').attr('class', 'iconfa-unlock');              
            }
            return false;
        });

        //移除字段按钮点击事件
        $(document).on('click', '.insurance-type-field-remove-btn', function(event){
            event.preventDefault();
            var id = $(this).attr('data-id');
            var $field_group = $('#insurance_type_field_container [data-field-id="' + id +'"]');
            $field_group.remove();

            //同时移除相关的关联项
            $('#insurance_type_field_links_container .insurance-type-field-link-item .field').each(function(i, n){
                if($(n).val() == id) $(n).parents('.insurance-type-field-link-item').remove();
            });

            $('#insurance_type_field_links_container .insurance-type-field-link-item .link-to-field').each(function(i, n){
                if($(n).val() == id) $(n).parents('.insurance-type-field-link-item').remove();
            });

            return false;
        })

        //添加保险公司按钮点击事件
        $(document).on('click', '.insurance-type-add-company', function(event){
            event.preventDefault();
            insurance_type_company_window.window('open').window('center');
            return false;
        });

        //优惠方式选择框改变事件
        $(document).on('change', '.insurance-type-company-discount', function(event){
            var value = $(this).val();
            var data_id = $(this).attr('data-id');
            var $company_box = $('.insurance_type_company_container [data-company-id="' + data_id + '"]');
            var state = $company_box.attr('data-company-state');

            if(state == 'old')
            {
                $company_box.attr('data-company-state', 'update');                
            }

            $('.insurance-type-discount-setting-secton[data-id="' + data_id +'"]').hide();
            $('.insurance-type-discount-percent-secton[data-id="' + data_id +'"]').hide();

            if(value == '1')
            {
                $('.insurance-type-discount-percent-secton[data-id="' + data_id +'"]').show();
            }
            else if(value == '2')
            {
                $('.insurance-type-discount-setting-secton[data-id="' + data_id +'"]').show();
            }
        });

        //移除保险公司按钮点击事件
        $(document).on('click', '.insurance-type-company-remove-btn', function(event){
            var data_id = $(this).attr('data-id');
            var $company_box = $('.company-box[data-company-id="' + data_id +'"]');
            
            $company_box.remove();
        });

        /**
         * 数据相关
         */

        //添加保险类目
        function addInsuranceCategory(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceCategory.json',
                method: 'POST',
                data: {creates:[data]},
                dataType: 'json',
                global: true
            }).done(callback);
        }

        //更新保险类目
        function updateInsuranceCategory(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceCategory/' + data.id +'.json',
                method: 'PUT',
                data: {updates:[data]},
                dataType: 'json',
                global: true
            }).done(callback);
        }

        function delInsuranceCategory(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceCategory/' + data.id + '.json',
                method: 'DELETE',
                dataType: 'json',
                global: true
            }).done(callback);
        }

        function addField(data, callback)
        {
            $.ajax({
                url: '/field/field.json',
                method: 'POST',
                data: {creates:[data]},
                dataType: 'json',
                global: true
            }).done(callback);   
        }

        function updateField(data, callback)
        {
            $.ajax({
                url: '/field/field/' + data.id + '.json',
                method: 'PUT',
                data: {updates: [data]},
                dataType: 'json',
                global: true
            }).done(callback);
        }

        function delField(data, callback)
        {
            $.ajax({
                url: '/field/field/' + data.id + '.json',
                method: 'DELETE',
                dataType: 'json',
                global: true
            }).done(callback);
        }

        function addInsuranceType(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceType.json',
                method: 'POST',
                data: {creates:[data]},
                dataType: 'json',
                global: true
            }).done(callback);
        }

        function updateInsuranceType(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceType/' + data.id + '.json',
                method: 'PUT',
                data: {updates: [data]},
                dataType: 'json',
                global: true
            }).done(callback);
        }

        function delInsuranceType(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceType/' + data.id + '.json',
                method: 'DELETE',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '删除险种失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '删除险种成功'
                    });
                    if(typeof callback == 'function') callback(data);
                }
            });
        }

        function getInsuranceTypeCategory(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceType/' + data.id + '/categoryList.json',
                method: 'GET',
                dataType: 'json',
                global: true
            }).done(callback);
        }

        function getInsuranceTypeField(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceType/' + data.id + '/fieldList.json',
                method: 'GET',
                dataType: 'json',
                global: true
            }).done(callback);    
        }

        function getInsuranceTypeCompany(data, callback)
        {
            $.ajax({
                url: '/insurance/insuranceType/' + data.id + '/companyList.json',
                method: 'GET',
                dataType: 'json',
                global: true
            }).done(callback);    
        }

        function renderInsuranceTypeCategory(data)
        {
            var values = [];
            $.each(data, function(i, n){
                values.push(n.id)
            });
            cate_combotree.combotree('setValues', values);
        }

        function renderInsuranceTypeField(data)
        {
            var $field_group = $('#insurance_type_field_container [data-field-id="' + data.id + '"]');
            
            var state = $field_group.attr('data-field-state');

            //是否是旧(在编辑模式中以存在,但被临时移除的)字段
            if(state == 'remove')
            {
                $field_group.attr('data-field-state', 'old').show();
            }
            else if(state == 'new' || state == 'old')
            {
                return;
            }

            var tpl = null;
            if(data.type == 1)
            {
                tpl = _.template($('#insurance_type_field_input_text_tpl').html());
            }
            else if(data.type == 2)
            {
                tpl = _.template($('#insurance_type_field_input_number_tpl').html());
            }
            else if(data.type == 3)
            {
                tpl = _.template($('#insurance_type_field_input_datetime_tpl').html());
            }
            else if(data.type == 4)
            {
                tpl = _.template($('#insurance_type_field_input_date_tpl').html());
            }
            else if(data.type == 5)
            {
                tpl = _.template($('#insurance_type_field_select_tpl').html());
            }
            else if(data.type == 6)
            {
                tpl = _.template($('#insurance_type_field_radio_tpl').html());
            }
            else if(data.type == 7)
            {
                tpl = _.template($('#insurance_type_field_checkbox_tpl').html());
            }
            else if(data.type == 8)
            {
                tpl = _.template($('#insurance_type_field_textarea_tpl').html());   
            }
            else if(data.type == 9)
            {
                tpl = _.template($('#insurance_type_field_readonly_text_tpl').html());   
            }
            else
            {
                return;
            }

            $('#insurance_type_field_container').append( tpl({data:data}) );
        }

        function renderInsuranceTypeFieldLink(data)
        {
            $('#insurance_type_field_links_container').append(link_item_tpl(data));
        }

        function renderInsuranceTypeCompany(data)
        {
            var $company_box = $('#insurance_type_company_container [data-company-id="' + data.companyId + '"]');
            
            var state = $company_box.attr('data-company-state');

            //是否是旧(在编辑模式中以存在,但被临时移除的)公司
            if(state == 'remove')
            {
                $company_box.attr('data-field-state', 'old').show();
            }
            else if(state == 'new' || state == 'old')
            {
                return;
            }

            var tpl = _.template($('#insurance_type_company_tpl').html());
            $('#insurance_type_company_container').append( tpl({data:data}) );
        }

        //页面离开时事件
        CarMate.page.on_leave = function(){
            //销毁控件
            cate_tree_menu.menu('destroy');
            cate_combotree.combotree('destroy');
            search_cate_combotree.combotree('destroy');
            company_combogrid.combogrid('destroy');

            //销毁窗口
            $('#insurance_type_cu_dialog').dialog('destroy');
            field_window.window('destroy');
            field_cu_window.dialog('destroy');
            field_links_window.window('destroy');
            insurance_type_company_window.window('destroy');

            //清除动态绑定事件
            
            $(document).off('click', '.insurance-type-edit-btn');
            $(document).off('click', '.insurance-type-del-btn');
            $(document).off('click', '.insurance-type-push-btn');
            $(document).off('click', '.insurance-type-unpush-btn');
            $(document).off('click', '.insurance-company-edit-btn');
            $(document).off('click', '.insurance-company-del-btn');

            $(document).off('click', '.insurance-type-field-edit-btn');
            $(document).off('click', '.insurance-type-field-del-btn');
            $(document).off('click', '.insurance-type-field-add-parts-btn');
            $(document).off('click', '.insurance-type-field-linkto-btn');
            $(document).off('click', '.insurance-type-field-default-disabled-btn');
            $(document).off('click', '.insurance-type-field-default-visible-btn');
            $(document).off('click', '.insurance-type-field-remove-btn');
            $(document).off('click', '.insurance-type-field-link-remove-btn');
            $(document).off('change', '#insurance_type_field_links_container .field-link-to-type');
            $(document).off('click', '.insurance-type-add-company');
            $(document).off('click', '.insurance-type-company-remove-btn');
            $(document).off('change', '.insurance-type-company-discount');
        }
    };

</script>
<script type="text/plain" id="insurance_type_field_input_text_tpl">
    <%
        var state = 'new';
        if(data.is_old)
        {
            state = 'old';
        }
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>" >
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
            <div class="row-fluid">
                <div class="span6">
                    <input type="text" value="<%=data.default%>">
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/plain" id="insurance_type_field_input_number_tpl">
    <%
        var state = 'new';
        if(data.is_old)
        {
            state = 'old';
        }
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>">
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
            <div class="row-fluid">
                <div class="span6">
                    <input type="number" value="<%=data.default%>" class='.number-field'>
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/plain" id="insurance_type_field_input_datetime_tpl">
    <%
        var state = 'new';
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>">
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
            <div class="row-fluid">
                <div class="span6">
                    <input type="datetime" value="<%=data.default%>" class='.datetime-field'>
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/plain" id="insurance_type_field_input_date_tpl">
    <%
        var state = 'new';
        if(data.is_old)
        {
            state = 'old';
        }
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>">
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
            <div class="row-fluid">
                <div class="span6">
                    <input type="date" class='.date-field'>
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/plain" id="insurance_type_field_select_tpl">
    <%
        var state = 'new';
        if(data.is_old)
        {
            state = 'old';
        }
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>">
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
        <div class="row-fluid">
                <div class="span6">
                    <select>
                        <%
                            var value_arr = data.values.replace(/\s+$/, '').split("\n");  
                            var len = value_arr.length;
                            var text_value = null;
                            var text = null;
                            var value = null;
                        %>
                        <% for (var i = 0; i < len; i++) { %>
                            <%
                                text_value = value_arr[i].split(':');
                                text = text_value[0];
                                value = text_value[1];
                            %>
                            <% if(value == data.default){ %>
                            <option value="<%=value%>" selected><%=text%>:<%=value%></option>
                            <% }else{ %>
                            <option value="<%=value%>"><%=text%>:<%=value%></option>
                            <% } %>
                        <% } %>
                    </select>
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/plain" id="insurance_type_field_radio_tpl">
    <%
        var state = 'new';
        if(data.is_old)
        {
            state = 'old';
        }
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>">
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
            <div class="row-fluid">
                <div class="span6">
                <%
                    var value_arr = data.values.replace(/\s+$/, '').split("\n");  
                    var len = value_arr.length;
                    var text_value = null;
                    var text = null;
                    var value = null;
                %>
                <% for (var i = 0; i < len; i++) { %>
                    <%
                        text_value = value_arr[i].split(':');
                        text = text_value[0];
                        value = text_value[1];
                    %>
                    <% if(value == data.default){ %>
                    <input type="radio" name="<%=data.id%>" value="<%=value%>" checked/><%=text%>:<%=value%>
                    <% }else{ %>
                    <input type="radio" name="<%=data.id%>" value="<%=value%>"/><%=text%>:<%=value%>
                    <% } %>
                <% } %>
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/plain" id="insurance_type_field_checkbox_tpl">
    <%
        var state = 'new';
        if(data.is_old)
        {
            state = 'old';
        }
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>">
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
            <div class="row-fluid">
                <div class="span6">
                <%
                    var value_arr = data.values.replace(/\s+$/, '').split("\n");  
                    var default_arr = data.default.replace(/\s+$|,+$/, '').split(',');
                    var len = value_arr.length;
                    var text_value = null;
                    var text = null;
                    var value = null;
                %>
                <% for (var i = 0; i < len; i++) { %>
                    <%
                        text_value = value_arr[i].split(':');
                        text = text_value[0];
                        value = text_value[1];
                    %>
                    <% if(default_arr.indexOf(value) != -1){ %>
                    <label style="font-size: 1em"><input type="checkbox" name="<%=data.id%>" value="<%=value%>" checked/><%=text%>:<%=value%></label>
                    <% }else{ %>
                    <laebl style="font-size: 1em"><input type="checkbox" name="<%=data.id%>" value="<%=value%>"/><%=text%>:<%=value%></label>
                    <% } %>
                <% } %>
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/plain" id="insurance_type_field_textarea_tpl">
    <%
        var state = 'new';
        if(data.is_old)
        {
            state = 'old';
        }
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>">
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
            <div class="row-fluid">
                <div class="span6">
                    <textarea>
                    <%=data.default%>
                    </textarea>
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/plain" id="insurance_type_field_readonly_text_tpl">
    <%
        var state = 'new';
        if(data.is_old)
        {
            state = 'old';
        }
    %>
    <div class="control-group field-item" data-field-state="<%=state%>" data-field-id="<%=data.id%>" data-field-name="<%=data.name%>" data-field-links="<%=encodeURIComponent(data.links)%>" data-field-default-visible="<%=data.default_visible%>" data-field-default-disabled="<%=data.default_disabled%>">
        <div class="control-label"><%=data.name%></br>[<%=data.ename%>]</div>
        <div class="controls">
            <div class="row-fluid">
                <div class="span6">
                    <span><%=data.default%></span>
                </div>
                <div class="span6">
                    <div class="btn-group">
                        <button class="insurance-type-field-add-parts-btn btn" title="添加组件" data-id="<%=data.id%>"><i class="iconfa-magnet"></i></button>
                        <button class="insurance-type-field-default-visible-btn btn" title="默认<% if(data.default_visible == 1){ print('显示'); }else{ print('隐藏'); } %>" data-id="<%=data.id%>" data-default-visible="<%=data.default_visible%>"><i class="iconfa-eye-<% if(data.default_visible == 1){ print('open'); }else{ print('close'); } %>"></i></button>
                        <button class="insurance-type-field-default-disabled-btn btn" title="默认<% if(data.default_disabled == 1){ print('禁用'); }else{ print('启用'); } %>" data-id="<%=data.id%>" data-default-disabled="<%=data.default_disabled%>" ><i class="iconfa-<% if(data.default_disabled == 1){ print('lock'); }else{ print('unlock') } %>"></i></button>
                        <button class="insurance-type-field-remove-btn btn btn-danger" title="移除字段" data-id="<%=data.id%>"><i class="iconfa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="insurance_type_field_link_tpl">
    <div class="row-fluid insurance-type-field-link-item">
        <div class="span3">
            <select class="field input-medium">
            <% for(var i = 0; i < data.fields.length; i++){ %>
                <option value="<%=data.fields[i].id%>" <% if(data.id == data.fields[i].id){ print('selected'); } %>><%=data.fields[i].name%></option>
            <% } %>
            </select>
        </div>
        <div class="span1">
            <select class="field-link-type input-mini">
                <option value="1" <% if(data.link_type == 1 || !data.link_type){ print('selected') } %> >值</option>
            </select>
        </div>
        <div class="span1">
            <input type="text" class="field-link-value link-with-value input-mini" value="<%=data.link_value%>" />
        </div>
        <div class="span1" style="text-align:center">
            <i class="iconfa-link"></i>
        </div>
        <div class="span3">
            <select class="link-to-field input-medium">
            <% for(var i = 0; i < data.fields.length; i++){ %>
                <option value="<%=data.fields[i].id%>" <% if(data.link_to_id == data.fields[i].id){ print('selected'); } %>><%=data.fields[i].name%></option>
            <% } %>
            </select>
        </div>
        <div class="span1">
            <select class="field-link-to-type input-mini">
                <option value="1" <% if(data.link_to_type == 1){ print('selected') } %> >值</option>
                <option value="2" <% if(data.link_to_type == 2 || !data.link_to_type){ print('selected') } %> >启禁</option>
                <option value="3" <% if(data.link_to_type == 3){ print('selected') } %> >显隐</option>
            </select>
        </div>
        <div class="span1">
            <input type="text" class="field-link-to-value link-with-value input-mini" style="<% if(data.link_to_type == 1){ print('display:inline-block'); }else{ print('display:none'); } %>" value="<%=data.link_to_value%>" />
            <select class="field-link-to-value link-with-enable input-mini" style="<% if(data.link_to_type == 2 || !data.link_to_type){ print('display:inline-block'); }else{ print('display:none'); } %>">
                <option value="enable" <% if(data.link_to_value == 'enable'){ print('selected') } %> >启用</option>
                <option value="disable" <% if(data.link_to_value == 'disable'){ print('selected') } %> >禁用</option>
            </select>
            <select class="field-link-to-value link-with-visible input-mini" style="<% if(data.link_to_type == 3){ print('display:inline-block'); }else{ print('display:none'); } %>">
                <option value="show" <% if(data.link_to_value == 'show'){ print('selected') } %> >显示</option>
                <option value="hide" <% if(data.link_to_value == 'hide'){ print('selected') } %> >隐藏</option>
            </select>
        </div>
        <div class="span1">
            <div class="btn-group">
                <button class="btn btn-danger insurance-type-field-link-remove-btn"><i class="iconfa-remove"></i></button>
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="insurance_type_company_tpl">
    <%
        var state = 'new';
        var discount_percent = '';
        var discount_setting = '';
        if(data.is_old)
        {
            state = 'old';
            discount_percent = data.discount_percent;
            discount_setting = data.discount_setting;
        }
    %>
    <div class="well well-small company-box item" data-company-id="<%=data.companyId%>" data-company-state="<%=state%>">
        <i class="iconfa-remove tools tools-top insurance-type-company-remove-btn" data-id="<%=data.companyId%>"></i>
        <div><%=data.shortName%></div>
        <div><%=data.companyName%></div>
        <div><%=data.ename%></div>
        <div class="tools tools-bottom" style="display:block;clear:left;width:100%">
            <div class="row-fluid">
                <span class="label pull-left">优惠方式</span>
                <select class="insurance-type-company-discount" data-id="<%=data.companyId%>">
                    <option value="0" <% if(data.discount_type == '0'){ print('selected'); } %> >无</option>
                    <option value="1" <% if(data.discount_type == '1'){ print('selected'); } %> >总价比例</option>
                    <option value="2" <% if(data.discount_type == '2'){ print('selected'); } %> >满减</option>
                </select>
            </div>
            <div class="row-fluid insurance-type-discount-percent-secton" data-id="<%=data.companyId%>" style="display:none">
                <span class="label pull-left">优惠百分比</span>
                <input type="number" class="insurance-type-company-discount-percent input-number" data-id="<%=data.companyId%>" value="<%=discount_percent%>"/>
            </div>
            <div class="row-fluid insurance-type-discount-setting-secton" data-id="<%=data.companyId%>" style="display:none">
                <span class="label pull-left">优惠表配置</span>
                <textarea class="insurance-type-company-discount-setting" data-id="<%=data.companyId%>"><%=discount_setting%></textarea>
            </div>
        </div>
    </div>
</script>