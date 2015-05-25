<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-19
 * Time: 下午10:34
 */

$router = $di->getShared('router');

/**
 * 用户
 */

$user = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'adminuser'
));

//登录
$user->add('/login', array(
    'action' => 'login'
));

//登出
$user->add('/logout', array(
    'action' => 'logout'
));

$router->mount($user);

/**
 * 首页
 */

$index = new \Phalcon\Mvc\Router\Group( array(
    'controller' => 'index'
) );

//查看连接(状态)
$index->add('/status', array(
    'action' => 'status'
));

//app排行
$index->add('/ranks', array(
    'action' => 'ranks'
));

$index->add('/ranks.json', array(
    'action' => 'getRanks'
));

$router->mount($index);

/**
 * 统计
 */
$statistics = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'statistics'
));

$statistics->setPrefix('/statistics');

//统计页面
$statistics->add('/queryStatistics', array(
    'action' => 'queryStatistics'
));

//获取统计页面数据
$statistics->addGet('/queryStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}.json', array(
    'action' => 'getQueryStatistics'
));

//总访问统计
$statistics->add('/queryTotalStatistics', array(
    'action' => 'queryTotalStatistics'
));

$statistics->addGet('/queryTotalStatistics/{start_date:(\d{4}-\d{2}-\d{2}|origin)}/{end_date:(\d{4}-\d{2}-\d{2}|now)}.json', array(
    'action' => 'getQueryTotalStatistics'
));

//用户统计
$statistics->add('/userStatistics', array(
    'action' => 'userStatistics'
));

$statistics->addGet('/userStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}.json', array(
    'action' => 'getUserStatistics'
));

$statistics->add('/userTotalStatistics', array(
    'action' => 'userTotalStatistics'
));

$statistics->addGet('/userTotalStatistics/{start_date:(\d{4}-\d{2}-\d{2}|origin)}/{end_date:(\d{4}-\d{2}-\d{2}|now)}.json', array(
    'action' => 'getUserTotalStatistics'
));

/*
 * 操作统计
 */
$statistics->add('/actStatistics', array(
    'action' => 'actStatistics'
));

$statistics->addGet('/actStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}/{statistics_type:[a-zA-Z]+}.json', array(
    'action' => 'getActStatistics'
));

$statistics->addGet('/actTotalStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{statistics_type:[a-zA-Z]+}.json', array(
    'action' => 'getActTotalStatistics'
));


//总访问注册统计
$statistics->add('/quTotalStatistics', array(
    'action' => 'quTotalStatistics'
));

$statistics->add('quTotalStatistics.json', array(
    'action' => 'getQuTotalStatistics'
));

//用户活跃度
$statistics->add('/userActivityStatistics', array(
    'action' => 'userActivityStatistics'
));

$statistics->add('/userActivityStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}.json', array(
    'action' => 'getUserActivityStatistics'
));

//各省份用户凉排名
$statistics->add('/provinceUserStatistics', array(
    'action' => 'provinceUserStatistics'
));

$statistics->add('/provinceUserStatistics.json', array(
    'action' => 'getProvinceUserStatistics'
));

//用户版本统计
$statistics->add('/clientVersionStatistics', array(
    'action' => 'clientVersionStatistics'
));

$statistics->add('/clientVersionStatistics.json', array(
    'action' => 'getClientVersionStatistics'
));

/*
 * 保险行为统计
 * 第一次使用初算功能的用户
 */
$statistics->add('/firstPreliminaryCalculationStatistics', array(
    'action' => 'firstPreliminaryCalculationStatistics'
));

$statistics->add('/firstPreliminaryCalculationStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}.json', array(
    'action' => 'getFpcStatistics'
));

/*
 * 保险行为统计
 */
$statistics->add('/firstPreliminaryCalculationTotalStatistics', array(
    'action' => 'firstPreliminaryCalculationTotalStatistics'
));

$statistics->add('/firstPreliminaryCalculationTotalStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}.json', array(
    'action' => 'getFpcTotalStatistics'
));

$statistics->add('/insuranceActStatics', array(
    'action' => 'insuranceActStatics'
));

$statistics->add('/insuranceActStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}.json', array(
    'action' => 'getInsuranceActStatistics'
));

