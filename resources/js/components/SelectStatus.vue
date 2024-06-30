<template>
    <label :for="id" class="form-label m-0 h5">{{ titulo }}</label>
    <slot></slot>
    <div :id="idHelp" class="form-text text-muted">{{ textoAjuda }}</div>

</template>

<script>
export default {
    props: ['id', 'titulo', 'idHelp', 'textoAjuda'],
};
</script>
