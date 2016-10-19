<?php

class ProvinceController extends ControllerBase
{

    public function getCityListAction($province_id)
    {
        $city_list = Province::getCityListByPid($province_id);

        $count = count($city_list);

        $this->view->setVar('data', array(
            'total' => $count,
            'count' => $count,
            'rows' => $city_list
        ));
    }

}

