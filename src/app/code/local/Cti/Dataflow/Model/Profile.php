<?php

/**
 * Class Cti_Dataflow_Model_Profile
 *
 * @package Cti_Dataflow
 * @author Chris Gan <c.gan@ctidigital.com>
 *
 * Override to implement fixes for showing the profile history
 *
 */
class Cti_Dataflow_Model_Profile extends Mage_Dataflow_Model_Profile
{

    /**
     * Implement the missing getAdminUserId method
     * @return mixed
     */
    protected function getAdminUserId()
    {
        return Mage::getSingleton('admin/session')->getUser()->getId();
    }


    /**
     * Run profile
     * Adds in the saving of the admin id
     * @return Mage_Dataflow_Model_Profile
     */
    public function run()
    {

        $adminUserId = $this->getAdminUserId();

        /**
         * Save history
         */
        Mage::getModel('dataflow/profile_history')
            ->setProfileId($this->getId())
            ->setActionCode('run')
            ->setUserId($adminUserId)
            ->save();

        /**
         * Prepare xml convert profile actions data
         */
        $xml = '<convert version="1.0"><profile name="default">' . $this->getActionsXml()
            . '</profile></convert>';
        $profile = Mage::getModel('core/convert')
            ->importXml($xml)
            ->getProfile('default');
        /* @var $profile Mage_Dataflow_Model_Convert_Profile */

        try {
            $batch = Mage::getSingleton('dataflow/batch')
                ->setProfileId($this->getId())
                ->setStoreId($this->getStoreId())
                ->save();
            $this->setBatchId($batch->getId());

            $profile->setDataflowProfile($this->getData());
            $profile->run();
        } catch (Exception $e) {
            echo $e;
        }

//        if ($batch) {
//            $batch->delete();
//        }

        $this->setExceptions($profile->getExceptions());
        return $this;
    }

}