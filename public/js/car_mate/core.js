/**
 * Created by jkzleond on 15-3-29.
 * Core of CarMate
 */

/**
 * core
 */
var CarMate = {};
CarMate.dispatcher = {};

//系统事件
CarMate.fire = function(event){
    jQuery(CarMate.dispatcher).trigger(event);
};

CarMate.bind = function(event, handler){
    jQuery(CarMate.dispatcher).bind(event, handler);
};

CarMate.unbind = function(event, handler){
    if(handler)
    {
        jQuery(CarMate.dispatcher).unbind(event, handler);
    }
    else
    {
        jQuery(CarMate.dispatcher).unbind(event);
    }
};


/**
 * 页面对象,加载面页并持有页面状态
 * @type {{_xhr: null, container: string, load: Function, on_loading: null, on_loaded: null, on_error: null, on_complete: null, on_leave: null, content_filter: null}}
 */
CarMate.page = {
    //上一次成功加载页面的url
    _url:'',
    //xhr对象,用于保存ajax加载页时产生的xhr,以便管理页的加载
    _xhr: null,
    //页面容器,jQuery选择器
    container: '#page',
    //ajax加载页
    load: function(url) {
        //首先取消之前还未加载完成的页的加载行为
        if(this._xhr) this._xhr.abort();
        //执行当前页的on_leave回调函数,并清除回调函数
        if(this.on_leave){
            this.on_leave();
            this.on_leave = null;
        }
        $(this.container).empty();
        this._xhr = $.ajax({
            url: url,
            method: 'GET',
            type: 'GET',
            dataType: 'html',
            //回调函数中上下文设定,此处设定微page
            context: this,
            async: true,
            global: true,
            beforeSend: function(){
                if(this.on_loading)
                {
                    this.on_loading();
                }
            },
            dataFilter: function(data){
                if(this.content_filter) return this.content_filter(data);
                return data;
            },
            success: function(html){
                this._url = url;
                $(this.container).append(html);
                if(this.on_loaded)
                {
                    this.on_loaded();
                    this.on_loaded = null;
                }
            },
            error: function(xhr, text_status, error_thrown){
                $(this.container).append('<h1>' + error_thrown + '<h1/>');
                if(this.on_error)
                {
                    this.on_error(xhr, text_status, error_thrown);
                }
            },
            complete: function(){
                if(this.on_complete)
                {
                    this.on_complete();
                }
            }
        });
    },
    reload: function(){
        this.load(this._url);
    },
    on_loading: null,
    on_loaded: null,
    on_error: null,
    on_complete: null,
    on_leave: null,
    content_filter: null
};

//元类
CarMate.Cls = function(definitions){

    var cls = function(){
        //__ext__表示继承时构造作为子类原型实例的标志
        if(!arguments || !arguments['__ext__'] && typeof(this.init) === 'function')
        {
            this.init.apply(this,arguments);
        }
        this.__class__ = arguments.callee;
    };

    for(var prop in definitions)
    {
        if(prop === '__class_props__') continue;
        cls.prototype[prop] = definitions[prop];
    }

    if(definitions && definitions['__class_props__'])
    {
        var __class_props__ = definitions['__class_props__'];
        for(var class_prop in __class_props__)
        {
            cls[class_prop] = __class_props__[class_prop]
        }
        cls.__class_props__ = __class_props__;
    }

    cls.__class__ = CarMate.Cls;

    return cls;
};


//每个构造函数都是Function实例,在Function.prototype上定义的属性将成为类函数(原型链继承规则)
Function.prototype.extend = function(addtion){

    //创建空类
    var cls = new CarMate.Cls();

    //此处this指调用extend的类(CarMate.Cls的实例)
    var super_class = this;

    var prototype = new super_class({__ext__:true});

    for(var prop in addtion)
    {
        if(prop === '__class_props__') continue;
        prototype[prop] = addtion[prop];
    }

    //设置父类,以便之后的class_init类方法中子类可以使用super
    prototype.super = super_class;
    cls.prototype = prototype;
    cls.super = super_class;

    //合并复制类属性
    var _super_class_props = super_class.__class_props__ || {};
    var _addtion_class_props = addtion.__class_props__ || {};

    var __class_props__ = {};

    for(var super_class_prop in _super_class_props)
    {
        __class_props__[super_class_prop] = _super_class_props[super_class_prop];
        cls[super_class_prop] = _super_class_props[super_class_prop];
    }

    for(var addition_class_prop in _addtion_class_props)
    {
        __class_props__[addition_class_prop] = _addtion_class_props[addition_class_prop];
        cls[addition_class_prop] = _addtion_class_props[addition_class_prop];
    }

    cls.__class_props__ = __class_props__;

    //执行类初始化方法(子类各自的属性,非与父类共享的属性,如一些Object和Array)
   // 非方法属性在class_init中初始化,否则该属性将与父类共享(如同类方法)
    if(cls.class_init){
        cls.class_init();
    }

    return cls;
};


