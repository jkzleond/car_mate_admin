<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-19
 * Time: 下午10:34
 */

$router = $di->getShared('router');

/**
 * 系统用户
 */

$admin = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'adminuser'
));

//登录
$admin->add('/login', array(
    'action' => 'login'
));

//登出
$admin->add('/logout', array(
    'action' => 'logout'
));

$router->mount($admin);

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

//用户留存统计
$statistics->addGet('/userRetentionStatistics', array(
    'action' => 'userRetentionStatistics'
));

$statistics->addGet('/userRetentionStatistics/{start_date:(\d{4}-\d{2}-\d{2}|origin)}/{end_date:(\d{4}-\d{2}-\d{2}|now)}/{grain:(day|week|month|year)}/{client:.*}/{version:.*}.json', array(
    'action' => 'getUserRetentionStatistics'
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

/**
 * 违章代缴业务统计
 */

//违章代缴业务订单统计页面
$statistics->addGet('/orderIllegalStatistics', array(
    'action' => 'orderIllegalStatistics'
));

$statistics->add('/orderIllegalStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}.json', array(
    'action' => 'getOrderIllegalStatistics'
));

$statistics->add('/orderIllegalTotalStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}.json', array(
    'action' => 'getOrderIllegalTotalStatistics'
));

//违章代缴业务用户统计页面
$statistics->addGet('/orderIllegalUserStatistics', array(
    'action' => 'orderIllegalUserStatistics'
));

$statistics->add('/orderIllegalNewUserStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}.json', array(
    'action' => 'getOrderIllegalNewUserStatistics'
));

$statistics->add('/orderIllegalUserTotalStatistics/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}.json', array(
    'action' => 'getOrderIllegalUserTotalStatistics'
));

//违章代缴业务追踪统计页面
$statistics->addGet('/orderIllegalTrackStatistics', array(
    'action' => 'orderIllegalTrackStatistics'
));

$statistics->add('/orderIllegalTrackStatistics/{user_id:.*}/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}/{group_type:[a-zA-Z]+}.json', array(
    'action' => 'getOrderIllegalTrackStatistics'
));

$statistics->add('/orderIllegalTrackTotalStatistics/{user_id:.*}/{start_date:\d{4}-\d{2}-\d{2}}/{end_date:\d{4}-\d{2}-\d{2}}.json', array(
    'action' => 'getOrderIllegalTrackTotalStatistics'
));

/**
 * 挪车业务统计
 */
//挪车业务统计页面
$statistics->addGet('/moveCarStatistics', array(
    'action' => 'moveCarStatistics'
));

//调用挪车业务统计存储过程
$statistics->add('/move_car/{proc_name:.*}.json', array(
    'action' => 'callMoveCarStatisticsProc'
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

//保险购买处理页面
$insurance->addGet('/insuranceOrderInfo/{info_id:\d+}', array(
    'action' => 'insuranceOrderInfo'
));

//获取已精算公司
$insurance->addGet('/insurance/{info:\d+}/hasActuaryCompany.json', array(
    'action' => 'getHasActuaryCompany'
));

//保险获取指定保险公司的精算结果
$insurance->addGet('/insurance/finalResult/{info_id:\d+}/{company_id:\d+}.json', array(
    'action' => 'getFinalResult'
));

//保险保存某家公司的精算结果
$insurance->addPost('/insurance/finalResult/{info_id:\d+}/{company_id:\d+}.json', array(
    'action' => 'saveFinalResult'
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

//险种管理页面
$insurance->addGet('/insuranceType', array(
    'action' => 'insuranceType'
));

//获取险种数据列表
$insurance->add('/insurance/insuranceTypeList.json', array(
    'action' => 'getInsuranceTypeList'
));

//添加险种
$insurance->addPost('/insurance/insuranceType.json', array(
    'action' => 'addInsuranceType'
));

//更新险种
$insurance->addPut('/insurance/insuranceType/{type_id:\d+}.json', array(
    'action' => 'updateInsuranceType'
));

//删除险种
$insurance->addDelete('/insurance/insuranceType/{type_id:\d+}.json', array(
    'action' => 'delInsuranceType'
));

//获取险种所属类目列表
$insurance->addGet('/insurance/insuranceType/{type_id:\d+}/categoryList.json', array(
    'action' => 'getInsuranceTypeCategoryList'
));

//获取险种字段列表
$insurance->addGet('/insurance/insuranceType/{type_id:\d+}/fieldList.json', array(
    'action' => 'getInsuranceTypeFieldList'
));

//获取险种支持公司列表
$insurance->addGet('/insurance/insuranceType/{type_id:\d+}/companyList.json', array(
    'action' => 'getInsuranceTypeCompanyList'
));

//获取险种类目数据
$insurance->add('/insurance/insuranceCategoryList.json', array(
    'action' => 'getInsuranceCategoryList'
));

//添加保险类目
$insurance->addPost('/insurance/insuranceCategory.json', array(
    'action' => 'addInsuranceCategory'
));

//更新保险类目
$insurance->addPut('/insurance/insuranceCategory/{cate_id:\d+}.json', array(
    'action' => 'updateInsuranceCategory'
));

//删除保险类目
$insurance->addDelete('/insurance/insuranceCategory/{cate_id:\d+}.json', array(
    'action' => 'delInsuranceCategory'
));

//保险订单管理页面
$insurance->addGet('/insuranceNewInfoManage', array(
    'action' => 'insuranceNewInfoManage'
));

//获取保险订单列表数据
$insurance->add('/insurance/newInfoList.json', array(
    'action' => 'getInsuranceNewInfoList'
));

//保险订单明细页面(全险种)
$insurance->addGet('/insurance/newInfoDetail/{info_id:\d+}', array(
    'action' => 'insuranceNewInfoDetail'
));

//保险预约页面
$insurance->addGet('/insuranceReservation', array(
    'action' => 'insuranceReservation'
));

//获取保险数据列表
$insurance->add('/insuranceReservationList.json', array(
    'action' => 'getInsuranceReservationList'
));

//处理保险预约(标记已报价状态)
$insurance->addPut('/insuranceReservationProcess/{reservation_id:\d+}.json', array(
    'action' => 'processInsuranceReservation'
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

//获取指定ID首页推广信息
$local_favour->addGet('/localFavourAdv/{id:\d+}.json', array(
    'action' => 'getLocalFavourAdv'
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

//抽奖时段页面
$activity->addGet('/activity/drawPeriod/{aid:\d+}', array(
    'action' => 'drawPeriod'
));

//获取抽奖时段列表数据
$activity->add('/activity/{aid:\d+}/periodList.json', array(
    'action' => 'getDrawPeriodList'
));

//添加抽奖时段列表数据
$activity->addPost('/activity/{aid:\d+}/period.json', array(
    'action' => 'addDrawPeriod'
));

//编辑抽奖时段数据
$activity->addPut('/activity/period/{id:\d+}.json', array(
    'action' => 'updateDrawPeriod'
));

//删除抽奖时段数据
$activity->addDelete('/activity/period/{id:\d+}.json', array(
    'action' => 'delDrawPeriod'
));

//时段奖品管理页面
$activity->addGet('/activity/period/{period_id:\d+}/award', array(
    'action' => 'drawPeriodAward'
));

//获取指定时段奖品列表
$activity->add('/activity/period/{period_id:\d+}/awardList.json', array(
    'action' => 'getDrawPeriodAwardList'
));

//为指定时段添加奖品
$activity->addPost('/activity/period/{period_id:\d+}/award.json', array(
    'action' => 'addDrawPeriodAward'
));

//删除抽奖时段的奖品
$activity->addDelete('/activity/periodAward/{id:\d+}.json', array(
    'action' => 'delDrawPeriodAward'
));

//奖品管理页面
$activity->addGet('/awardManage/{aid:\d+}', array(
    'action' => 'awardManage'
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

//活动参与用户订单明细
$activity->addGet('/activityUserOrderDetail/{order_id:\d+}', array(
    'action' => 'orderDetail'
));

//付款活动订单管理页面
$activity->addGet('/orderMng', array(
    'action' => 'orderManage'
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

//获取指定ID奖品图像
$award->addGet('/award/{award_id:\d+}/pic.png', array(
    'action' => 'getAwardPic'
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
 * 违法代缴
 */

$illegal = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'illegal'
));

$illegal->setPrefix('/illegal');

//违章代缴-订单管理页面
$illegal->addGet('/orderMng', array(
    'action' => 'orderManage'
));

//违章代缴-获取订单列表
$illegal->addPost('/orderList.json', array(
    'action' => 'getOrderList'
));

//违章代缴-获取订单明细
$illegal->addGet('/orderDetail/{order_id:\d+}', array(
    'action' => 'orderDetail'
));

//违章代缴-处理违章
$illegal->addPut('/orderProcess/{order_id:\d+}.json', array(
    'action' => 'orderProcess'
));

//违章代缴-退款
$illegal->addGet('/refund/{order_id:\d+}/{refund_fee:\d+\.\d{2}}', array(
    'action' => 'orderRefund'
));

//驾驶员信息管理页面
$illegal->addGet('/driverInfoMng', array(
    'action' => 'driverInfoManage'
));

//获取驾驶员信息列表
$illegal->add('/driverInfoList.json', array(
    'action' => 'getDriverInfoList'
));

//更新驾驶员信息
$illegal->addPut('/driverInfo/{info_id:\d+}.json', array(
    'action' => 'updateDriverInfo'
));

//更新车辆信息
$illegal->addPut('/carInfo/{car_info_id:\d+}.json', array(
    'action' => 'updateCarInfo'
));

//流水列表页面
$illegal->addGet('/transactionList', array(
    'action' => 'transactionList'
));

//获取流水列表数据
$illegal->add('/transactionList.json', array(
    'action' => 'getTransactionList'
));

//更新流水
$illegal->addPut('/transaction/{order_id:\d+}.json', array(
    'action' => 'updateTransaction'
));

//获取违章代缴用户信息列表
$illegal->add('/orderUserList.json', array(
    'action' => 'getOrderUserList'
));

$router->mount($illegal);

/**
 * 挪车业务
 */

$move_car = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'movecar'
));

$move_car->setPrefix('/move_car');

//订单管理页面
$move_car->addGet('/orderMng',array(
    'action' => 'orderManage'
));

//获取挪车订单列表
$move_car->add('/orderList.json', array(
    'action' => 'getOrderList'
));

//挪车详细页面
$move_car->add('/orderDetail/{order_id:\d+}', array(
    'action' => 'orderDetail'
));

//挪车订单申诉处理
$move_car->addPut('/appeal_process/{order_id:\d+}.json', array(
    'action' => 'appealProcess'
));

//获取挪车反馈意见数据列表
$move_car->addPost('/advise.json', array(
    'action' => 'getFeedbackAdvise'
));

//车主管理页面
$move_car->addGet('/carOwnerMng', array(
    'action' => 'carOwnerManage'
));

//获取车主列表
$move_car->add('/car_owners.json', array(
    'action' => 'getCarOwnerList'
));

//更新车主
$move_car->addPut('/car_owner/{car_owner_source:.*}/{car_owner_id:\d+}.json', array(
    'action' => 'updateCarOwner'
));

//车主话单详情页面
$move_car->addGet('/car_owner/{car_owner_source:.*}/{car_owner_id:\d+}/call_record', array(
    'action' => 'carOwnerCallRecord'
));

$router->mount($move_car);

/**
 * 修理厂业务
 */

$garage = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'garage'
));

$garage->setPrefix('/garage');

//修理厂数据管理页面
$garage->addGet('/manage', array(
    'action' => 'manage'
));

//获取修理厂数据列表
$garage->add('/list.json', array(
    'action' => 'getGarageList'
));

//添加修理厂
$garage->addPost('/garage.json', array(
    'action' => 'addGarage'
));

//删除修理厂
$garage->addDelete('/garage/{garage_id:\d+}.json', array(
    'action' => 'delGarage'
));

//更新修理厂
$garage->addPut('/garage/{garage:_id:\d+}.json', array(
    'action' => 'updateGarage'
));

$router->mount($garage);

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

//开屏广告管理页面
$router->addGet('/welcomeAdvList', array(
    'controller' => 'welcomepage',
    'action' => 'welcomeAdvList'
));

//汽车服务内容管理页面
$router->addGet('/carService/manage', array(
    'controller' => 'carservice',
    'action' => 'manage'
));

//获取汽车服务列表数据
$router->add('/carService/list.json', array(
    'controller' => 'carservice',
    'action' => 'getCarServiceList'
));

//获取汽车服务图标
$router->addGet('/carService/img/{id:\d+}.png', array(
    'controller' => 'carservice',
    'action' => 'getCarServiceImg'
));

//添加汽车服务
$router->addPost('/carService.json', array(
    'controller' => 'carservice',
    'action' => 'addCarService'
));

//更新汽车服务
$router->addPut('/carService/{id:\d+}.json', array(
    'controller' => 'carservice',
    'action' => 'updateCarService'
));

//删除汽车服务
$router->addDelete('/carService/{id:\d+}.json', array(
    'controller' => 'carservice',
    'action' => 'delCarService'
));

//添加汽车服务
$router->addPost('/carService.json', array(
    'controller' => 'carservice',
    'action' => 'addCarService'
));

//获取开屏广告列表
$router->add('/welcomeAdvList.json', array(
    'controller' => 'welcomepage',
    'action' => 'getWelcomeAdvList'
));

//获取开屏广告图片
$router->addGet('/welcomeAdvPic/{id:\d+}', array(
    'controller' => 'welcomepage',
    'action' => 'getAdvPic'
));

//添加开屏广告
$router->addPost('/welcomeAdv.json', array(
    'controller' => 'welcomepage',
    'action' => 'addWelcomeAdv'
));

//删除开屏广告
$router->addDelete('/welcomeAdv/{id:\d+}.json', array(
    'controller' => 'welcomepage',
    'action' => 'delWelcomeAdv'
));

//更新开屏广告
$router->addPut('/welcomeAdv.json', array(
    'controller' => 'welcomepage',
    'action' => 'updateWelcomeAdv'
));

//弃用开屏广告
$router->addPut('/welcomeAdvDisable/{id:\d+}.json', array(
    'controller' => 'welcomepage',
    'action' => 'welcomeAdvDisable'
));

//使用开屏广告
$router->addPut('/welcomeAdvEnable/{id:\d+}.json', array(
    'controller' => 'welcomepage',
    'action' => 'welcomeAdvEnable'
));

//公告管理页面
$router->addGet('/noticeManage', array(
    'controller' => 'notice',
    'action' => 'noticeManage'
));

//公告管理页面
$router->addGet('/noticeManage', array(
    'controller' => 'notice',
    'action' => 'noticeManage'
));

//添加公告
$router->addPost('/notice.json', array(
    'controller' => 'notice',
    'action' => 'addNotice'
));

//删除公告
$router->addDelete('/notice/{id:\d+}.json', array(
    'controller' => 'notice',
    'action' => 'delNotice'
));

//更新公告
$router->addPut('/notice.json', array(
    'controller' => 'notice',
    'action' => 'updateNotice'
));

//使用公告
$router->addPut('/noticeEnable/{id:\d+}.json', array(
    'controller' => 'notice',
    'action' => 'noticeEnable'
));

//弃用公告
$router->addPut('/noticeDisable/{id:\d+}.json', array(
    'controller' => 'notice',
    'action' => 'noticeDisable'
));

//公告首页推广
$router->addPut('/noticeExtend/{id:\d+}.json', array(
    'controller' => 'notice',
    'action' => 'noticeExtend'
));

//公告取消首页推
$router->addPut('/noticeUnextend/{id:\d+}.json', array(
    'controller' => 'notice',
    'action' => 'noticeUnextend'
));

//车友互动管理页面
$router->addGet('/talk/manage', array(
    'controller' => 'talkinfo',
    'action' => 'manage'
));

//获取车友互动列表
$router->addPost('/talk/list.json', array(
    'controller' => 'talkinfo',
    'action' => 'talkList'
));

//获取车友互动图片
$router->addGet('/talk/{id:\d+}.png', array(
    'controller' => 'talkinfo',
    'action' => 'getTalkPic'
));

//添加车友互动回复
$router->addPost('/talk/{id:\d+}/reply.json', array(
    'controller' => 'talkinfo',
    'action' => 'talkReply'
));

//获取车友互动回复列表
$router->addGet('/talk/{id:\d+}/reply_list.json', array(
    'controller' => 'talkinfo',
    'action' => 'talkReplyList'
));

//改变车友互动发布状态
$router->addPut('/talk/{id:\d+}/state/{state:\d}.json', array(
    'controller' => 'talkinfo',
    'action' => 'talkStateChange'
));

//改变车友互动禁止回复状态
$router->addPut('/talk/{id:\d+}/no_reply/{no_reply:\d}.json', array(
    'controller' => 'talkinfo',
    'action' => 'talkNoReplyChange'
));

//删除指定ID车友互动
$router->addDelete('/talk/{id:\d+}.json', array(
    'controller' => 'talkinfo',
    'action' => 'deleteTalk'
));

//删除指定ID回复
$router->addDelete('/talk/reply/{id:\d+}.json', array(
    'controller' => 'talkinfo',
    'action' => 'deleteTalkReply'
));

//消息推送页面
$router->addGet('/push_message', array(
    'controller' => 'pushmessage',
    'action' => 'index'
));

//消息推送
$router->addPost('/push_message.json', array(
    'controller' => 'pushmessage',
    'action' => 'pushMessage'
));

//获取用户数据列表
$router->add('/user/list.json', array(
    'controller' => 'user',
    'action' => 'getUserList'
));

//用户禁言
$router->add('/user/{user_id:.*}/no_talk.json', array(
    'controller' => 'user',
    'action' => 'setNoTalk'
));

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

//意见反馈管理页面
$router->addGet('/feedBackMng', array(
    'controller' => 'feedback',
    'action' => 'feedBackManage'
));

//获取意见反馈列表
$router->add('/feedBackList.json', array(
    'controller' => 'feedback',
    'action' => 'getFeedBackList'
));

//删除制定ID的意见反馈
$router->addDelete('/feedBack/{id:\d+}.json', array(
    'controller' => 'feedback',
    'action' => 'delFeedBack'
));

//回复指定ID的意见反馈
$router->addPost('/feedBack/{id:\d+}/reply.json', array(
    'controller' => 'feedback',
    'action' => 'replyFeedBack'
));

/*通用*/
$common = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'common'
));

//上传
$common->addPost('/upload.json', array(
    'action' => 'upload'
));

$router->mount($common);

/*其他*/

/**
 * 字段
 */
$field = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'field'
));

$field->setPrefix('/field');

//获取字段数据列表
$field->add('/fieldList.json', array(
    'action' => 'getFieldList'
));

//添加字段
$field->addPost('/field.json', array(
    'action' => 'addField'
));

//更新字段
$field->addPut('/field/{id:\d+}.json', array(
    'action' => 'updateField'
));

//删除字段
$field->addDelete('/field/{id:\d+}.json', array(
    'action' => 'delField'
));

$router->mount($field);


/*
 * GYGD
 */

$gygd = new \Phalcon\Mvc\Router\Group(array(
    'controller' => 'gygd'
));

$gygd->setPrefix('/gygd');

$gygd->addGet('/', array(
    'action' => 'index'
));


//体育馆活动页面
$gygd->addGet('/stadium', array(
    'action' => 'stadium'
));

//获取体育馆活动用户数据
$gygd->add('/stadium/activity_users.json', array(
    'action' => 'getStadiumActivityUserList'
));

//获取体育馆活动用户数据
$gygd->addGet('/stadium/activity_users.csv', array(
    'action' => 'exportStadiumActivityUserData'
));

//删除体育馆参与用户信息
$gygd->addDelete('/stadium/activity/{activity_id:\d+}/users/{user_id:\d+}.json', array(
    'action' => 'delStadiumActivityUser'
));

//更新体育馆活动
$gygd->addPut('/stadium/activity.json', array(
    'action' => 'updateStadiumActivity',
));

//体育馆活动领取
$gygd->addPut('/stadium/activity/user/gain.json', array(
    'action' => 'gainStadiumActivity'
));

//博物馆活动页面
$gygd->addGet('/museum', array(
    'action' => 'museum'
));

//获取博物馆活动参与数据
$gygd->add('/museum/activity_users.json', array(
    'action' => 'getMuseumActivityUserList'
));

//获取博物馆活动摇号
$gygd->add('/museum/activity/{draw_type:.*}/users/random/{people_num:\d+}.json', array(
    'action' => 'getMuseumActivityRandomUser'
));

//博物馆活动用户中奖
$gygd->addPost('/museum/activity/{draw_type:.*}/users/win.json', array(
    'action' => 'museumActivityWinUser'
));

//获取博物馆活动奖品列表
$gygd->add('/museum/activity/{draw_type:.*}/awards.json', array(
    'action' => 'getMuseumActivityAwardList'
));

//删除博物馆活动用户
$gygd->addDelete('/museum/activity/{activity_id:\d+}/users/{user_id:\d+}.json', array(
    'action' => 'delStadiumActivityUser'
));

//更新博物馆活动
$gygd->addPut('/museum/activity.json', array(
    'action' => 'updateStadiumActivity',
));

//博物馆活动领取
$gygd->addPut('/museum/activity/user/gain.json', array(
    'action' => 'gainStadiumActivity'
));

$router->mount($gygd);