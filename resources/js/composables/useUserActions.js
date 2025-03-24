import { ref } from 'vue';

export function useUserActions(setShowEditModal, setSelectedUser) {
  const menuItems = ref([
    {
      label: 'View History',
      icon: 'pi pi-history',
      command: (event) => viewHistory(event.item.instance),
    },
    {
      label: 'Edit Role',
      icon: 'pi pi-user-edit',
      command: (event) => editRole(event.item.instance),
    },
    {
      label: 'Delete',
      icon: 'pi pi-trash',
      command: (event) => deleteUser(event.item.instance),
    },
  ]);

  const viewHistory = (user) => {
    alert(`Viewing history for: ${user.name}`);
  };

  const editRole = (user) => {
    setSelectedUser(user);
    setShowEditModal(true);
  };

  const deleteUser = async (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
      try {
        await axios.delete(`/api/admin/delete/${user.id}`);
        return true;
      } catch (error) {
        console.error('Error deleting user:', error);
        return false;
      }
    }
  };

  const toggleMenu = (event, user, menuRef) => {
    console.log('Toggling menu', event, user, menuRef);
    menuRef.toggle(event);
    menuItems.value.forEach(item => item.instance = user);
  };

  return {
    menuItems,
    toggleMenu,
    viewHistory,
    editRole,
    deleteUser,
  };
} 