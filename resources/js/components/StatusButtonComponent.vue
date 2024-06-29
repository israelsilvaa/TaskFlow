<template>
    <div class="form-group">
        <button type="button" :class="['btn', 'btn-' + buttonColor]">{{ status }}</button>
    </div>
</template>

<script>
export default {
    props: ['status'],
    computed: {
        buttonColor() {
            // Mapear o valor de obj.name para a classe de cor correspondente
            const colorMap = {
                'Não Iniciado': 'secondary',
                'Parado': 'danger',
                'Em andamento': 'info',
                'Finalizado': 'success'
            };
            return colorMap[this.status] || 'secondary'; // Cor padrão
        }
    }
};
</script>
