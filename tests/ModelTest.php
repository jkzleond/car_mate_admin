<?php
namespace Test;

/**
 * Class UnitTest
 */
class UnitTest extends \UnitTestCase {

    public function testInsurance()
    {
        /**
         * insurance param add/del
         */
        $data = array(
            'car_price' => null,
            'car_seat' => null,
            'first_year' => null,
            'first_month' => null,
            'insurance_year' => null,
            'insurance_month' => null,
            'compulsory_id' => null,
            'damage_id' => null,
            'third' => null,
            'driver' => null,
            'passenger' => null,
            'robbery_id' => null,
            'glass_id' => null,
            'optional_deductible' => null,
            'not_deductible_id' => null,
            'new_device' => null,
            'goods' => null,
            'offshore' => null,
            'ton' => null,
            'scratch' => null,
            'self_ignition_id' => null,
            'create_date' => date('Y-m-d H:i:s'),
            'discount_company_id' => null,
            'tax' => null
        );

        $new_id = \Insurance::addInsuranceFinalParam($data);

        $this->assertInternalType('string', $new_id, 'addInsuranceFinalParam is fail');

        $suc_del = \Insurance::delInsuranceFinalParam($new_id);

        $this->assertTrue($suc_del, 'cant delInsuranceFinalParam '.$new_id);

        /**
         * insurance final result add/del
         */
        $data = array(
            'round_year' => '2014',
            'last_month' => '4',
            'round_month' => '6',
            'coefficient' => null,
            'standard_compulsory_insurance' => null,
            'after_discount_compulsory_insurance' => null,
            'single_notDeductible_compulsory_insurance' => null,
            'standard_damage_insurance' => null,
            'after_discount_damage_insurance' => null,
            'single_not_deductible_damage_insurance' => null,
            'standard_third' => null,
            'after_discount_third' => null,
            'single_not_deductible_third' => null,
            'standard_driver' => null,
            'after_discount_driver' => null,
            'single_not_deductible_driver' => null,
            'standard_passenger' => null,
            'after_discount_passenger' => null,
            'single_not_deductible_passenger' => null,
            'standard_robbery' => null,
            'after_discount_robbery' => null,
            'single_not_deductible_robbery' => null,
            'standard_glass' => null,
            'after_discount_glass' => null,
            'single_not_deductible_glass' => null,
            'standard_optional_deductible' => null,
            'after_discount_optional_deductible' => null,
            'standard_not_deductible' => null,
            'after_discount_not_deductible' => null,
            'total_standard' => null,
            'total_after_discount' => null,
            'total_single_not_deductible' => null,
            'standard_new_device' => null,
            'after_discount_new_device' => null,
            'standard_goods' => null,
            'after_discount_goods' => null,
            'standard_offshore' => null,
            'after_discount_offshore' => null,
            'trailer_standard_compulsory' => null,
            'trailer_preferential_compulsory' => null,
            'trailer_standard_damage' => null,
            'trailer_preferential_damage' => null,
            'trailer_standard_third' => null,
            'trailer_preferential_third' => null,
            'trailer_standard_driver' => null,
            'trailer_preferential_driver' => null,
            'trailer_standard_passenger' => null,
            'trailer_preferential_passenger' => null,
            'trailer_standard_robbery' => null,
            'trailer_preferential_robbery' => null,
            'trailer_standard_glass' => null,
            'trailer_preferential_glass' => null,
            'trailer_standard_optional_deductible' => null,
            'trailer_preferential_optional_deductible' => null,
            'trailer_standard_not_deductible' => null,
            'trailer_preferential_not_deductible' => null,
            'trailer_standard_new_device' => null,
            'trailer_preferential_new_device' => null,
            'trailer_standard_goods' => null,
            'trailer_preferential_goods' => null,
            'trailer_standard_offshore' => null,
            'trailer_preferential_offshore' => null,
            'standard_scratch' => null,
            'after_discount_scratch' => null,
            'single_not_deductible_scratch' => null,
            'standard_self_ignition' => null,
            'after_discount_self_ignition' => null,
            'single_not_deductible_self_ignition' => null,
            'business' => 230143.00
        );

        $new_id = \Insurance::addInsuranceFinalResult($data);

        $this->assertInternalType('string', $new_id, 'addInsuranceFinalResult is fail');

        $suc_del = \Insurance::delInsuranceFinalResult($new_id);

        $this->assertTrue($suc_del, 'cant delInsuranceFinalResult '.$new_id);

    }
}
