<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Manufacturer\AbstractManufacturerHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\AddManufacturerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Handles command which adds new manufacturer using legacy object model
 */
final class AddManufacturerHandler extends AbstractManufacturerHandler implements AddManufacturerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddManufacturerCommand $command)
    {
        $manufacturer = new Manufacturer();
        $this->fillLegacyManufacturerWithData($manufacturer, $command);

        try {

            if (!$manufacturer->add()) {
                throw new ManufacturerException(
                    sprintf('Failed to add new manufacturer "%s"', $command->getName())
                );
            }
            $this->addShopAssociation($manufacturer, $command);

            if (null !== $command->getLogoImagePath()) {
                $this->uploadImage($manufacturer->id, $command->getLogoImagePath());
            }

        } catch (\PrestaShopException $e) {
            throw new ManufacturerException(
                sprintf('Failed to add new manufacturer "%s"', $command->getName())
            );
        }

        return new ManufacturerId((int) $manufacturer->id);
    }

    /**
     * Add manufacturer and shop association
     *
     * @param Manufacturer $manufacturer
     * @param AddManufacturerCommand $command
     *
     * @throws \PrestaShopDatabaseException
     */
    private function addShopAssociation(Manufacturer $manufacturer, AddManufacturerCommand $command)
    {
        $this->associateWithShops(
            $manufacturer,
            $command->getShopAssociation()
        );
    }

    /**
     * @param Manufacturer $manufacturer
     * @param AddManufacturerCommand $command
     */
    private function fillLegacyManufacturerWithData(Manufacturer $manufacturer, AddManufacturerCommand $command)
    {
        $manufacturer->name = $command->getName();
        $manufacturer->short_description = $command->getLocalizedShortDescriptions();
        $manufacturer->description = $command->getLocalizedDescriptions();
        $manufacturer->meta_title = $command->getLocalizedMetaTitles();
        $manufacturer->meta_description = $command->getLocalizedMetaDescriptions();
        $manufacturer->meta_keywords = $command->getLocalizedMetaKeywords();
        $manufacturer->active = $command->isEnabled();
    }
}
