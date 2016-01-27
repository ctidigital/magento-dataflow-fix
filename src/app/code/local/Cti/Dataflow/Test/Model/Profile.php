<?php

class Cti_Dataflow_Test_Model_Profile extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @test
     */
    public function getAdminUserId()
    {
        $id = Mage::getModel('dataflow/profile')->getAdminUserId();
        $this->assertInternalType("int", $id);
    }

}