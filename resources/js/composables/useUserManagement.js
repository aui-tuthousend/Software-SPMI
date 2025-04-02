import { ref, reactive } from 'vue';
import { useToast } from 'primevue';

export function useUserManagement() {
  const users = ref([]);
  const showModal = ref(false);
  const submitted = ref(false);
  const menu = ref();
  const toast = useToast();

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
    { name: 'Pengendalian', value: 'Pengendalian' },
    { name: 'Admin', value: 'Admin' }
  ];

  const showEditModal = ref(false);
  const selectedUser = ref(null);
  const editSubmitted = ref(false);

  const showResetModal = ref(false);
  const newPassword = ref('');
  const resetSubmitted = ref(false);

  // User history related states
  const showHistoryModal = ref(false);
  const userHistory = ref([]);
  const isLoadingHistory = ref(false);
  const historyError = ref(null);

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
        toast.add({
          severity: 'success',
          summary: 'Success',
          detail: 'User registered successfully',
          life: 3000,
        });
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
        toast.add({
          severity: 'success',
          summary: 'Success',
          detail: 'Role updated successfully',
          life: 3000,
        });
        showEditModal.value = false;
        await fetchUsers();
        editSubmitted.value = false;
      } catch (error) {
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to update role',
          life: 3000,
        });
      }
    }
  };

  const resetUserPassword = async () => {
    resetSubmitted.value = true;

    if (selectedUser.value && newPassword.value) {
      try {
        const response = await axios.post('/api/admin/reset-password', {
          user_id: selectedUser.value.id,
          new_password: newPassword.value
        });

        toast.add({
          severity: 'success',
          summary: 'Success',
          detail: 'Password reset successfully',
          life: 3000,
        });
        showResetModal.value = false;
        newPassword.value = '';
        resetSubmitted.value = false;
      } catch (error) {
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to reset password',
          life: 3000,
        });
      }
    }
  };

  const fetchUserHistory = async (user) => {
    isLoadingHistory.value = true;
    historyError.value = null;
    try {
      const response = await axios.post('/api/api-logs-user', {
        username: user.name
      });
      userHistory.value = response.data;
    } catch (error) {
      console.error('Error getting user history:', error);
      historyError.value = error.response?.data?.message || 'Failed to fetch user history';
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Failed to fetch user history',
        life: 3000,
      });
    } finally {
      isLoadingHistory.value = false;
    }
  };
  
  const viewUserHistory = async (user) => {
    selectedUser.value = user;
    await fetchUserHistory(user);
    showHistoryModal.value = true;
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
    showResetModal,
    newPassword,
    resetSubmitted,
    resetUserPassword,
    // User history related returns
    showHistoryModal,
    userHistory,
    isLoadingHistory,
    historyError,
    viewUserHistory,
    fetchUserHistory
  };
} 