/**
 * Model
 */
CarMate.Model = new CarMate.Cls({
    init: function(name){
        this._name = name;
        this._raw_data = {};
    },
    get: function(prop){
        return this._raw_data[prop];
    },
    set: function(prop, value){
        return this._raw_data[prop] = value;
    },
    getUrl: function()
    {
        return this._url;
    },
    setUrl: function()
    {
        return this._url;
    },
    //调用类方法buildUrl,构建模型实例的url
    buildUrl: function()
    {
        this._url = this.__class__.buildUrl(this._raw_data);
    },
    //保存(更新)单个模型的数据(将模型数据记录到Model类的_saves数组中,Model.commit调用后一并向服务器提交数据并保存)
    create: function()
    {
        this.__class__._creates.push(this._raw_data);
    },
    //删除单个模型的数据(同保存)
    delete: function()
    {
        this.__class__._dels.push(this._raw_data);
    },
    //更新单歌模型的数据(同保存)
    update: function()
    {
        this.__class__._updates.push(this._raw_data);
    },
    find: function()
    {
        this.__class__.find(this._raw_data, 'findOne');
    },
    //定义类属性
    __class_props__: {
        //非方法属性在class_init中初始化,否则该属性将与父类共享
        class_init: function(){

            //用于储存模型实例的集合
            this._models = [];
            //用于储存要创建(插入)的模型,之后将清空
            this._creates = [];
            //同保存
            this._dels = [];
            //同保存
            this._updates = [];
        },
        //ajax请求, 返回xhr
        async: function(options){
            return $.ajax(options);
        },
        /**
         *查询模型数据,不需要调用commit直接获取服务器数据,返回xhr
         * @param condition 查询条件
         * @param type 查询类型 find or findOne
         * @returns {*}
         */
        find: function(condition, type){

            var find_type = type || 'find';

            var find_options = {
                url: this.buildUrl(condition, find_type),
                dataType: 'json',
                //同时设置type和method是为了兼容jQuery1.90以及以前的(使用type的)版本
                type: 'GET',
                method: 'GET',
                //设置回调事件的上下文为模型类
                context: this,
                success: function(data){
                    var cls = this;
                    if(find_type == 'find')
                    {
                        //this为上面设置的context,即模型类
                        //数据在没有限定条目数的总数.用于分页
                        cls.total  = data.total;
                        //所获取的数据条目数
                        cls.count = data.count;
                        //保存原始数据
                        cls.raw_data = data;

                        //查询数据成功后清除_models模型集合
                        this._models.splice(0);

                        //更具返回数据创建模型实例,并记录到模型类的_models模型集合中
                        var len = data.rows.length;
                        for(var i = 0 ; i < len; i++)
                        {
                            var row = data.rows[i];
                            var model = new cls();
                            for(var field in row)
                            {
                                var value = row[field];
                                model[field] = value;
                                model.set(field, value);
                            }

                            cls._models.push(model);
                        }
                        //trigger执行handler时使用apply,所以数据不能直接以数组传递,否则将成为可变参数传递进handler,需把数据集放数组中,以便handler通过models形参操作模型集合
                        this.getEventDispatcher().trigger('found', [cls._models, data]);
                    }
                    else if( find_type == 'findOne')
                    {
                        var model = new cls();

                        for(var field in data)
                        {
                            var value = data[field];
                            //model[field] = value;
                            model.set(field, value);
                        }

                        this.getEventDispatcher().trigger('foundOne', [model, data]);
                    }

                },
                error: function(code){
                    this.getEventDispatcher().trigger('notFound', [code]);
                },
                beforeSend: function()
                {
                    this.getEventDispatcher().trigger('beforeFind');
                }
            };
           return this.async(find_options);
        },
        //提交要保存(更新,删除)的模型数据,只有调用次方法后数据才会真的被提交服务器
        commit: function(additional_options){

            //创建(插入)模型数据
            if(this._creates.length !== 0)
            {
                var create_options = {
                    url: this.buildUrl(null, 'create'),
                    data: {creates: this._creates},
                    dataType: 'json',
                    //同时设置type和method是为了兼容jQuery1.90以及以前的(使用type的)版本
                    type: 'POST',
                    method: 'POST',
                    //设置回调事件的上下文为模型类
                    context: this,
                    success: function(data){

                        //TODO 保存成功后设置模型id
                        this.getEventDispatcher().trigger('created', [data]);
                    },
                    error: function(code){
                        this.getEventDispatcher().trigger('notCreated', [code]);
                    },
                    beforeSend: function()
                    {
                        this.getEventDispatcher().trigger('beforeCreate');
                    }
                };

                if(additional_options)
                {
                    create_options = $.extend(create_options, additional_options);
                }

                this.async(create_options);
                //删出暂存于_saves的模型
                this._creates.splice(0);
            }

            //删除模型数据
            if(this._dels.length !== 0)
            {

                var condition = this._dels.length == 1 ? this._dels[0] : this._dels;

                var del_options = {
                    url: this.buildUrl(condition, 'delete'),
                    dataType: 'json',
                    //同时设置type和method是为了兼容jQuery1.90以及以前的(使用type的)版本
                    type: 'DELETE',
                    method: 'DELETE',
                    //设置回调事件的上下文为模型类
                    context: this,
                    success: function(data){

                        this.getEventDispatcher().trigger('deleted',[data]);
                    },
                    error: function(code){
                        this.getEventDispatcher().trigger('notDeleted', [code]);
                    },
                    beforeSend: function()
                    {
                        this.getEventDispatcher().trigger('beforeDelete');
                    }
                };

                if(additional_options)
                {
                    del_options = $.extend(del_options, additional_options);
                }

                this.async(del_options);
                //删出暂存于_saves的模型
                this._dels.splice(0);
            }

            //更新模型数据

            if(this._updates.length !== 0)
            {
                var update_options = {
                    url: this.buildUrl(null, 'update'),
                    data: {updates: this._updates},
                    dataType: 'json',
                    //同时设置type和method是为了兼容jQuery1.90以及以前的(使用type的)版本
                    type: 'PUT',
                    method: 'PUT',
                    //设置回调事件的上下文为模型类
                    context: this,
                    success: function(data){

                        //TODO 保存成功后设置模型id

                        this.getEventDispatcher().trigger('updated', [data]);
                    },
                    error: function(code){
                        this.getEventDispatcher().trigger('notUpdated', [code]);
                    },
                    beforeSend: function()
                    {
                        this.getEventDispatcher().trigger('beforeUpdate');
                    }
                };

                if(additional_options)
                {
                    update_options = $.extend(update_options, additional_options);
                }

                this.async(update_options);
                //删出暂存于_saves的模型
                this._updates.splice(0);
            }
        },
        getUrl: function()
        {
            return this._url;
        },
        setUrl: function(url)
        {
            this._url = url;
        },
        //子类必须覆盖,以获得数据操作的真确url
        //condition为构建url的数据(url参数)
        buildUrl: function(condition, action)
        {
            throw new Error('Model.buildUrl should be override in child class');
        },
        //获取事件调度器
        getEventDispatcher: function(){
            if(!this._eventDispatcher)
            {
                this._eventDispatcher = $({});
            }
            return this._eventDispatcher;
        },
        //绑定模型事件
        on: function(event, handler){
            this.getEventDispatcher().bind(event, handler);
        },
        //移除模型事件
        off: function(event, handler){
            this.getEventDispatcher().unbind(event, handler);
        }


    }
});


