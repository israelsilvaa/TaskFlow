<template>
    <div>
        <table class="table table-hover" v-if="dados.length > 0">
            <thead>
                <tr>
                    <th scope="col" v-for="coluna, key in titulos" :key="key" class="text-uppercase">{{ coluna }}</th>
                    <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel" colspan="2"
                        class="text-center w-auto">Ações</td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj in dados" :key="obj.id">
                    <template v-for="(valor, chave) in obj" :key="chave">
                        <td v-if="titulos.includes(chave)" :key="chave">{{ valor }}</td>
                    </template>
                    <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel" class="text-center w-auto">
                        <!-- <button v-if="concluir.visivel" class="btn btn-outline-success btn-sm me-1" @click="concluirTask(obj)"><i class="fa-solid fa-check"></i></button> -->
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm me-1"
                            :data-bs-toggle="visualizar.dataBsToggle" :data-bs-target="visualizar.dataBsTarget"
                            @click="setStore(obj)"><i class="fa-solid fa-eye"></i></button>
                        <button v-if="atualizar.visivel" class="btn btn-outline-warning btn-sm me-1"
                            :data-bs-toggle="atualizar.dataBsToggle" :data-bs-target="atualizar.dataBsTarget"
                            @click="setStore(obj)"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button v-if="remover.visivel" class="btn btn-outline-danger btn-sm"
                            :data-bs-toggle="remover.dataBsToggle" :data-bs-target="remover.dataBsTarget"
                            @click="setStore(obj)"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else class="alert alert-info" role="alert">
            <i class="fa-solid fa-bell"></i> Você não tem task atribuídas...
        </div>
    </div>

</template>

<script>
export default {
    methods: {
        setStore(obj) {
            this.$store.state.transacao.status = ''
            this.$store.state.transacao.detalhes = ''

            this.$store.state.item = { ...obj };
            this.$store.state.relacionados = obj.assigned_users.map(user => user.name).join(', ');
            this.$store.state.responsavel = obj.user;
        }
    },
    props: ['dados', 'titulos', 'concluir', 'visualizar', 'atualizar', 'remover']
}
</script>
