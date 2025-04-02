import { reactive } from 'vue';
import CryptoJS from "crypto-js";

export const commonStore = reactive({
    loading: false,
});

export function getUserName(): string {
    const encryptedName = localStorage.getItem("name");
    if (encryptedName) {
        const nameBytes = CryptoJS.AES.decrypt(encryptedName, "XD");

        return nameBytes.toString(CryptoJS.enc.Utf8);
    }
}

export function getUserRole(): string {
    const encryptedRole = localStorage.getItem("userRole");
    if (encryptedRole) {
        const roleBytes = CryptoJS.AES.decrypt(encryptedRole, "XD");

        return roleBytes.toString(CryptoJS.enc.Utf8);
    }
}