/***
 * utils
 */

CarMate.utils = {};

//zfill same as Python
CarMate.utils.zfill = function(num_str, length){
    var str = num_str;
    if(typeof num_str === 'number') str = String(num_str);
    var len = length - str.length;
    for(var i = len ; i > 0; i--)
    {
        str = '0' + str;
    }
    return str;
}

//date function same as PHP
CarMate.utils.date = function(format, date_str){
    var date = new Date(date_str);
    var year = date.getFullYear();
    var month = this.zfill(date.getMonth() + 1, 2);
    var day = this.zfill(date.getDate(), 2);
    var hour = this.zfill(date.getHours(), 2);
    var min = this.zfill(date.getMinutes(), 2);
    var sec = this.zfill(date.getSeconds(), 2);

    var conv = null;
    conv = format.replace('Y', year);
    conv = conv.replace('m', month);
    conv = conv.replace('d', day);
    conv = conv.replace('H', hour);
    conv = conv.replace('i', min);
    conv = conv.replace('s', sec);

    return conv;
};

//convert mssql default datetime format to which javascript know
CarMate.utils.date.mssqlToJs = function(datetime){
    var conv = datetime.replace(/\s+/g, '/');
    conv = conv.replace(/\/(\d{2}:)/g, ' $1');
    conv = conv.replace(/:\d+(AM|PM)/, ' $1');
    return conv;
}


