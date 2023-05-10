<template>
    <Head :title="'Martin Bende Praxisprojekt'"/>
    <div class="main-container w-100 d-flex flex-column justify-content-center align-items-center">
        <div class="panel flex-grow-1">
            <div class="px-2">
                <div class="pb-2 d-flex justify-content-center">
                    <h1 class="text-3xl font-bold text-gray-900">Martin Bende Praxisprojekt</h1>
                </div>
                <div class="d-flex  justify-content-between align-content-between my-3">
                    <h3 class="text-2xl font-bold text-gray-900">Resources: {{ rc }}</h3>
                    <PrimaryButton :width="'w-25'" :type="'button'" @click="seedResources">
                        Create 10 Resources
                    </PrimaryButton>
                </div>

                <div class="d-flex justify-content-between align-content-between my-3">
                    <h3 class="text-2xl font-bold text-gray-900">Bookings: {{ bc }}</h3>
                    <PrimaryButton :width="'w-25'" :type="'button'" @click="seedBookings">
                        Create Bookings
                    </PrimaryButton>
                </div>
                <button :type="'button'" class="btn btn-outline-danger w-100 mb-5" @click="flushDatabase">
                    Flush database
                </button>

                <Chart ref="chartComponent"/>

                <div class="d-flex flex-column justify-content-center">
                    <h1 class="ps-2 text-2xl font-bold text-gray-900">Test the different connections</h1>
                    <div v-for="connection in connections">
                        <ConnectionPanel :connection="connection" @call-done="requestChartData"/>
                    </div>
                </div>
                <div v-if="result">
                    {{ result }}
                </div>
            </div>
        </div>
        <span class="text-center">
                    Powered by
            <a href="https://anny.co" target="_blank" style="text-decoration:none">anny GmbH</a>
        </span>
        <div class="d-flex flex-grow-1 flex-column justify-content-center align-items-baseline">
            <img src="anny.png" style="height: 5rem; width: 5rem" alt="anny"/>
        </div>
    </div>
</template>

<script lang="ts">
import {get} from 'lodash'
import {Head} from "@inertiajs/vue3";
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { connections } from "@/utils/connectionHelper";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import ConnectionPanel from "@/Components/ConnectionPanel.vue";
import Chart from "@/Components/Chart.vue";

export default {
    name: "Main",
    props: {
        resourceCounter: Number,
        bookingsCounter: Number,
    },
    data() {
        return {
            connections: connections,

            result: null,
            rc: this.resourceCounter,
            bc: this.bookingsCounter,
        }
    },
    components: {
        ConnectionPanel,
        FontAwesomeIcon,
        Head,
        PrimaryButton,
        Chart
    },
    methods: {
        async seedResources() {
            let response = await axios.post(
                "api/seedResources",
            )
            this.rc = get(response, 'data.resourceCounter')
        },
        async seedBookings() {
            let response = await axios.post(
                "api/seedBookings",
            )
            this.bc = get(response, 'data.bookingsCounter')
        },
        async flushDatabase() {
            await axios.post(
                "api/flushDatabase",
            )

            this.rc = 0
            this.bc = 0

            await this.$refs.chartComponent.requestChartData()
        },
        async requestChartData() {
            await this.$refs.chartComponent.requestChartData()
        },
    },
}
</script>

<style lang="scss">

.main-container {
    background-color: #F3F4F6FF;
}

.panel {
    min-width: 85%;
    max-width: 85%;
    background-color: #ffffff;
    border: none;
    border-radius: 1rem;
    min-height: 100vh;
}

html { overflow-y: scroll; }
</style>