$statistics->add('/insuranceActTotalStatistics', array(
    'action' => 'insuranceActTotalStatistics'
));

$statistics->add('/insuranceActTotalStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}.json', array(
    'action' => 'getInsuranceActTotalStatistics'
));

$router->mount($statistics);

/**
 * 保险
 */

$insurance = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'insurance'
));

//保险列表页面
$insurance->addGet('/insuranceList', array(
    'action' => 'insuranceList'
));

//获取保险列表
$insurance->add('/insuranceList.json', array(
    'action' => 'getInsuranceList'
));

//保险详情/精算页面
$insurance->addGet('/insuranceResult', array(
    'action' => 'insuranceResult'
));

//保险购买处理页面
$insurance->addGet('/insuranceBuy', array(
    'action' => 'insuranceBuy'
));

//保险无法尽算理由
$insurance->addPut('/insuranceCantExactReason.json', array(
    'action' => 'insuranceCantExactReason'
));

//生成保单
$insurance->addPut('/insuranceGenNo.json', array(
    'action' => 'genInsuranceNo'
));

//保险出单
$insurance->addPut('/insuranceIssuing.json', array(
    'action' => 'insuranceIssuing'
));

//保险 提交精算结果
$insurance->addPut('/insuranceExactResult.json', array(
    'action' => 'updateInsuranceResult'
));

//保险 交易完成
$insurance->addPut('/insuranceComplete.json', array(
    'action' => 'insuranceComplete'
));

//保险公司管理页面
$insurance->addGet('/insuranceCompany', array(
    'action' => 'company'
));

//获取保险公司列表
$insurance->add('/insuranceCompanyList.json', array(
    'action' => 'getInsuranceCompanyList'
));

//添加保险公司
$insurance->addPost('/insuranceCompany.json', array(
    'action' => 'addInsuranceCompany'
));

//更新保险公司
$insurance->addPut('/insuranceCompany.json', array(
    'action' => 'updateInsuranceCompany'
));

//删除保险公司
$insurance->addDelete('/insuranceCompany/{id:\d+}.json', array(
    'action' => 'delInsuranceCompany'
));

$router->mount($insurance);

/**
 * 本地惠
 */

$local_favour = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'localfavour'
));

//本地惠发布页面
$local_favour->add('/localFavourPush', array(
    'action' => 'localFavourPush'
));

//获取本地惠列表
$local_favour->add('/localFavourList.json', array(
    'action' => 'getLocalFavourList'
));

//添加本地惠
$local_favour->addPost('/localFavour.json', array(
    'action' => 'addLocalFavour'
));

//更新本地惠
$local_favour->addPut('/localFavour.json', array(
    'action' => 'updateLocalFavour'
));

//删除本地惠
$local_favour->addDelete('/localFavour/{id:\d+}.json', array(
    'action' => 'delLocalFavour'
));

//获取本地惠图片
$local_favour->add('/localFavourPic/{id:\d+}', array(
    'action' => 'getLocalFavourPic'
));

//临时上传本地惠图片
$local_favour->addPost('/localFavourTempPic.json', array(
    'action' => 'uploadTempPic'
));


//本地惠评论窗口内容
$local_favour->addGet('/localFavourComment/{pid:\d+}', array(
    'action' => 'localFavourComment'
));

//本地惠评论回复
$local_favour->addPut('/localFavourCommentReply/{id:\d+}.json', array(
    'action' => 'localFavourCommentReply'
));

//首页推广页面
$local_favour->add('/localFavourAdvList', array(
    'action' => 'localFavourAdvList'
));

//获取推广列表
$local_favour->add('/localFavourAdvList.json', array(
    'action' => 'getLocalFavourAdvList'
));

//添加推广
$local_favour->addPost('/localFavourAdv.json', array(
    'action' => 'addLocalFavourAdv'
));

//更新推广
$local_favour->addPut('/localFavourAdv.json', array(
    'action' => 'updateLocalFavourAdv'
));

//删除推广
$local_favour->addDelete('/localFavourAdv/{id:\d+}.json', array(
    'action' => 'delLocalFavourAdv'
));

//本地惠稿件管理页面
$local_favour->addGet('/localFavourSubList', array(
    'action' => 'localFavourSubList'
));

