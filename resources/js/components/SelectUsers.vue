<template>
    <div class="dropdown">
        <button class="btn border border-secondary dropdown-toggle" type="button" id="dropdownUsuarios"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-users"></i> Selecionar usuários
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownUsuarios">
            <li v-for="user in users" :key="user.id">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" :id="'user_' + user.id" :value="user.id"
                        :checked="isUserOldAssigned(user.id)" @change="toggleUsuario(user.id, $event.target.checked)">
                    <label class="form-check-label" :for="'user_' + user.id">
                        <i v-if="user.role == 'admin'" class="fa-solid fa-shield-halved"></i>
                        <i v-else class="fa-solid fa-user"></i>                        
                        {{ user.name }}
                    </label>

                </div>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['users', 'assignedUsersIds'],
    methods: {
        isUserOldAssigned(id) {
            return this.assignedUsersIds.includes(id);
        },
        toggleUsuario(id, isChecked) {
            if (isChecked) {
                this.$store.commit('addUsuarioAtribuido', id);
            } else {
                this.$store.commit('removeUsuarioAtribuido', id);
            }
        }
    }
};
</script>
