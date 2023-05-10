<template>
    <div class="d-flex flex-column align-items-start justify-content-center">
        <span>
            <font-awesome-icon class="me-2" style="color: green" :icon="['fas', 'check']"/>
            <b class="text-bold text-xl" style="color: green">The request was done in {{
                    requestTime
                }} seconds</b>
        </span>
        <span>
            <font-awesome-icon class="me-2" :icon="['far', 'clock']"/>
            The execution time of the algorithm is <b
            :style="{color: executionTimeColor}">{{ executionTime }}
        </b>
        </span>
        <span>
            <font-awesome-icon class="me-2" :icon="['far', 'calendar-xmark']"/>
            {{ periodRangeCounter }} days were checked</span>
        <span>
            <font-awesome-icon class="me-2" :icon="['fas', 'swatchbook']"/>
            {{ resourceRangeCounter }} resources were included
        </span>
        <span>
             <font-awesome-icon class="me-2" :icon="['fas', 'rotate-left']"/>
            Totally loop counter is {{ totalRangeCounter }}
        </span>
        <span>
            <font-awesome-icon class="me-2" :icon="['far', 'calendar-days']"/>
            Number of unavailable days is {{ unavailableTotalDaysCounter }}
        </span>
    </div>
</template>

<script>
export default {
    name: "Result",
    props: {
        requestTime: {
            type: String,
            default: '0.0'
        },
        executionTime: {
            type: String,
            default: '0.0'
        },
        periodRangeCounter: {
            type: Number,
            default: 0
        },
        resourceRangeCounter: {
            type: Number,
            default: 0
        },
        totalRangeCounter: {
            type: Number,
            default: 0
        },
        unavailableTotalDaysCounter: {
            type: Number,
            default: 0
        },
    }
}
</script>

<style scoped>

</style>
