<?php

namespace Terramar\Bundle\SalesBundle\Model;

interface SalesProfileInterface
{
    /**
     * Gets all of a SalesProfile's Transactor accounts
     *
     * @return \Doctrine\Common\Collections\Collection|\Orkestra\Transactor\Entity\AbstractAccount[]
     */
    public function getAccounts();

    /**
     * Gets all of a SalesProfile's Transactor accounts that may be used for Autopay
     *
     * @return \Doctrine\Common\Collections\Collection|\Orkestra\Transactor\Entity\AbstractAccount[]|
     */
    public function getAutopayAccounts();

    /**
     * Gets the assigned autopay account
     *
     * @return \Orkestra\Transactor\Entity\AbstractAccount
     */
    public function getAutopayAccount();

    /**
     * Sets autopay to enabled or disabled
     *
     * @param boolean $autopay
     */
    public function setAutopay($autopay);

    /**
     * Returns true if autopay is enabled
     *
     * @return boolean
     */
    public function isAutopay();

    /**
     * Gets the associated Points account
     *
     * @return \Orkestra\Transactor\Entity\Account\PointsAccount
     */
    public function getPointsAccount();
}
