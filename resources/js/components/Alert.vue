<template>
    <div :class="estilo" role="alert">
        {{ titulo }} - {{ detalhes.mensagem }}
        <br>
        <ul v-if="detalhes.dados">
            <li class="mb-0" v-for="e, key in detalhes.dados" :key="key">{{ e[0] }}</li><br>
        </ul>
    </div>
</template>
  
<script>
export default {
    props: ['tipo', 'titulo', 'detalhes'],
    computed: {
        estilo() {
            return 'alert alert-' + this.tipo
        }
    }

};
</script>
  