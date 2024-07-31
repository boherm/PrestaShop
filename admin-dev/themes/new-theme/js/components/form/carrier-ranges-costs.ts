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

import ComponentsMap from '@components/components-map';
import {createApp} from 'vue';
import {createI18n} from 'vue-i18n';
import CarrierRangesCostsComponent from '@pages/carrier/form/components/CarrierRangesCosts.vue';
import EventEmitter from '@components/event-emitter';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import CarrierFormEventMap from '@pages/carrier/form/carrier-form-event-map';

export default class CarrierRangesCosts {
  private readonly eventEmitter: typeof EventEmitter;

  constructor() {
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.initRangesCosts();
  }

  initRangesCosts(): void {
    // Retrieve the main container
    const $container = <HTMLElement>document.getElementById(ComponentsMap.carrierRanges.rangesCostsMainContainerId);
    const $data = <HTMLFormElement>document.getElementById(ComponentsMap.carrierRanges.rangesCostsDataId);

    // Create the app container
    const $appContainer = <HTMLElement>document.createElement('div');
    $appContainer.setAttribute('id', ComponentsMap.carrierRanges.rangesCostsAppContainerId);
    $container.appendChild($appContainer);

    // Retreive zones and translations from the container
    const zonesData = JSON.parse($container.dataset.zones || '{}');
    const i18n = createI18n({
      locale: 'en',
      formatter: new ReplaceFormatter(),
      messages: {en: JSON.parse($container.dataset.translations || '{}')},
    });

    // Initialize the Vue app with the CarrierRangesModal component
    const vueApp = createApp(CarrierRangesCostsComponent, {
      i18n,
      eventEmitter: this.eventEmitter,
      zonesData,
      currencySymbol: $container.dataset.currency || '',
      zonesPrices: JSON.parse($data.value || '{}'),
    }).use(i18n);

    // Mount the Vue app to the app container
    vueApp.mount('#' + ComponentsMap.carrierRanges.rangesCostsAppContainerId);
  }
}
