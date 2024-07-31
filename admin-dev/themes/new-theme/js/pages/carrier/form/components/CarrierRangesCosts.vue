<template>
  <div v-for="zonePrice in zonesPrices" class="card shadow-none mb-2">
    <div class="card-body p-4">
      <div class="d-flex mb-2 justify-content-between">
        <h2 class="card-title">{{ this.$props.zonesData[zonePrice.zoneId] }}</h2>
        <button type="button" class="btn btn-outline-danger">
          <i class="material-icons">delete</i>
          <span class="pl-1">{{ $t('action.deleteZone') }}</span>
        </button>
      </div>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-2">
            <label class="mb-2">{{ $t('label') }}</label>
          </div>
          <div class="col-12 col-md-10">
            <div class="d-flex gap-3 flex-wrap">

              <div v-for="r in zonePrice.ranges" class="p-1 col-12 col-md-6 col-lg-3">
                <div class="border p-2">
                  <h3 class="mb-3">{{ getRangeTitle(r) }}</h3>
                  <div class="mb-1">{{ $t('price.label') }}</div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">{{ currencySymbol }}</span>
                    </div>
                    <input type="number" v-model="r.price" class="form-control form-min" inputmode="decimal">
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import { defineComponent } from 'vue';
  import CarrierFormEventMap from '@pages/carrier/form/carrier-form-event-map';

  interface ZonePrice {
    zoneId: number;
    ranges: PriceRange[];
  }

  interface Range {
    from: number;
    to: null|number;
  }

  interface ZonesUpdatedEvent {
    zones: Array<number>;
    ranges: Range[];
  }

  interface PriceRange {
    from: number;
    to: null|number;
    price: number;
  }

  interface CarrierRangesCostsData {
    zonesPrices: ZonePrice[];
    rangeSymbol: string;
  }

  export default defineComponent({
    name: 'CarrierRangesCosts',
    data(): CarrierRangesCostsData {
      return {
        zonesPrices: [],
        rangeSymbol: '',
      };
    },
    props: {
      eventEmitter: {
        type: Object,
        required: true,
      },
      zonesData: {
        type: Object,
        required: true,
      },
      currencySymbol: {
        type: String,
        required: true,
      },
      zonesPrices: {
        type: Object as () => ZonePrice[],
        required: true,
      },
    },
    mounted() {
      this.zonesPrices = this.$props.zonesPrices;
      // If we need to change the shipping method symbol
      this.eventEmitter.on(CarrierFormEventMap.shippingMethodChange, (symbol: string) => this.rangeSymbol = symbol);

      // If we need to change the zones
      this.eventEmitter.on(CarrierFormEventMap.zonesUpdated, (data: ZonesUpdatedEvent) => {

        console.log('COSTS', data);
      });
    },
    methods: {
      getRangeTitle(range: PriceRange): string {
        const to = range.to === null ? '\u221E' : range.to;
        return `${range.from}${this.rangeSymbol}\u0020-\u0020${to}${this.rangeSymbol}`;
      },
    },
  });
</script>
