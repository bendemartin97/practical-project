<template>
    <div class="connection-panel py-3">
        <!-- Header -->
            <button class="d-flex justify-content-between pe-4 w-100" @click="isOpen = !isOpen">
                <slot name="header">
                    <span class="ps-4 font-bold text-gray-900"> {{ connection.name }} </span>
                </slot>
                <font-awesome-icon v-if="!isOpen" icon="fa-solid fa-angle-down"/>
                <font-awesome-icon v-else icon="fa-solid fa-angle-up"/>
            </button>

        <!-- Content -->
        <div class="px-4 py-2" v-show="isOpen">

            <!-- Spinner -->
            <div v-if="isLoading" class="d-flex justify-content-center align-items-center">
                <font-awesome-icon :icon="['fas', 'spinner']" class="spin text-2xl"/>
            </div>
            <div v-else>
                <slot name="content">
                    <!-- Sequence diagram -->
                    <div class="d-flex justify-content-center mb-3">
                        <img :src="'img/' + connection.img" style="height: 80%;"  alt="rabbitmq"/>
                    </div>

                    <!-- Connection info -->
                    <div class="alert alert-warning d-flex align-items-center" >
                    <span class="d-flex align-items-center justify-content-center border-rounded text-center col-1">
                        <font-awesome-icon style="color: gray" :icon="['fas', 'info']"/>
                    </span>
                        <span class="ps-3 text-justify text-wrap"> {{ connection.info }}</span>
                    </div>

                    <!-- Result -->
                    <div v-if="result" class="d-flex align-items-center justify-content-center">
                        <Result
                            :execution-time="executionTime"
                            :request-time="requestTime"
                            :period-range-counter="periodRangeCounter"
                            :resource-range-counter="resourceRangeCounter"
                            :total-range-counter="totalRangeCounter"
                            :unavailable-total-days-counter="unavailableTotalDaysCounter"
                        />
                    </div>

                    <!-- Error -->
                    <div v-if="error" class="alert alert-danger">
                        <div class="d-flex align-items-center">
                        <span class="d-flex align-items-center justify-content-center border-rounded text-center col-1">
                           X
                        </span>
                            <span class="ps-3 text-justify"> {{ error }}</span>
                        </div>
                    </div>
                </slot>
            </div>

            <!-- Run button -->
            <div class="d-flex justify-content-end">
                <slot name="footer" :setLoading="setLoading" :isLoading="isLoading">
                    <button class="btn btn-outline-primary btn-sm" :class="{disabled: isLoading}"
                            @click="call()">
                        Run
                    </button>
                </slot>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {get} from "lodash";
import Result from "@/Components/Result.vue"
export default {
    name: "ConnectionPanel",
    props: {
        connection: {
            type: Object,
            default: null,
        }
    },
    components: {
        Result
    },
    data() {
        return {
            isLoading: false,
            isOpen: false,

            requestTime: null,
            result: null,
            executionTime: '',
            periodRangeCounter: 0,
            resourceRangeCounter: 0,
            totalRangeCounter: 0,
            unavailableTotalDaysCounter: 0,

            error: null,
        }
    },
    methods: {
        async call(route) {
            try {
                this.isLoading = true
                const start = Date.now();
                let response = await axios.post(
                    "api/" + route,
                )
                this.handleResponse(response)
                const end = Date.now();

                this.requestTime = (end - start) / 1000.0
                this.$emit('call-done')
            } catch (e) {
                this.error = e.message
            } finally {
                this.isLoading = false
            }
        },
        handleResponse(response) {
            this.result = get(response, 'data')
            this.executionTime = this.result['executionTime']
            this.periodRangeCounter = this.result['periodRangeCounter']
            this.resourceRangeCounter = this.result['resourceRangeCounter']
            this.totalRangeCounter = this.result['totalRangeCounter']
            this.unavailableTotalDaysCounter = this.result['unavailableTotalDaysCounter']
        },
        setLoading() {
            this.isLoading = !this.isLoading
        }
    }
}
</script>

<style>
.connection-panel {
    border-radius: 0.5rem;
    border: solid rgba(12, 33, 78, 0.45);
    margin: 0.3rem;
}

.border-rounded {
    border-radius: 20%;
    width: 2rem;
    height: 2rem;
    border: solid gray;
}

.spin {
    -webkit-animation: fa-spin .5s infinite linear;
    animation: fa-spin .5s infinite linear;
}
</style>
