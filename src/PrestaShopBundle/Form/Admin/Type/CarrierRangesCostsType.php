<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CarrierRangesCostsType is a form type used to set prices by Carrier ranges fo form.
 *
 * $builder
 *     ->add('ranges', CarrierRangesCostsType::class)
 * ;
 */
class CarrierRangesCostsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ConfigurableFormChoiceProviderInterface $zonesChoiceProvider,
        private readonly CurrencyDataProviderInterface $currencyDataProvider
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('data', HiddenType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'attr' => [
                'data-zones' => json_encode($this->getZonesData()),
                'data-currency' => $this->currencyDataProvider->getDefaultCurrencySymbol(),
                'data-translations' => json_encode([
                    'action.deleteZone' => $this->trans('Delete Zone', 'Admin.Shipping.Feature'),
                    'label' => $this->trans('Shipping Costs', 'Admin.Shipping.Feature'),
                    'price.label' => $this->trans('Price (VAT excl.)', 'Admin.Shipping.Feature'),
                ]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'carrier_ranges_costs';
    }

    private function getZonesData(): array
    {
        return array_flip($this->zonesChoiceProvider->getChoices([]));
    }
}
