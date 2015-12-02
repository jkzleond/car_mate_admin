<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-19
 * Time: 下午10:40
 */

$eventsManager = $di->getShared('eventsManager');

/**
 * 权限验证
 */
$eventsManager->attach('dispatch', new AuthFilter($di));
/**
 * utf8编码
 */
$eventsManager->attach('dispatch', new UTF8EnCodingFilter($di));
/**
 * ajax请求json自动转换
 */
$eventsManager->attach('dispatch', new AjaxFilter($di));

/**
 * 保险20免一活动插件
 */
$insurance_draw_filter = new InsuranceDrawFilter($di);
$eventsManager->attach('insurance', $insurance_draw_filter);