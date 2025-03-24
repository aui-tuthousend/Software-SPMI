import { ref, reactive } from 'vue';

export function useUserManagement() {
  const users = ref([]);
  const showModal = ref(false);
  const submitted = ref(false);
  const menu = ref();

  const newUser = reactive({
    email: '',
    name: '',
    role: '',
    password: '',
  });

  const roles = [
    { name: 'Evaluasi', value: 'Evaluasi' },
    { name: 'Pelaksanaan', value: 'Pelaksanaan' },
    { name: 'Peningkatan', value: 'Peningkatan' },
    { name: 'Pengendalian', value: 'Pengendalian' }
  ];

  const showEditModal = ref(false);
  const selectedUser = ref(null);
  const editSubmitted = ref(false);

  const fetchUsers = async () => {
    try {
      const response = await fetch('/api/admin/listuser');
      const data = await response.json();
      users.value = data;
    } catch (error) {
      console.error('Failed to fetch users:', error);
    }
  };

  const registerUser = async () => {
    submitted.value = true;

    if (newUser.email && newUser.name && newUser.role && newUser.password) {
      try {
        const response = await axios.post('/api/admin/registerUser', {
          name: newUser.name,
          email: newUser.email,
          password: newUser.password,
          role: newUser.role,
        });
        console.log('User registered:', response.data);
        showModal.value = false;
        await fetchUsers();
        resetForm();
      } catch (error) {
        console.error('Error registering user:', error.response.data);
      }
    }
  };

  const resetForm = () => {
    submitted.value = false;
    Object.assign(newUser, {
      email: '',
      name: '',
      role: '',
      password: '',
    });
  };

  const closeModal = () => {
    showModal.value = false;
    resetForm();
  };

  const updateUserRole = async () => {
    editSubmitted.value = true;

    if (selectedUser.value && selectedUser.value.role) {
      try {
        const response = await axios.post('/api/admin/edit/role', {
          user_id: selectedUser.value.id,
          new_role: selectedUser.value.role
        });
        console.log('Role updated:', response.data);
        showEditModal.value = false;
        await fetchUsers();
        editSubmitted.value = false;
      } catch (error) {
        console.error('Error updating role:', error.response?.data);
      }
    }
  };

  return {
    users,
    showModal,
    submitted,
    menu,
    newUser,
    roles,
    fetchUsers,
    registerUser,
    closeModal,
    resetForm,
    showEditModal,
    selectedUser,
    editSubmitted,
    updateUserRole,
  };
} 