<?php

namespace Terramar\Bundle\SalesBundle\Entity\Office;

use Doctrine\ORM\Mapping as ORM;
use Terramar\Bundle\SalesBundle\Entity\OfficeUser;
use Terramar\Bundle\SalesBundle\Model\ContractInterface;
use Terramar\Bundle\SalesBundle\Model\ContractSalesProfileInterface;
use Terramar\Bundle\SalesBundle\Model\OfficeSalesProfileInterface;
use Terramar\Bundle\SalesBundle\Entity\Contract;
use Orkestra\Transactor\Entity\Account\BankAccount;
use Orkestra\Transactor\Entity\Account\CardAccount;
use Orkestra\Transactor\Entity\Account\PointsAccount;
use Orkestra\Transactor\Entity\Account\SimpleAccount;
use Orkestra\Transactor\Entity\AbstractAccount;
use Doctrine\Common\Collections\ArrayCollection;
use Terramar\Bundle\SalesBundle\Entity\Office;
use Orkestra\Common\Entity\AbstractEntity;

/**
 * An office's sales profile
 *
 * @ORM\Entity
 * @ORM\Table(name="terramar_office_sales_profiles")
 */
class OfficeSalesProfile extends AbstractEntity implements OfficeSalesProfileInterface, ContractSalesProfileInterface
{
    /**
     * @var bool
     *
     * @ORM\Column(name="autopay", type="boolean")
     */
    protected $autopay = false;

    /**
     * @var \Orkestra\Transactor\Entity\Account\PointsAccount
     *
     * @ORM\ManyToOne(targetEntity="Orkestra\Transactor\Entity\Account\PointsAccount", cascade={"persist"})
     * @ORM\JoinColumn(name="points_account_id", referencedColumnName="id")
     */
    protected $pointsAccount;

    /**
     * @var \Orkestra\Transactor\Entity\AbstractAccount
     *
     * @ORM\ManyToOne(targetEntity="Orkestra\Transactor\Entity\AbstractAccount")
     * @ORM\JoinColumn(name="autopay_account_id", referencedColumnName="id", nullable=true)
     */
    protected $autopayAccount;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Terramar\Bundle\SalesBundle\Model\ContractInterface", cascade={"persist"})
     * @ORM\JoinTable(name="terramar_office_sales_profiles_contracts",
     *      joinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contract_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $contracts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Orkestra\Transactor\Entity\AbstractAccount", cascade={"persist"})
     * @ORM\JoinTable(name="terramar_offices_accounts",
     *      joinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $accounts;

    /**
     * @var \Terramar\Bundle\SalesBundle\Entity\Office
     *
     * @ORM\OneToOne(targetEntity="Terramar\Bundle\SalesBundle\Entity\Office", inversedBy="profile")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    protected $office;

    /**
     * @var \Terramar\Bundle\SalesBundle\Entity\OfficeUser
     *
     * @ORM\OneToOne(targetEntity="Terramar\Bundle\SalesBundle\Entity\OfficeUser", cascade={"persist"})
     * @ORM\JoinColumn(name="office_user_id", referencedColumnName="id")
     */
    protected $systemOfficeUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contracts = new ArrayCollection();
        $this->accounts = new ArrayCollection();
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $accounts
     */
    public function setAccounts($accounts)
    {
        $this->accounts = $accounts;
    }

    /**
     * @param \Orkestra\Transactor\Entity\AbstractAccount $account
     */
    public function addAccount(AbstractAccount $account)
    {
        if ($account instanceof PointsAccount) {
            $this->pointsAccount = $account;
        }

        $this->accounts->add($account);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * @param Contract $contract
     */
    public function addContract(ContractInterface $contract)
    {
        $this->contracts->add($contract);
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $contracts
     */
    public function setContracts($contracts)
    {
        $this->contracts = $contracts;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|ContractInterface[]
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    /**
     * @param \Terramar\Bundle\SalesBundle\Entity\Office $office
     */
    public function setOffice(Office $office)
    {
        $this->office = $office;
    }

    /**
     * @return \Terramar\Bundle\SalesBundle\Entity\Office
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param \Orkestra\Transactor\Entity\AbstractAccount $account
     */
    public function setAutopayAccount(AbstractAccount $account = null)
    {
        $this->autopayAccount = $account;
        if (null !== $account && !$this->accounts->contains($account)) {
            $this->accounts->add($account);
        }
    }

    /**
     * @return \Orkestra\Transactor\Entity\AbstractAccount
     */
    public function getAutopayAccount()
    {
        return $this->autopayAccount;
    }

    /**
     * @param boolean $autopay
     */
    public function setAutopay($autopay)
    {
        $this->autopay = $autopay ? true : false;
    }

    /**
     * @return boolean
     */
    public function isAutopay()
    {
        return $this->autopay;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBankAccounts()
    {
        return $this->accounts->filter(function(AbstractAccount $account) {
            return $account instanceof BankAccount;
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCardAccounts()
    {
        return $this->accounts->filter(function(AbstractAccount $account) {
            return $account instanceof CardAccount;
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAutopayAccounts()
    {
        return $this->accounts->filter(function(AbstractAccount $account) {
            return !$account instanceof SimpleAccount && !$account instanceof PointsAccount && $account->isActive();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSimpleAccounts()
    {
        return $this->accounts->filter(function(AbstractAccount $account) {
            return $account instanceof SimpleAccount;
        });
    }

    /**
     * @param \Orkestra\Transactor\Entity\Account\PointsAccount $account
     */
    public function setPointsAccount(PointsAccount $account)
    {
        $this->pointsAccount = $account;
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
        }
    }

    /**
     * @return \Orkestra\Transactor\Entity\Account\PointsAccount
     */
    public function getPointsAccount()
    {
        return $this->pointsAccount;
    }

    /**
     * @param \Terramar\Bundle\SalesBundle\Entity\OfficeUser $systemOfficeUser
     */
    public function setSystemOfficeUser(OfficeUser $systemOfficeUser = null)
    {
        $this->systemOfficeUser = $systemOfficeUser;
    }

    /**
     * @return \Terramar\Bundle\SalesBundle\Entity\OfficeUser
     */
    public function getSystemOfficeUser()
    {
        return $this->systemOfficeUser;
    }
}
