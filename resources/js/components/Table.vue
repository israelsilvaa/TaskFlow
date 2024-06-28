<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="coluna, key in titulos" :key="key" class="text-uppercase">{{ coluna }}</th>
                    <td v-if="concluir || visualizar.visivel || atualizar || remover" colspan="2" class="text-center">Ações</td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj in dados" :key="obj.id">
                    <template v-for="(valor, chave) in obj" :key="chave">
                        <td v-if="titulos.includes(chave)" :key="chave">{{ valor }}</td>
                    </template>
                    <td v-if="concluir || visualizar.visivel || atualizar || remover">
                        <button v-if="concluir" class="btn btn-outline-success btn-sm me-1">Concluir</button>
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm me-1" :data-bs-toggle="visualizar.dataBsToggle" :data-bs-target="visualizar.dataBsTarget" @click="setStore(obj)">Ver</button>
                        <button v-if="atualizar" class="btn btn-outline-warning btn-sm text-dark me-1">Atualizar</button>
                        <button v-if="remover" class="btn btn-outline-danger btn-sm">Remover</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</template>

<script>
export default {
    methods:{
        setStore(obj){
            this.$store.state.item = obj 
            this.$store.state.relacionados = obj.assigned_users.map(user => user.name).join(', ');
            console.log(this.$store.state.relacionados)
        }
    },
    props: ['dados', 'titulos', 'concluir', 'visualizar', 'atualizar', 'remover']
}
</script>
