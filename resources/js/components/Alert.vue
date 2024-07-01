<template>
    <div :class="estilo" role="alert">

        {{ titulo }}
        <ul class="mb-0" v-for="(mensagem, campo) in detalhes.dados" :key="campo" v-if="detalhes.dados">
            <!-- <strong>{{ campo }}:</strong> -->
                <li >{{ mensagem }}</li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['tipo', 'titulo', 'detalhes'],
    computed: {
        estilo() {
            return 'alert alert-' + this.tipo;
        }
    }
};
</script>