//获取本地惠稿件列表
$local_favour->add('/localFavourSubList.json', array(
    'action' => 'getLocalFavourSubList'
));

//获取本地惠稿件图片数据
$local_favour->addGet('/localFavourSubPic/{id:\d+}', array(
    'action' => 'getLocalFavourSubPic'
));

//获取本地惠稿件
$local_favour->addGet('/localFavourSub/{id:\d+}.json', array(
    'action' => 'getLocalFavourSub'
));

//删除本地惠稿件
$local_favour->addDelete('/localFavourSub/{id:\d+}.json', array(
    'action' => 'delLocalFavourSub'
));

//滚动广告管理页面
$local_favour->addGet('/localFavourScrollAd', array(
    'action' => 'localFavourScrollAd'
));

//获取滚动广告列表
$local_favour->add('/localFavourScrollAdList.json', array(
    'action' => 'getLocalFavourScrollAdList'
));

//添加滚动广告
$local_favour->addPost('/localFavourScrollAd.json', array(
    'action' => 'addLocalFavourScrollAd'
));

//删除本地惠稿件
$local_favour->addDelete('/localFavourScrollAd/{id:\d+}.json', array(
    'action' => 'delLocalFavourScrollAd'
));

//删除本地惠稿件
$local_favour->addPut('/localFavourScrollAd.json', array(
    'action' => 'updateLocalFavourScrollAd'
));

//车辆信息校对页面
$local_favour->addGet('/autoConfig', array(
    'action' => 'autoConfig'
));

//获取车辆信息
$local_favour->add('/autoList.json', array(
    'action' => 'getAutoList'
));

//获取车辆信息
$local_favour->addGet('/autoPic/{id:\d+}', array(
    'action' => 'getAutoPic'
));

$router->mount($local_favour);

/**
 * 活动
 */

$activity = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'activity'
));

//活动管理页面
$activity->addGet('/activityList', array(
    'action' => 'activityList'
));

//获取活动列表
$activity->add('/activityList.json', array(
    'action' => 'getActivityList'
));

//添加活动
$activity->addPost('/activity.json', array(
    'action' => 'addActivity'
));

//获取活动列表
$activity->addDelete('/activity/{id:\d+}.json', array(
    'action' => 'delActivity'
));

//更新活动
$activity->addPut('/activity.json', array(
    'action' => 'updateActivity'
));

//活动详情页面
$activity->addGet('/activityDetail/{id:\d+}', array(
    'action' => 'detail'
));

//获取活动签到信息列表
$activity->add('/activityCheckList.json', array(
    'action' => 'getCheckList'
));

//获取活动支付信息列表
$activity->add('/activityPayList.json', array(
    'action' => 'getPayList'
));

//活动参与用户页面
$activity->addGet('/activityUser', array(
    'action' => 'involvedUser'
));

//获取活动参与用户列表
$activity->add('/activityUserList.json', array(
    'action' => 'getInvolvedUserList'
));

//用户通知
$activity->add('/activityNoticeUser/{ids:(\d+(,\d+)*)}.json', array(
    'action' => 'noticeUser'
));

//用户领取
$activity->add('/activityUserGain/{ids:(\d+(,\d+)*)}.json', array(
    'action' => 'userGain'
));

//用户付款
$activity->add('/activityUserPay/{ids:(\d+(,\d+)*)}.json', array(
    'action' => 'userPay'
));

$router->mount($activity);

/**
 * 抽奖
 */

$award = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'award'
));

//抽奖奖品管理页面
$award->addGet('/awardList', array(
    'action' => 'awardList'
));

//获取支持抽奖活动的列表
$award->add('/awardActivityList.json', array(
    'action' => 'getAwardActivityList'
));

//获取奖品列表
$award->add('/awardList.json', array(
    'action' => 'getAwardList'
));

//添加奖品
$award->addPost('/award.json', array(
    'action' => 'addAward'
));

//删除奖品
$award->addDelete('/award/{id:\d+}.json', array(
    'action' => 'delAward'
));

//更新奖品
$award->addPut('/award.json', array(
    'action' => 'updateAward'
));

//中奖管理页面
$award->addGet('/awardGainManage', array(
    'action' => 'awardGainManage'
));

//获取中奖(用户中奖总数)列表
$award->add('/awardGainList.json', array(
    'action' => 'getGainList'
));

