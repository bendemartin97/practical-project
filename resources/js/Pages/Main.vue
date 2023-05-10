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
                    <PrimaryButton
                        :width="'w-25'"
                        :type="'button'"
                        :style="{opacity: disabled ? 0.5 : 1}"
                        @click="seedResources"
                        :disabled="disabled"
                    >
                        Create 10 Resources
                    </PrimaryButton>
                </div>

                <div class="d-flex justify-content-between align-content-between my-3">
                    <h3 class="text-2xl font-bold text-gray-900">Bookings: {{ bc }}</h3>
                    <PrimaryButton
                        :width="'w-25'"
                        :type="'button'"
                        :style="{opacity: disabled ? 0.5 : 1}"
                        @click="seedBookings"
                        :disabled="disabled"
                    >
                        Create Bookings
                    </PrimaryButton>
                </div>
                <button :type="'button'" class="btn btn-outline-danger w-100 mb-5" @click="flushDatabase">
                    Flush database
                </button>

                <Chart ref="chartComponent"/>

                <div class="d-flex flex-column justify-content-center mb-5">
                    <h1 class="ps-2 text-2xl font-bold text-gray-900">Test all connections in row</h1>
                    <ConnectionPanel
                        @call-done="requestChartData"
                    >
                        <template #header>
                            <span class="ps-4 font-bold text-gray-900"> Run the simulation </span>
                        </template>
                        <template #content>
                            <div class="alert alert-warning d-flex align-items-center">
                                <span
                                    class="d-flex align-items-center justify-content-center border-rounded text-center col-1">
                                    <font-awesome-icon style="color: gray" :icon="['fas', 'info']"/>
                                </span>
                                <span class="ps-3 text-justify text-wrap">
                                    All methods are executed one after the other with different number of resources and bookings. The current database will be deleted. This process can take a few minutes. Then the results are displayed on the chart
                                </span>
                            </div>
                        </template>
                        <template #footer="{ setLoading, isLoading }">
                            <button class="btn btn-outline-primary btn-sm" :class="{disabled: isLoading}"
                                    @click="simulate(setLoading)">
                                Run
                            </button>
                        </template>
                    </ConnectionPanel>
                </div>


                <div class="d-flex flex-column justify-content-center">
                    <h1 class="ps-2 text-2xl font-bold text-gray-900">Test the different connections</h1>
                    <div v-for="connection in connections">
                        <ConnectionPanel :connection="connection" @call-done="requestChartData"/>
                    </div>
                </div>
            </div>
        </div>
        <span class="text-center mt-3">
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
import {connections} from "@/utils/connectionHelper";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import ConnectionPanel from "@/Components/ConnectionPanel.vue";
import Chart from "@/Components/Chart.vue";
import { ROUTES } from "@/utils/connectionHelper";

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
    computed: {
        disabled() {
            return this.rc > 100 || this.bc > 1000
        }
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

            await this.requestChartData
        },
        async requestChartData() {
            await this.$refs.chartComponent.requestChartData()
        },
        async simulate(callback) {
            callback()
            try {
                await this.flushDatabase()
                for(let i = 0; i < 10; i++) {
                    await this.seedResources()
                    await this.seedBookings()

                    for (const route of ROUTES) {
                        await axios.post("api/" + route)
                    }
                }

                await this.requestChartData()
            } catch (e) {
                console.log(e)
            } finally {
                callback()
            }
        }
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

html {
    overflow-y: scroll;
}
</style>


