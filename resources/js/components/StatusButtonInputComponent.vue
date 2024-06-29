<template>
    Status
    <br>
    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
        <div v-for="statusItem in statusList":key="statusItem.id"
        class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio"
            :id="'btnradio'+statusItem.id" autocomplete="off" :checked="status.id === statusItem.id" @change="handleStatusChange(statusItem.id)">

            <label
            :class="'btn btn-outline-'+statusItem.classe" :for="'btnradio'+statusItem.id">{{ statusItem.name }}</label>
        </div>
    </div>
</template>

<script>
export default {
    props: ['status', 'statusList'],
    methods:{
        handleStatusChange(id){
            this.$store.state.updateStatusId = id
        }
    }
};
</script>