//添加中奖记录
$award->addPost('/awardUserGain.json', array(
    'action' => 'addUserGain'
));

//获取用户中奖记录列表
$award->add('/awardUserGainList.json', array(
    'action' => 'getUserGainList'
));

$award->addPut('/awardGain/{id:\d+}.json', array(
    'action' => 'awardGain'
));

$router->mount($award);

/**
 * 投票
 */

$vote = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'vote'
));

//投票管理页面
$vote->addGet('/voteList', array(
    'action' => 'voteList'
));

//获取投票列表
$vote->add('/voteList.json', array(
    'action' => 'getVoteList'
));

$vote->addPost('/vote.json', array(
    'action' => 'addVote'
));

$vote->addDelete('/vote/{id:\d+}.json', array(
    'action' => 'delVote'
));

$vote->addPut('/vote.json', array(
    'action' => 'updateVote'
));

$router->mount($vote);

/**
 * 积分商城
 */

//商品管理页面
$router->addGet('/itemManage', array(
    'controller' => 'item',
    'action' => 'itemManage'
));

//获取商品列表
$router->add('/itemList.json', array(
    'controller' => 'item',
    'action' => 'getItemList'
));

//获取商品分类列表
$router->add('/itemTypeList.json', array(
    'controller' => 'item',
    'action' => 'getTypeList'
));

//商品上架(支持批量)
$router->addPut('/itemOnShelf/{ids:(\d+(-\d+)*)}.json', array(
    'controller' => 'item',
    'action' => 'onShelfItem'
));

//商品下架(支持批量)
$router->addPut('/itemOffShelf/{ids:(\d+(-\d+)*)}.json', array(
    'controller' => 'item',
    'action' => 'offShelfItem'
));

//商品添加
$router->addPost('/item.json', array(
    'controller' => 'item',
    'action' => 'addItem'
));

//商品编辑
$router->addPut('/item.json', array(
    'controller' => 'item',
    'action' => 'updateItem'
));

//商品删除(支持批量)
$router->addDelete('/item/{ids:(\d+(-\d+)*)}.json', array(
    'controller' => 'item',
    'action' => 'delItem'
));

//商品类目添加
$router->addPost('/itemType.json', array(
    'controller' => 'item',
    'action' => 'addItemType'
));

//商品类目删除
$router->addDelete('/itemType/{id:\d+}.json', array(
    'controller' => 'item',
    'action' => 'delItemType'
));

//商品类目编辑
$router->addPut('/itemType.json', array(
    'controller' => 'item',
    'action' => 'updateItemType'
));

//获取商品对换信息列表
$router->add('/itemExchangeList.json', array(
    'controller' => 'item',
    'action' => 'getExchangeList'
));

//流水记录页面
$router->addGet('/transactionList', array(
    'controller' => 'transaction',
    'action' => 'list'
));

//获取流水记录列表
$router->add('/transactionList.json', array(
    'controller' => 'transaction',
    'action' => 'getTransactionList'
));

//订单管理页面
$router->addGet('/dealManage', array(
    'controller' => 'itemdeal',
    'action' => 'manage'
));

//获取订单列表
$router->add('/dealList.json', array(
    'controller' => 'itemdeal',
    'action' => 'getDealList'
));

//订单发货
$router->addPut('/dealDeliver/{id:\d+}.json', array(
    'controller' => 'itemdeal',
    'action' => 'deliver'
));

/**
 * 公告
 */

$notice = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'notice'
));

$notice->setPrefix('/notice');

//获取公告列表

$notice->add('/list.json', array(
    'action' => 'getNoticeList'
));

$router->mount($notice);


/**
 * 省份与城市
 */

$province = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'province'
));

$province->addGet('/province/{province_id:\d+}/city.json', array(
    'action' => 'getCityList'
));

$router->mount($province);

/**
 * 系统
 */

//异常信息页面
$router->addGet('/appException', array(
    'controller' => 'feedback',
    'action' => 'appException'
));

//获取异常列表
$router->add('/appExceptionList.json', array(
    'controller' => 'feedback',
    'action' => 'getAppExceptionList'
));

//删除异常信息
$router->addDelete('/appException/{id:\d+}.json', array(
    'controller' => 'feedback',
    'action' => 'delAppException'
));
