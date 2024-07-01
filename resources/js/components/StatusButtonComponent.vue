<template>
    <div class="form-group">
        <button type="button" :class="['btn', 'btn-' + buttonColor, 'btn-sm']"> 
            <i v-if="status === 'Finalizado'" class="fa-solid fa-check"style="color: #ffffff;"></i>
            <i v-else-if="status === 'Em andamento'" class="fa-solid fa-repeat"style="color: #ffffff;"></i>
            <i v-else-if="status === 'Parado'" class="fa-solid fa-triangle-exclamation"style="color: #ffffff;"></i>
            <!-- <i v-else-if="status === 'Não iniciado'" class="fa-solid fa-seedling"style="color: #ffffff;"></i> -->
            <i v-else-if="status === 'Não iniciado'" class="fa-solid fa-envelope"style="color: #ffffff;"></i>
            <!-- <i class="fa-solid fa-triangle-exclamation"></i> -->
            {{ status }}</button>
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
