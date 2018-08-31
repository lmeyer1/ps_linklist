<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\LinkList\Form\ChoiceProvider;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class CMSCategoryChoiceProvider implements FormChoiceProviderInterface
{
    /** @var Connection */
    private $connection;
    /** @var string */
    private $dbPrefix;
    /** @var integer */
    private $idLang;
    /** @var integer */
    private $idShop;

    /**
     * CMSCategoryChoiceProvider constructor.
     * @param Connection $connection
     * @param string     $dbPrefix
     * @param int        $idLang
     * @param int        $idShop
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        $idLang,
        $idShop
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->idLang = $idLang;
        $this->idShop = $idShop;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('cc.id_cms_category, ccl.name')
            ->from($this->dbPrefix.'cms_category', 'cc')
            ->innerJoin('cc', $this->dbPrefix.'cms_category_lang', 'ccl', 'cc.id_cms_category = ccl.id_cms_category')
            ->innerJoin('cc', $this->dbPrefix.'cms_category_shop', 'ccs', 'cc.id_cms_category = ccs.id_cms_category')
            ->andWhere('cc.active = 1')
            ->andWhere('ccl.id_lang = :idLang')
            ->andWhere('ccs.id_shop = :idShop')
            ->setParameter('idLang', $this->idLang)
            ->setParameter('idShop', $this->idShop)
            ->orderBy('ccl.name')
        ;

        $categories = $qb->execute()->fetchAll();
        $choices = [];
        foreach ($categories as $category) {
            $choices[$category['name']] = $category['id_cms_category'];
        }

        return $choices;
    }
}
